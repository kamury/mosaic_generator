<?php 

namespace Mosaic\Generator;
use Illuminate\Support\ServiceProvider;

class GeneratorServiceProvider extends ServiceProvider {

	/**
	 * Indicates if loading of the provider is deferred.
	 *
	 * @var bool
	 */
	protected $defer = false;

	/**
	 * Bootstrap the application events.
	 *
	 * @return void
	 */
	public function boot()
	{
		$this->package('mosaic/generator');
	}

	/**
	 * Register the service provider.
	 *
	 * @return void
	 */
	public function register()
	{
		/*$this->app->booting(function()
      {
        $loader = \Illuminate\Foundation\AliasLoader::getInstance();
        $loader->alias('Mosaic', 'Mosaic\Generator\GeneratorFacade');
      });*/
      
      $this->app->bind('MosaicGenerator', function ($app) {
        return new Generator;
      });
      
      //$this->app->alias('mosaic', 'Mosaic\Generator\Generator');
	}

	/**
	 * Get the services provided by the provider.
	 *
	 * @return array
	 */
	public function provides()
  {
    return array();
  }

}
