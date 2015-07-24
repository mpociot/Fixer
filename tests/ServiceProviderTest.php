<?php

/*
 * This file is part of StyleCI.
 *
 * (c) Alt Three Services Limited
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace StyleCI\Tests\Fixer;

use GrahamCampbell\TestBench\AbstractPackageTestCase;
use GrahamCampbell\TestBenchCore\ServiceProviderTrait;
use StyleCI\Cache\CacheServiceProvider;
use StyleCI\Config\ConfigServiceProvider;
use StyleCI\Fixer\Analyzer;
use StyleCI\Fixer\FixerServiceProvider;
use StyleCI\Fixer\ReportBuilder;
use StyleCI\Git\GitServiceProvider;

/**
 * This is the service provider test class.
 *
 * @author Graham Campbell <graham@alt-three.com>
 */
class ServiceProviderTest extends AbstractPackageTestCase
{
    use ServiceProviderTrait;

    protected function getRequiredServiceProviders($app)
    {
        return [
            CacheServiceProvider::class,
            ConfigServiceProvider::class,
            GitServiceProvider::class,
        ];
    }

    protected function getServiceProviderClass($app)
    {
        return FixerServiceProvider::class;
    }

    public function testAnalyzerIsInjectable()
    {
        $this->assertIsInjectable(Analyzer::class);
    }

    public function testAnalyzerIsAlwaysDifferent()
    {
        $this->assertNotSame($this->app->make(Analyzer::class), $this->app->make(Analyzer::class));
    }

    public function testReportBuilderIsInjectable()
    {
        $this->assertIsInjectable(ReportBuilder::class);
    }

    public function testReportBuilderIsAlwaysTheSame()
    {
        $this->assertSame($this->app->make(ReportBuilder::class), $this->app->make(ReportBuilder::class));
    }
}
