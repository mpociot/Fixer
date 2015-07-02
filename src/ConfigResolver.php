<?php

/*
 * This file is part of StyleCI.
 *
 * (c) Alt Three LTD <support@alt-three.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace StyleCI\Fixer;

use Exception;
use StyleCI\Config\Config as Conf;
use StyleCI\Config\ConfigFactory;
use StyleCI\Config\Exceptions\InvalidFinderSetupException;
use StyleCI\Config\FinderConfig;
use Symfony\CS\Config\Config;
use Symfony\CS\Console\ConfigurationResolver;
use Symfony\CS\Fixer;
use Symfony\CS\FixerInterface;

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
     * @param \Symfony\CS\Fixer $fixer
     * @param string            $path
     * @param string|false      $cache
     *
     * @throws \StyleCI\Config\Exceptions\InvalidFinderSetupException
     *
     * @return \Symfony\CS\Config\Config
     */
    public function resolve(Fixer $fixer, $path, $cache = false)
    {
        $conf = $this->getConfigObject($path);

        $config = Config::create()->level(FixerInterface::NONE_LEVEL)->fixers($conf->getFixers());

        try {
            $finder = $this->getFinderObject($conf);
        } catch (Exception $e) {
            throw new InvalidFinderSetupException($e);
        }

        $config->finder($finder->in($path));
        $config->setDir($path);

        $options = ['path' => $path, 'using-cache' => false];

        if ($cache) {
            $options['using-cache'] = true;
            $options['cache-file'] = $cache;
        }

        $resolver = new ConfigurationResolver();
        $resolver->setDefaultConfig($config);
        $resolver->setCwd($path)->setFixer($fixer)->setOptions($options)->resolve();

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
        $finder->in($finderConfig->getIn());
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

        foreach ((array) $finderConfig->getDate() as $date) {
            $finder->date($date);
        }
    }
}
