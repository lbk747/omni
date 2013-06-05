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
		$this->activateProfiler();
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
			return new \Sorora\Omni\Omni(
				new \Sorora\Omni\Loggers\Time
			);
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
	 * Activates the profiler
	 *
	 * @return void
	 */
	protected function activateProfiler()
	{
		// Check console isn't running and profiler is enabled
		$this->profiler = (!$this->app->runningInConsole()) ? $this->app['config']->get('omni::profiler') : false;

		if($this->profiler)
		{
			// Listen to shutdown
			$this->shutdownListener();
			$this->listenViewComposing();
			$this->listenLogs();
		}
	}

	/**
	 * Output data on shutdown
	 *
	 * @return void
	 */
	protected function shutdownListener()
	{
		$this->app->shutdown(function () {
    		\Omni::outputData();
		});
	}

	/**
	 * Listen to view composing events
	 *
	 * @return void
	 */
	protected function listenViewComposing()
	{
		$this->app['events']->listen('composing:*', function ($data)
		{
			\Omni::setViewData($data->getData());
		});
	}

	/**
	 * Listen to logging events
	 *
	 * @return void
	 */
	protected function listenLogs()
	{
		$this->app['events']->listen('illuminate.log', function ($type, $message)
		{
			\Omni::addLog($type, $message);
			$this->toExit($message);
		});
	}

	/**
	 * Determine whether to output now if exception
	 *
	 * @return void
	 */
	protected function toExit($message)
	{
		// Determine whether to output data now if it is exception
		if(is_object($message) and stripos(get_class($message), 'exception') !== false)
		{
			\Omni::outputData();
		}
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