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

use Gitonomy\Git\Diff\Diff;
use Symfony\CS\Error\ErrorsManager;

/**
 * This is the report class.
 *
 * @author Graham Campbell <graham@alt-three.com>
 */
class Report
{
    /**
     * The project diff instance.
     *
     * @var \Gitonomy\Git\Diff\Diff
     */
    protected $diff;

    /**
     * The error manager instance.
     *
     * @var \Symfony\CS\Error\ErrorsManager
     */
    protected $errors;

    /**
     * Create a new report instance.
     *
     * @param \Gitonomy\Git\Diff\Diff         $diff
     * @param \Symfony\CS\Error\ErrorsManager $errors
     *
     * @return void
     */
    public function __construct(Diff $diff, ErrorsManager $errors)
    {
        $this->diff = $diff;
        $this->errors = $errors;
    }

    /**
     * Get the get raw diff.
     *
     * @return string
     */
    public function diff()
    {
        return $this->diff->getRawDiff();
    }

    /**
     * Get the modified files.
     *
     * @return array
     */
    public function files()
    {
        return $this->diff->getFiles();
    }

    /**
     * Was the analysis successful?
     *
     * @return bool
     */
    public function successful()
    {
        return empty($this->diff->getFiles());
    }

    /**
     * Get the linting errors.
     *
     * @return array
     */
    public function lints()
    {
        $lints = [];

        foreach ($this->errors->getInvalidErrors() as $error) {
            $lints[] = ['type' => 'Syntax Error', 'file' => $error->getFilePath(), 'message' => $error->getMessage()];
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

        foreach ($this->errors->getNonInvalidErrors() as $error) {
            $error[] = ['type' => 'Internal Error', 'file' => $error->getFilePath(), 'message' => $error->getMessage()];
        }

        return $errors;
    }
}
