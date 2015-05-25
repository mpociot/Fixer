<?php

/*
 * This file is part of StyleCI Fixer.
 *
 * (c) Cachet HQ <support@cachethq.io>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace StyleCI\Fixer;

use StyleCI\Config\Config as Conf;
use StyleCI\Config\ConfigFactory;
use StyleCI\Config\FinderConfig;
use Symfony\CS\Config\Config;
use Symfony\CS\ConfigurationResolver;
use Symfony\CS\FixerInterface;

/**
 * This is the fixer resolver class.
 *
 * @author Graham Campbell <graham@cachethq.io>
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
     * @param string $path
     * @param array  $fixers
     *
     * @return \Symfony\CS\Config\Config
     */
    public function resolve($path, $fixers)
    {
        $conf = $this->getConfigObject($path);
        $config = Config::create()->level(FixerInterface::NONE_LEVEL)->fixers($conf->getFixers());

        $config->finder($this->getFinderObject($conf)->in($path));
        $config->setDir($path);

        $resolver = new ConfigurationResolver();
        $resolver->setAllFixers($fixers)->setConfig($config)->resolve();

        $config->fixers($resolver->getFixers());

        return $config;
    }

    /**
     * Get the styleci config object for the repo at the given path.
     *
     * @param string $path
     *
     * @return \StyleCI\Config\Config
     */
    protected function getConfigObject($path)
    {
        $configPath = $path.'/.styleci.yml';

        if (file_exists($configPath)) {
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

        if (null !== $finderConfig) {
            $this->configureFinder($finderConfig, $finder);
        } else {
            foreach ($conf->getExtensions() as $extension) {
                $finder->name('*.'.$extension);
            }

            foreach ($conf->getExcluded() as $excluded) {
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
        $finder->in($finderConfig->getIn());
        $finder->exclude($finderConfig->getExclude());

        foreach ($finderConfig->getName() as $pattern) {
            $finder->name($pattern);
        }

        foreach ($finderConfig->getNotName() as $pattern) {
            $finder->notName($pattern);
        }

        foreach ($finderConfig->getContains() as $pattern) {
            $finder->contains($pattern);
        }

        foreach ($finderConfig->getNotContains() as $pattern) {
            $finder->notContains($pattern);
        }

        foreach ($finderConfig->getPath() as $pattern) {
            $finder->path($pattern);
        }

        foreach ($finderConfig->getNotPath() as $pattern) {
            $finder->notPath($pattern);
        }

        foreach ($finderConfig->getDepth() as $depth) {
            $finder->depth($depth);
        }

        foreach ($finderConfig->getDate() as $date) {
            $finder->date($date);
        }
    }
}
