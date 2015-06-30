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
     * Create a report instance.
     *
     * @param \Gitonomy\Git\Diff\Diff $diff
     *
     * @return void
     */
    public function __construct(Diff $diff)
    {
        $this->diff = $diff;
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
}
