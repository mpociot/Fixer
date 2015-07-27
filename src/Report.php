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
     * The location of the project on the disk.
     *
     * @var string
     */
    protected $path;

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
            $lints[] = ['type' => 'Syntax Error', 'file' => $error->getFilePath(), 'message' => $this->sanitize($error->getMessage(), $this->path)];
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
            $errors[] = ['type' => 'Broken File', 'file' => $error->getFilePath(), 'message' => $this->sanitize($error->getMessage(), $this->path)];
        }

        return $errors;
    }

    /**
     * Sanitize the message for storage.
     *
     * We're removing all references to the location of the project on the
     * disk here, removing any double spaces for readability, and ensuing the
     * message ends in a full stop.
     *
     * @param string $message
     * @param string $path
     *
     * @return string
     */
    protected function sanitize($message, $path)
    {
        $message = $this->sanitizePaths($message, $path);

        $message = str_replace('  ', ' ', $message);
        $message = trim(rtrim($message, '.!?'));

        return "{$message}.";
    }

    /**
     * Sanitize the paths in the message.
     *
     * @param string $message
     * @param string $path
     *
     * @return string
     */
    protected function sanitizePaths($message, $path)
    {
        $real = realpath($path);

        $message = str_replace("{$real}/", '', $message);
        $message = str_replace($real, '', $message);
        $message = str_replace("{$path}/", '', $message);
        $message = str_replace($path, '', $message);

        if (substr_count($path, '/') > 2) {
            return $this->sanitizePaths($message, substr(strrpos($path, '/'), 0, $pos));
        }

        return $message;
    }
}
