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

use StyleCI\Config\Config as Conf;
use StyleCI\Config\ConfigFactory;
use StyleCI\Config\FinderConfig;
use Symfony\CS\Config\Config;
use Symfony\CS\FixerFactory;
use Symfony\CS\RuleSet;

/**
 * This is the config resolver class.
 *
 * @author Graham Campbell <graham@alt-three.com>
 */
class ConfigResolver
{
    /**
     * The config factory instance.
     *
     * @var \StyleCI\Config\ConfigFactory
     */
    protected $factory;

    /**
     * Create a new config resolver instance.
     *
     * @param \StyleCI\Config\ConfigFactory $factory
     *
     * @return void
     */
    public function __construct(ConfigFactory $factory)
    {
        $this->factory = $factory;
    }

    /**
     * Get the php-cs-fixer config object for the repo at the given path.
     *
     * @param string       $path
     * @param string|false $cache
     * @param string|null  $config
     * @param string|null  $header
     *
     * @return \Symfony\CS\Config\Config
     */
    public function resolve($path, $cache = false, $config = null, $header = null)
    {
        $conf = $this->getConfigObject($path, $config);
        $finder = $this->getFinderObject($conf);

        $rules = [];

        foreach ($conf->getFixers() as $fixer) {
            $rules[$fixer] = true;
        }

        if ($header) {
            $rules['header_comment'] = ['header' => $header];
        }

        $config = Config::create()->finder($finder)->setDir($path);
        $config->setRiskyAllowed(true)->setRules($rules);
        $config->setUsingLinter($conf->isLinting());

        $factory = FixerFactory::create()->attachConfig($config);
        $fixers = $factory->useRuleSet(new RuleSet($rules))->getFixers();
        $config->fixers($fixers);

        if ($cache) {
            $config->setCacheFile($cache)->setUsingCache(true);
        } else {
            $config->setUsingCache(false);
        }

        return $config;
    }

    /**
     * Get the styleci config object for the repo at the given path.
     *
     * @param string      $path
     * @param string|null $config
     *
     * @return \StyleCI\Config\Config
     */
    protected function getConfigObject($path, $config = null)
    {
        if ($config) {
            return $this->factory->makeFromYaml($config);
        }

        if (file_exists($configPath = $path.'/.styleci.yml')) {
            return $this->factory->makeFromYaml(file_get_contents($configPath));
        }

        return $this->factory->make();
    }

    /**
     * Get the styleci finder object.
     *
     * @param \StyleCI\Config\Config $conf
     *
     * @return \StyleCI\Fixer\Finder
     */
    protected function getFinderObject(Conf $conf)
    {
        $finder = Finder::create();

        $this->configureFinder($conf->getFinderConfig(), $finder);

        return $finder;
    }

    /**
     * Configure the finder with provided config.
     *
     * @param \StyleCI\Config\FinderConfig $finderConfig
     * @param \StyleCI\Fixer\Finder        $finder
     *
     * @return void
     */
    protected function configureFinder(FinderConfig $finderConfig, Finder $finder)
    {
        $finder->exclude($finderConfig->getExclude());

        foreach ((array) $finderConfig->getName() as $pattern) {
            $finder->name($pattern);
        }

        foreach ((array) $finderConfig->getNotName() as $pattern) {
            $finder->notName($pattern);
        }

        foreach ((array) $finderConfig->getContains() as $pattern) {
            $finder->contains($pattern);
        }

        foreach ((array) $finderConfig->getNotContains() as $pattern) {
            $finder->notContains($pattern);
        }

        foreach ((array) $finderConfig->getPath() as $pattern) {
            $finder->path($pattern);
        }

        foreach ((array) $finderConfig->getNotPath() as $pattern) {
            $finder->notPath($pattern);
        }

        foreach ((array) $finderConfig->getDepth() as $depth) {
            $finder->depth($depth);
        }
    }
}
