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

use StyleCI\Git\Repository;
use StyleCI\Git\RepositoryFactory;

/**
 * This is the diff applier class.
 *
 * @author Graham Campbell <graham@alt-three.com>
 */
class DiffApplier
{
    use AttemptTrait;

    /**
     * The git repository factory instance.
     *
     * @var \StyleCI\Git\RepositoryFactory
     */
    protected $factory;

    /**
     * The local storage path.
     *
     * @var string
     */
    protected $path;

    /**
     * Create a new diff applier instance.
     *
     * @param \StyleCI\Git\RepositoryFactory $factory
     * @param string                         $path
     * @param int|false                      $backoff
     *
     * @return void
     */
    public function __construct(RepositoryFactory $factory, $path, $backoff = false)
    {
        $this->factory = $factory;
        $this->path = $path;
        $this->backoff = $backoff;
    }

    /**
     * Checkout a new branch, apply a diff, and commit the changes.
     *
     * @param string      $name
     * @param int         $id
     * @param string      $commit
     * @param string      $branch
     * @param string      $target
     * @param string      $diff
     * @param string      $message
     * @param string|null $author
     * @param string|null $key
     *
     * @return void
     */
    public function apply($name, $id, $commit, $branch, $target, $diff, $message, $author = null, $key = null)
    {
        $repo = $this->factory->make($name, $path = "{$this->path}/repos/{$id}", $key);

        $this->attempt($repo, function (Repository $repo) use ($commit, $branch, $target, $diff, $message, $author) {
            $this->setup($repo, $commit, $branch);
            $repo->checkout($target);
            $repo->apply($diff);
            $repo->commit($message, $author);
        });

        $repo->publish($target);
    }

    /**
     * Setup the repository locally for use.
     *
     * @param \StyleCI\Git\Repository $repo
     * @param string                  $commit
     * @param string                  $branch
     *
     * @return void
     */
    protected function setup(Repository $repo, $commit, $branch)
    {
        if (!$repo->exists()) {
            $repo->get();
        }

        $repo->fetch("refs/heads/$branch", true);

        $repo->reset($commit);
    }
}
