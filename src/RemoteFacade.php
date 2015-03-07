<?php namespace Collective\Remote;

use Illuminate\Support\Facades\Facade;

/**
 * @see \Collective\Remote\RemoteManager
 * @see \Collective\Remote\Connection
 */
class RemoteFacade extends Facade {

	/**
	 * Get the registered name of the component.
	 *
	 * @return string
	 */
	protected static function getFacadeAccessor() {
		return 'remote';
	}
}