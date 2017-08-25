<?php 

namespace Mosaic\Generator;
use DB, Config, AWS;
use DateTime, DateTimeZone;
use Imagick;
use Exception;

class Generator {

  const SIZE = 1200;
  const COORDINATE_OFFSET = 50;
  private $allowed_target_size = array('1024x768' => '64x48',
                                       '1280x720' => '80x45');
  
  private $tmpFolderBackgroundImages = "uploads/tmp/events_background_upload/";
  private $fontsPath = "-/fonts/proxima.ttf";
  private $expired_interval = 16;

  public function addTarget($event_id, $target_url, $rows, $columns, $print_width, $print_height) {
    //cleaning last target and parsed data
    Target::where('event_id', '=', $event_id)->delete();
    ParsedTarget::where('event_id', '=', $event_id)->delete();
    Thumbnails::where('event_id', '=', $event_id)->delete();
    
    $data = $this->getSizes($target_url, $rows, $columns);
    $data['target_url'] = $target_url;
    $data['event_id'] = $event_id;
    $data['print_width'] = $print_width;
    $data['print_height'] = $print_height;
    Target::insert($data);
  }
  
  public function setPrintSize($event_id, $print_width, $print_height)
  {
    //change if target exists
    try {
      $target = Target::findOrFail($event_id);
      $target->print_width = $print_width;
      $target->print_height = $print_height;
      $target->save();
      return TRUE;
    } catch (Exception $e) {
      return FALSE;
    }
  }
  
  public function getGridSize($img_url)
  { 
    $img = imagecreatefromjpeg($img_url);
    $width = imagesx($img); 
    $height = imagesy($img);
    
    if (isset($this->allowed_target_size["{$width}x{$height}"])) {
      list($cols, $rows) = explode('x', $this->allowed_target_size["{$width}x{$height}"]);
      return array('cols' => $cols, 'rows' => $rows);
    } else {
      return FALSE;
    }
  }

  public function isParsed($event_id) {
    $target = Target::findOrFail($event_id);
    return $target->is_parsed;
  }

  public function targetParse($event_id) {
    //set target not parsed
    $target = Target::findOrFail($event_id);
    $target->is_parsed = 0;
    $target->save();
    ParsedTarget::where('event_id', '=', $event_id)->delete();
    
    $blank_cell = imagecreatetruecolor($target->cell_width, $target->cell_height);
    $img['file']   = imagecreatefromjpeg($target->target_url);
    $img['width']  = imagesx($img['file']);
    $img['height'] = imagesy($img['file']);
    
    // loops through every "cell" (rows/columns)
    for($x = 0; $x < $target->rows; $x++) {
      for($y = 0; $y < $target->columns; $y++) {
        $current_cell = $blank_cell;
        
        //avg image of each tile
        imagecopy($current_cell, $img['file'], 0, 0, $y * $target->cell_width, $x * $target->cell_height, $img['width'], $img['height']);
        /*$current_cell_filename = 'cell_' . time() . '.jpg';
        //save for watermark
        imagejpeg($current_cell, $this->tmpFolderBackgroundImages . $current_cell_filename, 95);
        var_dump($this->tmpFolderBackgroundImages . $current_cell_filename); exit;
        $current_cell_url = $this->uploadFileOnAws($this->tmpFolderBackgroundImages . $current_cell_filename, $current_cell_filename, $event_id);
        unlink($this->tmpFolderBackgroundImages . $current_cell_filename);
        */
        $data = $this->getAvgColor($current_cell);
        $data['event_id'] = $event_id;
        $data['x'] = $x;
        $data['y'] = $y;
        //$data['cell_url'] = $current_cell_url;
        
        ParsedTarget::insert($data);                        
      }
    }
    
    $target->is_parsed = 1;
    $target->save();
  }

  public function addImgToMosaic($event_id, $image_url, $mediaId = null, $animate, $watermark_depth) {
    $img = imagecreatefromjpeg($image_url);
    $img_color = $this->getAvgColor($img);
    
    //x+-100, y+-100
    //но важно добить последние
    
    //get last coordinates
    $last_thumb = Thumbnails::select()->where('event_id', '=', $event_id)->orderBy('created_at', 'desc')->first();
    
    //чтобы координваты были не рядом
    $coordinates = ParsedTarget::select('*', DB::raw("abs(red - {$img_color['red']}) + abs(green - {$img_color['green']}) + abs(blue - {$img_color['blue']}) as diff"))->
    where('is_filled', '=', 0)->where('event_id', '=', $event_id)->
    whereNotBetween('x', array($last_thumb->x-self::COORDINATE_OFFSET, $last_thumb->x+self::COORDINATE_OFFSET))->
    whereNotBetween('y', array($last_thumb->y-self::COORDINATE_OFFSET, $last_thumb->y+self::COORDINATE_OFFSET))->orderBy('diff', 'asc')->first();
    
      
    if (!$coordinates) {
      $coordinates = ParsedTarget::select('*', DB::raw("abs(red - {$img_color['red']}) + abs(green - {$img_color['green']}) + abs(blue - {$img_color['blue']}) as diff"))->
      where('is_filled', '=', 0)->where('event_id', '=', $event_id)->orderBy('diff', 'asc')->first();
      
      //FIXME make finish (all is_filled=1)
      if (!$coordinates) {
        //FIXME ecxeption not found
        throw new Exception('No target data in db. Please (re)parse target.');
      } 
    }
    
    /*$coordinates = ParsedTarget::select('*', DB::raw("abs(red - {$img_color['red']}) + abs(green - {$img_color['green']}) + abs(blue - {$img_color['blue']}) as diff"))->
    where('is_filled', '=', 0)->where('event_id', '=', $event_id)->orderBy('diff', 'asc')->first();
      
    if (!$coordinates) {
      
        //FIXME ecxeption not found
        throw new Exception('No target data in db. Please (re)parse target.');
       
    }*/
    
    $this->setFiled($coordinates->id);                  
    $target = Target::findOrFail($event_id);
    $now = time();
    $filename = md5($image_url.$now).'.jpg';
    
    //put mask on the image
    $img = $this->setTransparentMask($img, $coordinates->red, $coordinates->green, $coordinates->blue, $watermark_depth);
    //save
    imagejpeg($img, public_path($this->tmpFolderBackgroundImages . $filename), 95);
    $masked_image_url = $this->uploadFileOnAws(public_path($this->tmpFolderBackgroundImages . $filename), $filename, $event_id);
    unlink(public_path($this->tmpFolderBackgroundImages . $filename));
    
    $processedImg = $this->processImage($img, $coordinates->x, $coordinates->y, $target->print_width, $target->print_height);
    $processed_filename = 'processed-' . $filename;
    imagejpeg($processedImg, public_path($this->tmpFolderBackgroundImages . $processed_filename), 95);
    $processed_image_url = $this->uploadFileOnAws(public_path($this->tmpFolderBackgroundImages . $processed_filename), $processed_filename, $event_id);
    unlink(public_path($this->tmpFolderBackgroundImages . $processed_filename));
    
    //create thumb from img
    $thumb = imagecreatetruecolor($target->cell_width, $target->cell_height);
    //resize img
    imagecopyresampled($thumb, $img, 0, 0, 0, 0, $target->cell_width, $target->cell_height, imagesx($img), imagesy($img));
    //put mask on the image
    //$thumb = setTransparentMask($img, $coordinates->red, $coordinates->green, $coordinates->blue);
    //save thumb
    $thumb_filename = 'thumb-' . $filename;
    imagejpeg($thumb, public_path($this->tmpFolderBackgroundImages . $thumb_filename), 95);
    $thumb_url = $this->uploadFileOnAws(public_path($this->tmpFolderBackgroundImages . $thumb_filename), $thumb_filename, $event_id, true);
    unlink(public_path($this->tmpFolderBackgroundImages . $thumb_filename));
    
    //save thumb in database
    /*$thumb = new Thumbnails();
    $thumb->event_id = $event_id;
    $thumb->thumb_url = $thumb_url;
    $thumb->processed_image_url = $processed_image_url;
    $thumb->masked_image_url = $masked_image_url;
    $thumb->original_image_url = $image_url;
    $thumb->red = $img_color['red'];
    $thumb->green = $img_color['green'];
    $thumb->blue = $img_color['blue'];
    $thumb->x = $coordinates->x;
    $thumb->y = $coordinates->y;
    $thumb->save();*/
    
    $thumb_data = array(
      'instagram_image_id' => $mediaId,
      'event_id' => $event_id,
      'thumb_url' => $thumb_url,
      'processed_image_url' => $processed_image_url,
      'masked_image_url' =>$masked_image_url,
      'original_image_url' =>$image_url,
      'red' =>$img_color['red'],
      'green' =>$img_color['green'],
      'blue' =>$img_color['blue'],
      'x' =>$coordinates->x,
      'y' =>$coordinates->y);
    
    $thumb = Thumbnails::create($thumb_data);
    
    //regenerate mosaic
    $mosaic = $this->generate($event_id);
    $mosaic_filename = 'mosaic-' . $filename;
    //save current mosaic
    imagejpeg($mosaic, public_path($this->tmpFolderBackgroundImages . $mosaic_filename), 95);
    $current_mosaic_url = $this->uploadFileOnAws(public_path($this->tmpFolderBackgroundImages . $mosaic_filename), $mosaic_filename, $event_id, true);
    unlink(public_path($this->tmpFolderBackgroundImages . $mosaic_filename));
    
    if ($animate) {
      //mosaic has genered, image ready to be shown, set expired to show
      $thumb->expired_at = $this->getExpired($event_id);;
    } else {
      // FIXME [13.08.2016]: format should be in constant? or we should pass $animate to getExpired?
      $thumb->expired_at = date('Y-m-d H:i:s', strtotime('-1 day'));
    }
    
    $thumb->current_mosaic_url = $current_mosaic_url;
    $thumb->update();
    
    return $thumb->id;
  }

  private function setTransparentMask($img, $red, $green, $blue, $watermark_depth) {
    $width = imagesx($img); 
    $height = imagesy($img);
    
    $tweak = imagecreatetruecolor($width, $height);
    $color_resource = imagecolorallocate($tweak, $red, $green, $blue);
    imagefill($tweak, 0, 0, $color_resource);
    imagecopymerge($tweak, $img, 0, 0, 0, 0, $width, $height, $watermark_depth);
    return $tweak;
  }

  public function getCurrentImage($event_id, $showed_ts) {
    $now = date('Y-m-d H:i:s');
    $showed_dt = DateTime::createFromFormat('U', $showed_ts);
    $showed_dt->setTimezone(new DateTimeZone(Config::get('app.timezone')));
    //var_dump($showed_dt->format('Y-m-d H:i:s'));
    $current_thumb = Thumbnails::where('event_id', '=', $event_id)->
      where('expired_at', '>', $now)->
      where('expired_at', '>', $showed_dt->format('Y-m-d H:i:s'))->
      orderBy('expired_at', 'asc')->first();
      //orderBy('expired_at', 'desc')->first();
      
    $target = Target::findOrFail($event_id);
    $data['height'] = $target->cell_height;
    $data['width'] = $target->cell_width;
    $data['rows'] = $target->rows;
    $data['columns'] = $target->columns;

    if ($current_thumb) {
      $date = DateTime::createFromFormat('Y-m-d H:i:s', $current_thumb->expired_at);
      $last_ts = $date->getTimestamp();
      
      $data['x'] = $current_thumb->y;
      $data['y'] = $current_thumb->x;
      $data['url'] = $current_thumb->original_image_url;
      $data['mosaic_url'] = $current_thumb->current_mosaic_url;
      $data['ts'] = $last_ts;
      return $data; 
    } else {
      $last_thumb = Thumbnails::where('event_id', '=', $event_id)->
      orderBy('expired_at', 'desc')->first();
      
      if ($last_thumb) {
        $data['mosaic_url'] = $last_thumb->current_mosaic_url;
        return $data;
      } else {
        return array();
      }
    }
  }

  private function processImage($img, $x, $y, $print_width, $print_height) {
    $width = imagesx($img); 
    $height = imagesy($img);
    
    $new_width = $print_width;
    $new_height = $print_height;
    
    $new = imagecreatetruecolor($new_width, $new_height);
    $white = imagecolorallocate($new, 255, 255, 255);
    $black = imagecolorallocate($new, 0, 0, 0);
    imagefill($new, 0, 0, $white);
    
    //imagecopy($new, $img, 0, 0, 0, 0, $width, $height);
    
    
    imagecopyresized($new, $img, 0, 0, 0, 0, $new_width, $new_width, $width, $height);
    
    imagettftext($new, 14, 0, 10, 400, $black, public_path($this->fontsPath), "{$x}, {$y}");
    
    //rotate for printer
    $new = imagerotate($new, 180, $white);    
    
    return $new;
  }

  private function generate($event_id)
  {
    $target = Target::findOrFail($event_id);
    
    $mosaic = imagecreatefromjpeg($target->target_url);
    $data = Thumbnails::where('event_id', '=', $event_id)->orderBy('x', 'asc')->orderBy('y', 'asc')->get(); 
    
    $thumbs = array();
    foreach ($data as $row) {
      $thumbs[$row->x][$row->y] = $row->thumb_url;
    }

    $blank_cell = imagecreatetruecolor($target->cell_width, $target->cell_height);
    $blank_row  = imagecreatetruecolor($target->cell_width * $target->columns, $target->cell_height);

    for ($x = 0; $x < $target->rows; $x++) {
      $row = $blank_row;
      for ($y = 0; $y < $target->columns; $y++) {
        $current_cell = $blank_cell;
          
        if (isset($thumbs[$x][$y])) {
          $current_cell = imagecreatefromjpeg('http:' . $thumbs[$x][$y]); 
        }
        
        imagecopy($row, $current_cell, $y * $target->cell_width, 0, 0, 0, $target->cell_width, $target->cell_height);  
      }
      imagecopy($mosaic, $row, 0, $x * $target->cell_height, 0, 0, $target->cell_width * $target->columns,  $target->cell_height); 
    }
    
    /*imagejpeg($mosaic, $this->res_file, 99);
    unlink($this->tmpFolderBackgroundImages . $output_filename);
    copy($this->res_file, $this->tmpFolderBackgroundImages . $output_filename);*/
    
    return $mosaic;
  }
  
  public function getExpired($event_id) {
    $last = Thumbnails::select(DB::raw("MAX(expired_at) as expired"))->
    where('event_id', '=', $event_id)->first();
                             
    $last_expired = new DateTime($last->expired);
    $new_expired = new DateTime('now');
    
    if ($last_expired > $new_expired) {
      $new_expired = $last_expired;
    }
    
    $new_expired->modify("+{$this->expired_interval} seconds");
    
    return $new_expired->format('Y-m-d H:i:s');
  }

  private function getSizes($url, $rows, $columns) {
    $img['file']    = imagecreatefromjpeg($url);
    $img['width']  = imagesx($img['file']);
    $img['height'] = imagesy($img['file']);
    
    $sizes['columns'] = $columns;
    if ($img['width'] % $columns) {
      $sizes['columns'] = $this->getMultiple($columns, $img['width']);
    }

    $sizes['rows'] = $rows;
    if ($img['height'] % $rows) {
      $sizes['rows'] = $this->getMultiple($rows, $img['height']);
    }      
    
    $sizes['cell_width'] = $img['width']  / $sizes['columns'];
    $sizes['cell_height'] = $img['height'] / $sizes['rows'];
    
    return $sizes;
  }

  private function setFiled($id) {
    $parsed = ParsedTarget::findOrFail($id);
    $parsed->is_filled = 1;
    $parsed->save();
  }

  //get closest multiple
  private function getMultiple($to_multiple, $dividend)
  {
    $multiple = $dividend;
    
    for ($i=$to_multiple; $i < $dividend; $i++) { 
      if ($dividend % $i == 0) {
        $multiple = $i;
        break;
      }
    }
    
    return $multiple; 
  }
    
  // determines average color of an image
  private function getAvgColor($img) {
      $w = imagesx($img);
      $h = imagesy($img);
      
      $r = $g = $b = 0;

      for($y=0; $y<$h; $y++) {
          for($x=0; $x<$w; $x++) {
              $rgb = imagecolorat($img, $x, $y);
              $r += $rgb >> 16;
              $g += $rgb >> 8 & 255;
              $b += $rgb & 255;
          }
      }
      
      $pxls = $w * $h;
      
      return array(
        'red' => round($r / $pxls),
        'green' => round($g / $pxls),
        'blue' => round($b / $pxls)
      );
  }
  
  /**
   * Upload a file on aws
   *
   * @param string $srcFilePath the path to the file
   * @param string $dstFileName the filename
   * @param integer $eventId
   * @param bool $isTmp save to tmp folder or no
   * @return void
   */
   private function uploadFileOnAws($srcFilePath, $dstFileName, $eventId, $isTmp = false){
    
    if ($isTmp) {
      $amazonFilePath = Config::get('amazonBucket.mosaic') . "{$eventId}/tmp/{$dstFileName}";  
    } else {
      $amazonFilePath = Config::get('amazonBucket.mosaic') . "{$eventId}/{$dstFileName}";
    }
    
    
    $s3 = AWS::get('s3');
    $s3->setRegion(Config::get('amazonBucket.region'));
    $s3->putObject(array(
        'Bucket'     => Config::get('amazonBucket.bucket'),
        'Key'        => $amazonFilePath,
        'SourceFile' => $srcFilePath,
         'ACL'        => 'public-read',
    ));
    
    
    return Config::get('amazonBucket.endpoint') . $amazonFilePath;
  }
   
  public function resize($img_url, $save_path = '')
  {
    try {
      $exif = @exif_read_data($img_url);
      $img = imagecreatefromjpeg($img_url);
      $width = imagesx($img); 
      $height = imagesy($img);  
    } catch (Exception $e) {
      return FALSE;
    }
    
    if ($width != $height) {
      if ($width > $height) {
        $size = $height;
        $new = imagecreatetruecolor($size, $size);
        $delta = floor(($width - $height) / 2);
        imagecopyresized($new, $img, 0, 0, $delta, 0, $size, $size, $size, $size); 
      } else {
        $delta = floor(($height - $width) / 2);
        $size = $width;
        $new = imagecreatetruecolor($size, $size);
        imagecopyresized($new, $img, 0, 0, 0, $delta, $size, $size, $size, $size);
      }
      
      $img = $new;
    } else {
      $size = $height;
    }
    
    //rotate ios photo
    if (isset($exif['Orientation'])) {
      if ($exif['Orientation'] == 6) {
        $img = imagerotate($img, 270, 0);
      }
    
      if ($exif['Orientation'] == 8) {
        $img = imagerotate($img, 90, 0);
      }
      
      if ($exif['Orientation'] == 3) {
        $img = imagerotate($img, 180, 0);
      }   
    
      
    }
    
    if ($size > self::SIZE) {
        
      /*if ($width > self::SIZE) {
        $new_width = self::SIZE;
        $new_height = floor($height / ($width/self::SIZE));
      }
      
      if ($new_height > self::SIZE) {
        $new_width = floor($new_width / ($new_height/self::SIZE));
        $new_height = self::SIZE;
      }*/
       
      $new = imagecreatetruecolor(self::SIZE, self::SIZE);
      imagecopyresized($new, $img, 0, 0, 0, 0, self::SIZE, self::SIZE, $size, $size);
      $img = $new;
    }
    
    if ($save_path) {
      if ($img_url == $save_path) {
        unlink($img_url);
      }
      imagejpeg($img, $save_path, 95);
      return TRUE;
    } else {
      return $img; 
    }
  }
}