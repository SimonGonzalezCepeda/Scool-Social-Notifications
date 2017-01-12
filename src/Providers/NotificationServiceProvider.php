<?php
/**
 * Created by PhpStorm.
 * User: sylver
 * Date: 12/01/17
 * Time: 21:27
 */

namespace Scool\Social\Notifications\Providers;


use Illuminate\Support\ServiceProvider;

class NotificationServiceProvider extends ServiceProvider
{
    /**
     * Register package services.
     */
    public function register()
    {
        if (!defined('SCOOL_SOCIAL_NOTIFICATIONS_PATH')) {
            define('SCOOL_SOCIAL_NOTIFICATIONS_PATH', realpath(__DIR__.'/../../'));
        }
        $this->registerNamesServiceProvider();

        $this->registerStatefulEloquentServiceProvider();

        $this->bindRepositories();

        $this->app->bind(StatsRepositoryInterface::class,function() {
            return new CacheableStatsRepository(new StatsRepository());
        });

    }

    /**
     * Bind repositories
     */
    protected function bindRepositories()
    {
//        $this->app->bind(
//            \Scool\Social\Notifications\Repositories\StudyRepository::class,
//            \Scool\Social\Notifications\Repositories\StudyRepositoryEloquent::class);
//        //:end-bindings:
    }

    /**
     * Register acacha/stateful-eloquent Service Provider.
     *
     */
    protected function registerStatefulEloquentServiceProvider()
    {
        $this->app->register(StatefulServiceProvider::class);
    }

    /**
     * Register acacha/names Service Provider.
     *
     */
    protected function registerNamesServiceProvider()
    {
        $this->app->register(NamesServiceProvider::class);
    }

    /**
     * Bootstrap package services.
     *
     * @return void
     */
    public function boot()
    {
        $this->defineRoutes();
        $this->loadMigrations();
        $this->publishFactories();
        $this->publishConfig();
        $this->publishTests();
    }

    /**
     * Define the curriculum routes.
     */
    protected function defineRoutes()
    {
        if (!$this->app->routesAreCached()) {
            $router = app('router');

            $router->group(['namespace' => 'Scool\Social\Notifications\Http\Controllers'], function () {
                require __DIR__.'/../Http/routes.php';
            });

        }
    }

    /**
     * Load migrations.
     */
    private function loadMigrations()
    {
        $this->loadMigrationsFrom(SCOOL_SOCIAL_NOTIFICATIONS_PATH . '/database/migrations');
    }

    /**
     * Publish factories.
     */
    private function publishFactories()
    {
        $this->publishes(
            ScoolCurriculum::factories(),"scool_curriculum"
        );
    }

    /**
     * Publish config.
     */
    private function publishConfig() {
        $this->publishes(
            ScoolCurriculum::configs(),"scool_curriculum"
        );
        $this->mergeConfigFrom(
            SCOOL_SOCIAL_NOTIFICATIONS_PATH. '/config/curriculum.php', 'scool_curriculum'
        );
    }

    /**
     * Publich tests.
     */
    private function publishTests()
    {
        $this->publishes(
            [SCOOL_SOCIAL_NOTIFICATIONS_PATH .'/tests/CurriculumTest.php' => 'tests/CurriculumTest.php'] ,
            'scool_curriculum'
        );
    }
}