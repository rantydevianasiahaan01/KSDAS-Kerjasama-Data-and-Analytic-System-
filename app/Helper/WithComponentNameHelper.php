<?php

namespace App\Helper;

trait WithComponentNameHelper
{
    public static function getComponentName(): string
    {
        return app(\Livewire\Mechanisms\ComponentRegistry::class)->getName(static::class);
    }
}
