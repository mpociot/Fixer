<?php

/*
 * This file is part of StyleCI.
 *
 * (c) Alt Three Services Limited
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace StyleCI\Fixer;

use Illuminate\Support\ServiceProvider;
use Symfony\CS\Fixer;
use Symfony\CS\Linter\Linter;

/**
 * This is the fixer service provider class.
 *
 * @author Graham Campbell <graham@alt-three.com>
 */
class FixerServiceProvider extends ServiceProvider
{
    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->registerConfigResolver();
        $this->registerAnalyzer();
        $this->registerConfigTester();
        $this->registerDiffApplier();
        $this->registerReportBuilder();
    }

    /**
     * Register the config resolver class.
     *
     * @return void
     */
    protected function registerConfigResolver()
    {
        $this->app->singleton('fixer.resolver', function ($app) {
            $factory = $app['config.factory'];

            return new ConfigResolver($factory);
        });

        $this->app->alias('fixer.resolver', ConfigResolver::class);
    }

    /**
     * Register the analyzer class.
     *
     * @return void
     */
    protected function registerAnalyzer()
    {
        $this->app->bind('fixer.analyzer', function ($app) {
            $config = $app['fixer.resolver'];
            $fixer = new Fixer();
            $linter = new Linter();

            return new Analyzer($config, $fixer, $linter);
        });

        $this->app->alias('fixer.analyzer', Analyzer::class);
    }

    /**
     * Register the config tester class.
     *
     * @return void
     */
    protected function registerConfigTester()
    {
        $this->app->singleton('fixer.tester', function ($app) {
            $analyzer = function () use ($app) {
                return $app['fixer.analyzer'];
            };
            $path = $app['path.storage'];

            return new ConfigTester($analyzer, $path);
        });

        $this->app->alias('fixer.tester', ConfigTester::class);
    }

    /**
     * Register the diff applier class.
     *
     * @return void
     */
    protected function registerDiffApplier()
    {
        $this->app->singleton('fixer.applier', function ($app) {
            $factory = $app['git.factory'];
            $path = $app['path.storage'];

            return new DiffApplier($factory, $path, 1000);
        });

        $this->app->alias('fixer.applier', DiffApplier::class);
    }

    /**
     * Register the report builder class.
     *
     * @return void
     */
    protected function registerReportBuilder()
    {
        $this->app->singleton('fixer.builder', function ($app) {
            $factory = $app['git.factory'];
            $analyzer = function () use ($app) {
                return $app['fixer.analyzer'];
            };
            $cache = $app['cache.resolver'];
            $path = $app['path.storage'];

            return new ReportBuilder($factory, $analyzer, $cache, $path, 1000);
        });

        $this->app->alias('fixer.builder', ReportBuilder::class);
    }

    /**
     * Get the services provided by the provider.
     *
     * @return string[]
     */
    public function provides()
    {
        return [
            'fixer.analyzer',
            'fixer.applier',
            'fixer.builder',
            'fixer.resolver',
            'fixer.tester',
        ];
    }
}
