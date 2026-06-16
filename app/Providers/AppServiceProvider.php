<?php

namespace App\Providers;

use App\Services\WhatsApp\Contracts\WhatsAppProvider;
use App\Services\WhatsApp\Providers\WwebjsProvider;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(WhatsAppProvider::class, function () {
            $driver = config('whatsapp.driver');

            return match ($driver) {
                'wwebjs' => new WwebjsProvider(),
                default  => throw new \RuntimeException("WhatsApp driver [{$driver}] no soportado."),
            };
        });
    }

    public function boot(): void
    {
        //
    }
}
