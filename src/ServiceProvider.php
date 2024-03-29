<?php

namespace Raftx24\Healthy;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Support\ServiceProvider as IlluminateServiceProvider;
use Raftx24\Healthy\Console\Commands\PrinterCheckCommand;
use Raftx24\Healthy\Console\Commands\QueueHealthCommand;
use Raftx24\Healthy\Console\Commands\ScheduleHealthCommand;

class ServiceProvider extends IlluminateServiceProvider
{
    /**
     * The application instance.
     *
     * @var \Illuminate\Foundation\Application
     */
    protected $app;

    /**
     * Resource loader instance.
     *
     * @var
     */
    protected $resourceLoader;

    /**
     * The router.
     *
     * @var
     */
    private $router;

    /**
     * Cache closure.
     *
     * @var
     */

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->mergeConfig();

        $this->registerRoutes();

        $this->registerTasks();

        $this->registerConsoleCommands();
    }


    public function boot()
    {
        $this->publishes([
            __DIR__.'/config/healthy.php' => $this->config_path('healthy.php'),
        ]);
        $schedule = $this->app->make(Schedule::class);
        $schedule->command('check:print')->everyMinute();
    }

    /**
     * Merge configuration.
     */
    private function mergeConfig()
    {
        if (file_exists($this->config_path('/healthy.php'))) {
            $this->mergeConfigFrom($this->config_path('/healthy.php'), 'healthy');
        }
        $this->mergeConfigFrom(__DIR__.'/config/healthy.php', 'healthy');
        $this->addDistPathToConfig();
    }



    /**
     * Register console commands.
     */
    private function registerConsoleCommands()
    {
        $this->commands([
            PrinterCheckCommand::class,
            QueueHealthCommand::class,
            ScheduleHealthCommand::class,
        ]);
    }


    /**
     * Register routes.
     */
    private function registerRoutes()
    {
        app()->router->get('ping/readiness', [
            'uses' => "Raftx24\Healthy\Http\Controllers\HealthController@readiness",
        ]);
        app()->router->get('ping/liveness', [
            'uses' => "Raftx24\Healthy\Http\Controllers\HealthController@liveness",
        ]);
    }



    /**
     * Register scheduled tasks.
     */
    private function registerTasks()
    {
//        $this->app->booted(function () {
//
//        });
    }

    function config_path($path = '')
    {
        return app()->basePath() . '/config' . ($path ? '/' . $path : $path);
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return [
            'raftx24.health.resource.checker',
            'raftx24.health.commands',
        ];
    }

    public function addDistPathToConfig()
    {
        config(['healthy.dist_path' => __DIR__.'/resources/dist']);
    }
}
