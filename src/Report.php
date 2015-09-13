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

use Gitonomy\Git\Diff\Diff;
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
     * The project diff instance.
     *
     * @var \Gitonomy\Git\Diff\Diff
     */
    protected $diff;

    /**
     * Create a new report instance.
     *
     * @param \Gitonomy\Git\Diff\Diff         $diff
     * @param \Symfony\CS\Error\ErrorsManager $errors
     * @param string                          $path
     *
     * @return void
     */
    public function __construct(Diff $diff, ErrorsManager $errors, $path)
    {
        $this->diff = $diff;
        $this->errors = $errors;
        $this->path = $path;
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
     * Was the analysis successful?
     *
     * @return bool
     */
    public function successful()
    {
        return empty($this->diff->getFiles());
    }

    /**
     * Does the diff contain binary files?
     *
     * @return bool
     */
    public function binary()
    {
        foreach ($this->diff->getFiles() as $file) {
            if ($file->isBinary()) {
                return true;
            }
        }

        return false;
    }
}
