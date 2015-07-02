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
     * The errors manager instance.
     *
     * @var \Symfony\CS\ErrorsManager
     */
    protected $errors;

    /**
     * The lint manager instance.
     *
     * @var \Symfony\CS\LintManager
     */
    protected $lint;

    /**
     * Create an new analyser instance.
     *
     * @param \Symfony\CS\Fixer             $fixer
     * @param \StyleCI\Fixer\ConfigResolver $config
     * @param \Symfony\CS\ErrorsManager     $errors
     * @param \Symfony\CS\LintManager       $lint
     *
     * @return void
     */
    public function __construct(Fixer $fixer, ConfigResolver $config, ErrorsManager $errors, LintManager $lint)
    {
        $this->fixer = $fixer;
        $this->config = $config;
        $this->errors = $errors;
        $this->lint = $lint;

        $this->fixer->registerBuiltInFixers();
        $this->fixer->registerBuiltInConfigs();
        $this->fixer->setErrorsManager($errors);
        $this->fixer->setLintManager($lint);
    }

    /**
     * Analyse the project.
     *
     * @param string $path
     *
     * @return array
     */
    public function analyse($path)
    {
        $config = $this->config->resolve($path, $this->fixer->getFixers());

        $this->fixer->fix($config);

        return $this->errors->getErrors();
    }
}
