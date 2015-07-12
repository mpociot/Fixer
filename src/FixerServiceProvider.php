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
        $this->registerAnalyser();
        $this->registerReportBuilder();
    }

    /**
     * Register the analyser class.
     *
     * @return void
     */
    protected function registerAnalyser()
    {
        $this->app->bind('fixer.analyser', function ($app) {
            $fixer = new Fixer();
            $config = new ConfigResolver($app['config.factory']);
            $linter = new Linter();

            return new Analyser($fixer, $config, $linter);
        });

        $this->app->alias('fixer.analyser', Analyser::class);
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
            $analyser = function () use ($app) {
                return $app['fixer.analyser'];
            };
            $path = $app['path.storage'];

            return new ReportBuilder($factory, $analyser, $path);
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
            'fixer.analyser',
            'fixer.builder',
        ];
    }
}
