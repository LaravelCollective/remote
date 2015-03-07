<?php namespace Collective\Remote;

use Illuminate\Support\ServiceProvider;

class RemoteServiceProvider extends ServiceProvider {

	/**
	 * Indicates if loading of the provider is deferred.
	 *
	 * @var bool
	 */
	protected $defer = true;

	public function boot()
	{
		$this->publishes([
			__DIR__.'/../config/remote.php' => config_path('remote.php'),
		]);
	}
	/**
	 * Register the service provider.
	 *
	 * @return void
	 */
	public function register() {
		$this->app->bindShared( 'remote', function ( $app ) {
			return new RemoteManager( $app );
		} );
	}

	/**
	 * Get the services provided by the provider.
	 *
	 * @return array
	 */
	public function provides() {
		return [ 'remote' ];
	}
}
