<?php namespace Sorora\Omni\Facades;

use Illuminate\Support\Facades\Facade;

class Omni extends Facade {

    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor() { return 'omni'; }

}