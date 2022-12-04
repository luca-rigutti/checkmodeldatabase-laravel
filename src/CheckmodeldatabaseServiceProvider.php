<?php

namespace LucaRigutti\CheckmodeldatabaseLaravel;

use Illuminate\Support\ServiceProvider;
use LucaRigutti\CheckmodeldatabaseLaravel\Commands\CheckDatabase;

class CheckmodeldatabaseServiceProvider extends ServiceProvider
{

    public function boot()
    {
        if ($this->app->runningInConsole()) {
            $this->commands([
                CheckDatabase::class,
            ]);
        }
    }

    public function registry()
    {

    }
}