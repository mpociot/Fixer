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

use Symfony\CS\ErrorsManager;
use Symfony\CS\Fixer;
use Symfony\CS\LintManager;

/**
 * This is the analyser class.
 *
 * @author Graham Campbell <graham@alt-three.com>
 */
class Analyser
{
    /**
     * The cs fixer instance.
     *
     * @var \Symfony\CS\Fixer
     */
    protected $fixer;

    /**
     * The config resolver instance.
     *
     * @var \StyleCI\Fixer\ConfigResolver
     */
    protected $config;

    /**
     * Create an analyser instance.
     *
     * @param \Symfony\CS\Fixer             $fixer
     * @param \StyleCI\Fixer\ConfigResolver $config
     *
     * @return void
     */
    public function __construct(Fixer $fixer, ConfigResolver $config)
    {
        $this->fixer = $fixer;
        $this->config = $config;

        $this->fixer->registerBuiltInFixers();
        $this->fixer->registerBuiltInConfigs();

        $this->fixer->setErrorsManager(new ErrorsManager());
        $this->fixer->setLintManager(new LintManager());
    }

    /**
     * Analyse the project.
     *
     * @param string $path
     *
     * @return void
     */
    public function analyse($path)
    {
        $config = $this->config->resolve($path, $this->fixer->getFixers());

        $this->fixer->fix($config);
    }
}
