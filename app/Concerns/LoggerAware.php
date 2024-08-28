<?php

namespace App\Concerns;

use Illuminate\Container\Container;
use Psr\Log\LoggerInterface;

trait LoggerAware
{
    protected function getLogger(): LoggerInterface
    {
        return Container::getInstance()->make(LoggerInterface::class);
    }
}
