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

use InvalidArgumentException;
use StyleCI\Git\Repositories\RepositoryInterface;
use StyleCI\Git\RepositoryFactory;

/**
 * This is the report builder class.
 *
 * @author Graham Campbell <graham@alt-three.com>
 */
class ReportBuilder
{
    /**
     * The git repository factory instance.
     *
     * @var \StyleCI\Git\RepositoryFactory
     */
    protected $factory;

    /**
     * The cs analyser instance.
     *
     * @var \StyleCI\Fixer\Analyser
     */
    protected $analyser;

    /**
     * The local storage path.
     *
     * @var string
     */
    protected $path;

    /**
     * Create a new report builder instance.
     *
     * @param \StyleCI\Git\RepositoryFactory $factory
     * @param \StyleCI\Fixer\Analyser        $analyser
     * @param string                         $path
     *
     * @return void
     */
    public function __construct(RepositoryFactory $factory, Analyser $analyser, $path)
    {
        $this->factory = $factory;
        $this->analyser = $analyser;
        $this->path = $path;
    }

    /**
     * Analyse the commit and return the results.
     *
     * Note that you must provide either a branch or a pr, but not both.
     *
     * @param string      $name
     * @param int         $id
     * @param string      $commit
     * @param string|null $branch
     * @param int|null    $pr
     * @param string|null $key
     *
     * @return \StyleCI\Fixer\Report
     */
    public function analyse($name, $id, $commit, $branch, $pr, $default, $key = null)
    {
        $repo = $this->factory->make($name, $path = "{$this->path}/repos/{$id}", $this->getKeyPath($key));

        $this->setup($repo, $commit, $branch, $pr);

        $errors = $this->analyser->analyse($path, $this->cacheCachePath($id, $branch, $pr, $default));

        return new Report($repo->diff(), $errors, $path);
    }

    /**
     * Save the private key and return its path.
     *
     * If no key is provided, then we do nothing and return nothing.
     *
     * @param string|null $key
     *
     * @return string|null
     */
    protected function getKeyPath($key = null)
    {
        if ($key) {
            $path = "{$this->path}/key";
            file_put_contents($path, $key);

            return $path;
        }
    }

    /**
     * Set things the repository locally for analysis.
     *
     * @param \StyleCI\Git\Repositories\RepositoryInterface $repo
     * @param string                                        $commit
     * @param string|null                                   $branch
     * @param int|null                                      $pr
     *
     * @throws \InvalidArgumentException
     *
     * @return void
     */
    protected function setup(RepositoryInterface $repo, $commit, $branch, $pr)
    {
        if (!$repo->exists()) {
            $repo->get();
        }

        if ($branch) {
            $repo->fetch("refs/heads/$branch");
        } elseif ($pr) {
            $repo->fetch("refs/pull/$pr/head");
        } else {
            throw new InvalidArgumentException('Either a repo or pr must be provided.');
        }

        $repo->reset($commit);
    }

    /**
     * Prep the cache and get the cache file to use.
     *
     * @param int         $id
     * @param string|null $branch
     * @param int|null    $pr
     * @param string      $default
     *
     * @throws \InvalidArgumentException
     *
     * @return string
     */
    protected function cacheCachePath($id, $branch, $pr, $default)
    {
        $path = "{$this->path}/fixers/{$id}";

        if (!is_dir($path)) {
            mkdir($path, 0777, true);
        }

        if ($branch) {
            $path .= "/branch-{$branch}";
        } elseif ($pr) {
            $path .= "/pr-{$pr}";
        } else {
            throw new InvalidArgumentException('Either a repo or pr must be provided.');
        }

        if (!file_exists($path) && file_exists($main = "{$this->path}/fixers/{$id}/branch-{$default}")) {
            copy($main, $path);
        }

        return $path;
    }
}
