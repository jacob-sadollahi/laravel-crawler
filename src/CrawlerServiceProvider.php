<?php

namespace Hamrahnegar\Crawler;

use Illuminate\Support\ServiceProvider;

use Hamrahnegar\Crawler\App\Middleware\CrawlerAuth;


class CrawlerServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        // load route
        $this->loadRoutesFrom(__DIR__ . '/routes/web.php');

        // load views
        $this->loadViewsFrom(__DIR__.'/resources/views', 'crawler');

        // load migration  
        $this->loadMigrationsFrom(__DIR__.'/database/migrations');

        // load translation
        $this->loadTranslationsFrom(__DIR__.'/resources/lang', 'crawler');

    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        //config file
        $this->mergeConfigFrom(
            __DIR__.'/config/crawler.php', 'crawler'
        );

        $this->registerCrawler();

        $this->app->alias('crawler', Crawler::class);

    }


    protected function registerCrawler()
    {
        $this->app->singleton('crawler', function ($app) {
            //dd($app['request']);
            return new Crawler();
        });
    }
}
