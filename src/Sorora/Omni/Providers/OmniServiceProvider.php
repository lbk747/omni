<?php namespace Sorora\Omni\Providers;

use Illuminate\Support\ServiceProvider;

class OmniServiceProvider extends ServiceProvider {

	/**
	 * Indicates if loading of the provider is deferred.
	 *
	 * @var bool
	 */
	protected $defer = false;

	protected $profiler = true;

	/**
	 * Bootstrap the application events.
	 *
	 * @return void
	 */
	public function boot()
	{
		$this->package('sorora/omni');
	}

	/**
	 * Register the service provider.
	 *
	 * @return void
	 */
	public function register()
	{
		$this->shareWithApp();
		$this->registerAlias();
		$this->loadConfig();
		$this->registerViews();
		$this->profiler = (!$this->app->runningInConsole()) ? $this->app['config']->get('omni::profiler') : false;
		if($this->profiler)
		{
			$this->activateTimers();
			$this->app['events']->listen('composing:*', function ($data)
			{
				\Omni::setViewData($data->getData());
			});
			$this->app['events']->listen('illuminate.log', function ($type, $message)
			{
				\Omni::addLog($type, $message);
				if(is_object($message) and stripos(get_class($message), 'exception') !== false)
				{
    				\Omni::setTimer('__end');
					\Omni::outputData();
				}
			});
		}
	}

	/**
	 * Share the package with application
	 *
	 * @return void
	 */
	protected function shareWithApp()
	{
		$this->app['omni'] = $this->app->share(function($app)
		{
			return new \Sorora\Omni\Omni;
		});
	}

	/**
	 * Register the alias for package.
	 *
	 * @return void
	 */
	protected function registerAlias()
	{
		$this->app->booting(function()
		{
			$loader = \Illuminate\Foundation\AliasLoader::getInstance();
			$loader->alias('Omni', 'Sorora\Omni\Facades\Omni');
		});
	}

	/**
	 * Load the config for the package
	 *
	 * @return void
	 */
	protected function loadConfig()
	{
		$this->app['config']->package('sorora/omni', __DIR__.'/../../../config');
	}

	/**
	 * Register views
	 *
	 * @return void
	 */
	protected function registerViews()
	{
   		$this->app['view']->addNamespace('omni', __DIR__.'/../../../views');
	}

	/**
	 * Activates the timers on events
	 *
	 * @return void
	 */
	protected function activateTimers()
	{
   		$this->app->booting(function () {
    		\Omni::setTimer('__start');
		});
		$this->app->shutdown(function () {
    		\Omni::setTimer('__end');
    		\Omni::outputData();
		});
	}

	/**
	 * Get the services provided by the provider.
	 *
	 * @return array
	 */
	public function provides()
	{
		return array('omni');
	}

}