<?php

namespace App\Providers;

use App\LogHandlers\ESHandler;
use Elasticsearch\ClientBuilder;
use Illuminate\Support\ServiceProvider;
use Monolog\Formatter\NormalizerFormatter;
use Monolog\Processor\WebProcessor;

class CustomLogProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        $monolog = $this->app['log']->getMonolog();

        $esClient = ClientBuilder::create()->build();

        $esHandler = new ESHandler($esClient);
        $esHandler->setFormatter(new NormalizerFormatter('Y-m-d\TH:i:s.uP'));
        $esHandler->pushProcessor(new WebProcessor);
        $esHandler->pushProcessor(function (array $record = []) {
            $record['extra']['app_name'] = config('app.name');
            $record['extra']['app_version'] = env('APP_VERSION', -1);
            $record['extra']['fingerprint'] = request()->fingerprint();
            return $record;
        });

        $monolog->pushHandler($esHandler);
    }
}
