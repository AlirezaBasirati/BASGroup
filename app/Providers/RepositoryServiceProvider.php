<?php

namespace App\Providers;

use App\Repositories\Contracts\MessageRepositoryInterface;
use Illuminate\Support\ServiceProvider;
use App\Repositories\Eloquent\MessageRepository;

class RepositoryServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->bind(MessageRepositoryInterface::class, MessageRepository::class);
    }

    public function boot()
    {
        //
    }
}
