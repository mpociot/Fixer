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

/**
 * This is the errors trait.
 *
 * @author Graham Campbell <graham@alt-three.com>
 */
trait ErrorsTrait
{
    /**
     * The error manager instance.
     *
     * @var \Symfony\CS\Error\ErrorsManager
     */
    protected $errors;

    /**
     * The location of the project.
     *
     * @var string
     */
    protected $path;

    /**
     * Get the linting errors.
     *
     * @return array
     */
    public function lints()
    {
        $lints = [];

        foreach ($this->errors->getInvalidErrors() as $error) {
            $lints[] = ['type' => 'Syntax Error', 'file' => $error->getFilePath(), 'message' => Sanitizer::sanitize($error->getMessage(), $this->path)];
        }

        return $lints;
    }

    /**
     * Get the analysis errors.
     *
     * @return array
     */
    public function errors()
    {
        $errors = [];

        foreach ($this->errors->getExceptionErrors() as $error) {
            $errors[] = ['type' => 'Failed To Fix', 'file' => $error->getFilePath(), 'message' => 'Something went wrong when we tried to fix '.$error->getFilePath().'.'];
        }

        foreach ($this->errors->getLintErrors() as $error) {
            $errors[] = ['type' => 'Broken File', 'file' => $error->getFilePath(), 'message' => Sanitizer::sanitize($error->getMessage(), $this->path)];
        }

        return $errors;
    }
}
