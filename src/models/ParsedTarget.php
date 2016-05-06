<?php 

namespace Mosaic\Generator;
use Illuminate\Database\Eloquent\Model;

class ParsedTarget extends Model {

  /**
   * The database table used by the model.
   *
   * @var string
   */
  protected $table = 'mosaic_generator_parsed_target';
}