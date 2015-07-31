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

use Symfony\CS\Fixer;
use Symfony\CS\Linter\LinterInterface;

/**
 * This is the analyzer class.
 *
 * @author Graham Campbell <graham@alt-three.com>
 */
class Analyzer
{
    /**
     * The config resolver instance.
     *
     * @var \StyleCI\Fixer\ConfigResolver
     */
    protected $config;

    /**
     * The cs fixer instance.
     *
     * @var \Symfony\CS\Fixer
     */
    protected $fixer;

    /**
     * The file linter instance.
     *
     * @var \Symfony\CS\Linter\LinterInterface
     */
    protected $linter;

    /**
     * Create an new analyzer instance.
     *
     * @param \Symfony\CS\Fixer                  $fixer
     * @param \StyleCI\Fixer\ConfigResolver      $config
     * @param \Symfony\CS\Linter\LinterInterface $linter
     *
     * @return void
     */
    public function __construct(ConfigResolver $config, Fixer $fixer, LinterInterface $linter)
    {
        $this->config = $config;
        $this->fixer = $fixer;
        $this->linter = $linter;

        $this->fixer->registerBuiltInFixers();
        $this->fixer->registerBuiltInConfigs();
    }

    /**
     * Analyze the project.
     *
     * @param string       $path
     * @param string|false $cache
     * @param string|null  $config
     * @param string|null  $header
     *
     * @return \Symfony\CS\Error\ErrorsManager
     */
    public function analyze($path, $cache = false, $config = null, $header = null)
    {
        $config = $this->config->resolve($this->fixer->getFixers(), $path, $cache, $config, $header);

        if ($config->usingLinter()) {
            $this->fixer->setLinter($this->linter);
        }

        $this->fixer->fix($config);

        return $this->fixer->getErrorsManager();
    }
}
