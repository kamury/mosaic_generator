<?php

namespace Mosaic\Generator;
use Illuminate\Database\Eloquent\Model;

class Highres extends Model {

  /**
   * The database table used by the model.
   *
   * @var string
   */
  protected $table = 'mosaic_generator_highres';
  protected $primaryKey = 'event_id';
  protected $fillable = array('event_id', 'url1', 'url2', 'url3', 'url4', 'url');
  
}

?>