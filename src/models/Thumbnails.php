<?php

namespace Mosaic\Generator;
use Illuminate\Database\Eloquent\Model;

class Thumbnails extends Model {

  /**
   * The database table used by the model.
   *
   * @var string
   */
  protected $table = 'mosaic_generator_thumbnails';
  //protected $primaryKey = 'thumb_url';
  protected $guarded = array('id');
  protected $fillable = array('event_id', 'thumb_url', 'processed_image_url', 'masked_image_url', 'original_image_url', 'red', 'green', 'blue','x','y');
  
}

?>