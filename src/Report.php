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

use Symfony\CS\Error\ErrorsManager;

/**
 * This is the report class.
 *
 * @author Graham Campbell <graham@alt-three.com>
 */
class Report
{
    use ErrorsTrait;

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
     * The raw diff.
     *
     * @var string
     */
    protected $diff;

    /**
     * Create a new report instance.
     *
     * @param \Symfony\CS\Error\ErrorsManager $errors
     * @param string                          $path
     * @param string                          $diff
     *
     * @return void
     */
    public function __construct(ErrorsManager $errors, $path, $diff)
    {
        $this->errors = $errors;
        $this->path = $path;
        $this->diff = $diff;
    }

    /**
     * Get the get raw diff.
     *
     * @return string|null
     */
    public function diff()
    {
        return $this->diff;
    }

    /**
     * Was the analysis successful?
     *
     * @return bool
     */
    public function successful()
    {
        return $this->diff === '';
    }
}
