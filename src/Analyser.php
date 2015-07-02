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
use Symfony\CS\Linter\LinterInterface;

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
     * Create an new analyser instance.
     *
     * @param \Symfony\CS\Fixer                  $fixer
     * @param \StyleCI\Fixer\ConfigResolver      $config
     * @param \Symfony\CS\Linter\LinterInterface $lint
     *
     * @return void
     */
    public function __construct(Fixer $fixer, ConfigResolver $config, LinterInterface $linter)
    {
        $this->fixer = $fixer;
        $this->config = $config;

        $this->fixer->setLinter($linter);

        $this->fixer->registerBuiltInFixers();
        $this->fixer->registerBuiltInConfigs();
    }

    /**
     * Analyse the project.
     *
     * @param string $path
     *
     * @return \Symfony\CS\Error\ErrorsManager
     */
    public function analyse($path)
    {
        $config = $this->config->resolve($path, $this->fixer->getFixers());

        $this->fixer->fix($config);

        return $this->fixer->getErrorsManager();
    }
}
