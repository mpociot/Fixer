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
 * This is the results class.
 *
 * @author Graham Campbell <graham@alt-three.com>
 */
class Results
{
    use ErrorsTrait;

    /**
     * The fixed sample.
     *
     * @var string
     */
    protected $sample;

    /**
     * Create a new results instance.
     *
     * @param \Symfony\CS\Error\ErrorsManager $errors
     * @param string                          $path
     * @param string                          $sample
     *
     * @return void
     */
    public function __construct(ErrorsManager $errors, $path, $sample)
    {
        $this->errors = $errors;
        $this->path = $path;
        $this->sample = $sample;
    }

    /**
     * Get the fixed sample.
     *
     * @return string
     */
    public function sample()
    {
        return $this->sample;
    }
}
