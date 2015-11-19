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

use Closure;
use Exception;
use StyleCI\Git\Repository;

/**
 * This is the attempt trait.
 *
 * @author Graham Campbell <graham@alt-three.com>
 */
trait AttemptTrait
{
    /**
     * The backoff time in ms.
     *
     * Set to false if we don't want to backoff on git error.
     *
     * @var int|false
     */
    protected $backoff;

    /**
     * Attempt to perform some operations on a repo.
     *
     * If we fail, optionally backoff, delete the repo, then retry.
     *
     * @param \StyleCI\Git\Repository $repo
     * @param \Closure                $function
     *
     * @return void
     */
    protected function attempt(Repository $repo, Closure $function)
    {
        try {
            $function($repo);
        } catch (Exception $e) {
            if ($this->backoff) {
                usleep($this->backoff * 1000);
            }

            $repo->delete();
            $function($repo);
        }
    }
}
