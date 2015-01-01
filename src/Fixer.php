<?php

/*
 * This file is part of StyleCI Fixer.
 *
 * (c) Graham Campbell <graham@mineuk.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace StyleCI\Fixer;

use StyleCI\Fixer\GitHub\Repository;

/**
 * This is the fixer class.
 *
 * @author Graham Campbell <graham@mineuk.com>
 */
class Fixer
{
    /**
     * The analyser instance.
     *
     * @var \StyleCI\Fixer\Analyser
     */
    protected $analyser;

    /**
     * The storage path.
     *
     * @var string
     */
    protected $path;

    /**
     * The gitlib options.
     *
     * @var array
     */
    protected $options;

    /**
     * Create a fixer instance.
     *
     * @param \StyleCI\Fixer\Analyser $analyser
     * @param string                  $path
     * @param array                   $options
     *
     * @return void
     */
    public function __construct(Analyser $analyser, $path, array $options)
    {
        $this->analyser = $analyser;
        $this->path = $path;
        $this->options = $options;
    }

    /**
     * Analyse the commit and return the results.
     *
     * @param string $repo
     * @param string $commit
     *
     * @return \StyleCI\Fixer\Report
     */
    public function analyse($repo, $commit)
    {
        $repo = $this->getRepo($repo);

        $this->setup($repo, $commit);

        $data = $this->analyser->analyse($repo->path());

        return $this->buildReport($data, $repo);
    }

    /**
     * Get the github repo instance.
     *
     * @param string $repo
     *
     * @return \StyleCI\Fixer\GitHub\Repository
     */
    protected function getRepo($repo)
    {
        return new Repository($repo, $this->path, $this->options);
    }

    /**
     * Set things up for analysis.
     *
     * @param \StyleCI\Fixer\GitHub\Repository $repo
     * @param string                           $commit
     *
     * @return void
     */
    protected function setup(Repository $repo, $commit)
    {
        if (!$repo->exists()) {
            $repo->get();
        }

        $repo->fetch();

        $repo->reset($commit);
    }

    /**
     * Build the fixer report.
     *
     * @param array                            $data
     * @param \StyleCI\Fixer\GitHub\Repository $repo
     *
     * @return \StyleCI\Fixer\Report
     */
    protected function buildReport(array $data, Repository $repo)
    {
        return new Report($repo->diff(), $data['time'], $data['memory']);
    }
}
