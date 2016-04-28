<?php 

namespace Mosaic\Generator;
use Illuminate\Database\Eloquent\Model;

class Target extends Model {

  /**
   * The database table used by the model.
   *
   * @var string
   */
  protected $table = 'mosaic_generator_target';
  protected $primaryKey = 'event_id';
  public $incrementing = false;
}