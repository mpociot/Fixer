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
use InvalidArgumentException;
use StyleCI\Cache\CacheResolver;
use StyleCI\Git\Repository;
use StyleCI\Git\RepositoryFactory;

/**
 * This is the report builder class.
 *
 * @author Graham Campbell <graham@alt-three.com>
 */
class ReportBuilder
{
    use AttemptTrait;

    /**
     * The git repository factory instance.
     *
     * @var \StyleCI\Git\RepositoryFactory
     */
    protected $factory;

    /**
     * The analyzer resolver.
     *
     * @var \Closure
     */
    protected $analyzer;

    /**
     * The cache resolver instance.
     *
     * @var \StyleCI\Cache\CacheResolver
     */
    protected $cache;

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
     * @param \Closure                       $analyzer
     * @param \StyleCI\Cache\CacheResolver   $cache
     * @param string                         $path
     * @param int|false                      $backoff
     *
     * @return void
     */
    public function __construct(RepositoryFactory $factory, Closure $analyzer, CacheResolver $cache, $path, $backoff = false)
    {
        $this->factory = $factory;
        $this->analyzer = $analyzer;
        $this->cache = $cache;
        $this->path = $path;
        $this->backoff = $backoff;
    }

    /**
     * Analyze the commit and return the results.
     *
     * Note that you must provide either a branch or a pr, but not both.
     *
     * @param string      $name
     * @param int         $id
     * @param string      $commit
     * @param string|null $branch
     * @param int|null    $pr
     * @param string|null $key
     * @param string|null $config
     * @param string|null $header
     *
     * @return \StyleCI\Fixer\Report
     */
    public function analyze($name, $id, $commit, $branch, $pr, $default, $key = null, $config = null, $header = null)
    {
        $repo = $this->factory->make($name, $path = "{$this->path}/repos/{$id}", $key);

        $this->attempt($repo, function (Repository $repo) use ($commit, $branch, $pr) {
            $this->setup($repo, $commit, $branch, $pr);
        });

        $name = $this->getName($branch, $pr);

        try {
            $this->cache->setUp($id, $name, "branch.{$default}");
            $errors = $this->getAnalyzer()->analyze($path, $this->cache->path(), $config, $header);
        } finally {
            $this->cache->tearDown($id, $name);
        }

        return new Report($repo->diff(), $errors, $path);
    }

    /**
     * Get the name to use in the cache.
     *
     * @param string|null $branch
     * @param int|null    $pr
     *
     * @throws \InvalidArgumentException
     *
     * @return string
     */
    protected function getName($branch, $pr)
    {
        if ($branch) {
            return "branch.{$branch}";
        }

        if ($pr) {
            return "pr.{$pr}";
        }

        throw new InvalidArgumentException('Either a branch name or PR number provided.');
    }

    /**
     * Setup the repository locally for use.
     *
     * @param \StyleCI\Git\Repository $repo
     * @param string                  $commit
     * @param string|null             $branch
     * @param int|null                $pr
     *
     * @throws \InvalidArgumentException
     *
     * @return void
     */
    protected function setup(Repository $repo, $commit, $branch, $pr)
    {
        if (!$repo->exists()) {
            $repo->get();
        }

        if ($branch) {
            $repo->fetch("refs/heads/$branch", true);
        } elseif ($pr) {
            $repo->fetch("refs/pull/$pr/head", true);
        } else {
            throw new InvalidArgumentException('Either a branch name or PR number provided.');
        }

        $repo->reset($commit);
    }

    /**
     * Get a new analyzer instance.
     *
     * @return \StyleCI\Fixer\Analyzer
     */
    public function getAnalyzer()
    {
        $resolver = $this->analyzer;

        return $resolver();
    }
}
