<?php

namespace App\Concerns;

use Illuminate\Container\Container;
use Illuminate\Contracts\Events\Dispatcher;

trait EventsAware
{
    protected function getEvents(): Dispatcher
    {
        return Container::getInstance()->make('events');
    }
}
