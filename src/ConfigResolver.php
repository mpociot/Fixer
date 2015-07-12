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

/**
 * This is the fixer resolver class.
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
     * @param \Symfony\CS\FixerInterface[] $fixers
     * @param string                       $path
     * @param string|false                 $cache
     * @param string|null                  $config
     *
     * @return \Symfony\CS\Config\Config
     */
    public function resolve(array $fixers, $path, $cache = false, $config = null)
    {
        $conf = $this->getConfigObject($path, $config);
        $finder = $this->getFinderObject($conf);

        $enabled = [];
        $names = $conf->getFixers();

        foreach ($fixers as $fixer) {
            if (in_array($fixer->getName(), $names, true)) {
                $enabled[] = $fixer;
            }
        }

        $config = Config::create()->finder($finder)->setDir($path)->fixers($enabled);

        if ($cache) {
            $config->setUsingCache(true);
            $config->setCacheFile($cache);
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
        $finder = Finder::create()->notName('*.blade.php');
        $finderConfig = $conf->getFinderConfig();

        if ($finderConfig) {
            $this->configureFinder($finderConfig, $finder);
        } else {
            foreach ((array) $conf->getExtensions() as $extension) {
                $finder->name('*.'.$extension);
            }

            foreach ((array) $conf->getExcluded() as $excluded) {
                $finder->exclude($excluded);
            }
        }

        return $finder;
    }

    /**
     * Configure the finder with provided configuration.
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
