<?php

/*
 * This file is part of StyleCI.
 *
 * (c) Cachet HQ <support@cachethq.io>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace StyleCI\Tests\Fixer;

use GrahamCampbell\TestBench\AbstractPackageTestCase;
use GrahamCampbell\TestBenchCore\ServiceProviderTrait;
use StyleCI\Config\ConfigServiceProvider;
use StyleCI\Fixer\Analyser;
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
            ConfigServiceProvider::class,
            GitServiceProvider::class,
        ];
    }

    protected function getServiceProviderClass($app)
    {
        return FixerServiceProvider::class;
    }

    public function testAnalyserIsInjectable()
    {
        $this->assertIsInjectable(Analyser::class);
    }

    public function testReportBuilderIsInjectable()
    {
        $this->assertIsInjectable(ReportBuilder::class);
    }
}
