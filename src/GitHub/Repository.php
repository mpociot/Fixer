<?php

/*
 * This file is part of StyleCI Fixer.
 *
 * (c) Graham Campbell <graham@mineuk.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace StyleCI\Fixer\GitHub;

use Exception;
use Gitonomy\Git\Admin as Git;
use Gitonomy\Git\Repository as GitRepo;
use Symfony\Component\Filesystem\Filesystem;

/**
 * This is the github repository class.
 *
 * @author Graham Campbell <graham@mineuk.com>
 */
class Repository
{
    /**
     * The local storage path.
     *
     * @var string
     */
    protected $path;

    /**
     * The github repository url.
     *
     * @var string
     */
    protected $url;

    /**
     * The gitlib options.
     *
     * @var array
     */
    protected $options;

    /**
     * The symfony filesystem instance.
     *
     * @var \Symfony\Component\Filesystem\Filesystem
     */
    protected $filesystem;

    /**
     * The gitlib repository instance.
     *
     * @var \Gitonomy\Git\Repository
     */
    protected $repo;

    /**
     * Create a new github repository instance.
     *
     * @param string $repo
     * @param string $path
     * @param array  $options
     *
     * @return void
     */
    public function __construct($repo, $path, array $options = [])
    {
        $this->path = $path.'/'.sha1($repo);
        $this->url = 'https://github.com/'.$repo.'.git';
        $this->options = $options;
        $this->filesystem = new Filesystem();
    }

    /**
     * Return the repository path on the local filesystem.
     *
     * @return string
     */
    public function path()
    {
        return $this->path;
    }

    /**
     * Does this git repository exist on the local filesystem?
     *
     * @return bool
     */
    public function exists()
    {
        return $this->filesystem->exists($this->path.'/.git');
    }

    /**
     * Clone the git repository to the local filesystem.
     *
     * @return void
     */
    public function get()
    {
        if ($this->exists()) {
            throw new Exception('You cannot clone a repo that already exists on the filesystem.');
        }

        $this->filesystem->mkdir($this->path);

        $this->repo = Git::cloneTo($this->path, $this->url, false, $this->options);
    }

    /**
     * Get the gitlib repository instance if the local state is usable.
     *
     * @return \Gitonomy\Git\Repository
     */
    public function repo()
    {
        if ($this->repo) {
            return $this->repo;
        }

        if (!$this->exists()) {
            throw new Exception('You need to clone the repo before you attempt to use it.');
        }

        return $this->repo = new GitRepo($this->path, $this->options);
    }

    /**
     * Fetch the latest changes to our git repository from the interwebs.
     *
     * @return void
     */
    public function fetch()
    {
        $this->repo()->run('fetch', ['--all']);
    }

    /**
     * Reset our local git repository to a specific commit.
     *
     * @param string $commit
     *
     * @return void
     */
    public function reset($commit)
    {
        $this->repo()->run('reset', ['--hard', $commit]);
    }

    /**
     * Get the diff for the uncommitted modifications.
     *
     * @return \Gitonomy\Git\Diff\Diff
     */
    public function diff()
    {
        return $this->repo()->getDiff('HEAD');
    }

    /**
     * Delete our local git repository from the local filesystem.
     *
     * Only do this if you really don't need it again because cloning it is
     * resource intensive, and can take a long time vs simply fetching the
     * latest changes in the future.
     *
     * @return void
     */
    public function delete()
    {
        $this->repo = null;

        if ($this->exists()) {
            $this->filesystem->remove($this->path);
        }
    }
}
