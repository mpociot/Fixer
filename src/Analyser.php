<?php

/**
 * This file is part of Laravel Fixer by Graham Campbell.
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at http://bit.ly/UWsjkb.
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */

namespace GrahamCampbell\Fixer;

use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\Stopwatch\Stopwatch;
use Symfony\CS\Config\Config;
use Symfony\CS\ConfigurationResolver;
use Symfony\CS\ErrorsManager;
use Symfony\CS\Finder\DefaultFinder;
use Symfony\CS\Fixer as CSFixer;
use Symfony\CS\LintManager;

/**
 * This is the analyser class.
 *
 * @author    Graham Campbell <graham@mineuk.com>
 * @copyright 2014 Graham Campbell
 * @license   <https://github.com/GrahamCampbell/Laravel-Fixer/blob/master/LICENSE.md> Apache 2.0
 */
class Analyser
{
    /**
     * The fixer instance.
     *
     * @var \Symfony\CS\Fixer
     */
    protected $fixer;

    /**
     * The event dispatcher instance.
     *
     * @var \Symfony\Component\EventDispatcher\EventDispatcher
     */
    protected $eventDispatcher;

    /**
     * The stopwatch instance.
     *
     * @var \Symfony\Component\Stopwatch\Stopwatch
     */
    protected $stopwatch;

    /**
     * The errors manager instance.
     *
     * @var \Symfony\CS\ErrorsManager
     */
    protected $errorsManager;

    /**
     * The lint manager instance.
     *
     * @var \Symfony\CS\LintManager
     */
    protected $lintManager;

    /**
     * The storage path.
     *
     * @var string
     */
    protected $path;

    /**
     * Create an analyser instance.
     *
     * @param \Symfony\CS\Fixer $fixer
     * @param string            $path
     *
     * @return void
     */
    public function __construct(CSFixer $fixer)
    {
        $this->fixer = $fixer;

        $this->eventDispatcher = new EventDispatcher();
        $this->stopwatch = new Stopwatch();
        $this->errorsManager = new ErrorsManager();
        $this->lintManager = new LintManager();

        $this->fixer->registerBuiltInFixers();
        $this->fixer->registerBuiltInConfigs();
        $this->fixer->setStopwatch($this->stopwatch);
        $this->fixer->setErrorsManager($this->errorsManager);
        $this->fixer->setLintManager($this->lintManager);
    }

    /**
     * Analyse the project.
     *
     * @param string      $path
     * @param string|null $cache
     *
     * @return array
     */
    public function analyse($path, $cache = null)
    {
        $this->stopwatch->start('fixFiles');
        $this->fixer->fix($this->getConfig($path, $cache));
        $this->stopwatch->stop('fixFiles');

        $event = $this->stopwatch->getEvent('fixFiles');

        $time = round($event->getDuration() / 1000, 3);
        $memory = round($event->getMemory() / 1024 / 1024, 3);

        return compact('time', 'memory');
    }

    /**
     * Get the project configuration.
     *
     * @param string      $path
     * @param string|null $cache
     *
     * @return \Symfony\CS\Config\Config
     */
    protected function getConfig($path, $cache)
    {
        $config = $this->getConfigFromProject($path);

        if (!is_object($config) || !is_a($config, Config::class)) {
            $config = $this->getDefaultConfig($path);
        }

        $config->setDir($path);

        $resolver = new ConfigurationResolver();
        $resolver->setAllFixers($this->fixer->getFixers())->setConfig($config)->resolve();

        $config->fixers($resolver->getFixers());

        if ($cache) {
            $config->setUsingCache(true);
            $config->setCacheDir($cache);
        } else {
            $config->setUsingCache(false);
        }

        return $config;
    }

    protected function getConfigFromProject($path)
    {
        if (is_file($file = $path.'/.php_cs')) {
            return include $file;
        }
    }

    protected function getDefaultConfig($path)
    {
        $fixers = [
            '-yoda_conditions',
            'align_double_arrow',
            'multiline_spaces_before_semicolon',
            'ordered_use',
            'short_array_syntax',
        ];

        $config = Config::create()->fixers($fixers);

        $config->finder(DefaultFinder::create()->notName('*.blade.php')->exclude('storage')->in($path));

        return $config;
    }
}
