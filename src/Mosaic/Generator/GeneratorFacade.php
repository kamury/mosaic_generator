<?php
/**
 * Mosaic Facade
 */

namespace Mosaic\Generator;

use Illuminate\Support\Facades\Facade;

/**
 * Facade for the AWS service
 *
 * @method static AwsClientInterface get($name, $throwAway = false) Get a client from the service builder
 */
class GeneratorFacade extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'MosaicGenerator';
    }
}
