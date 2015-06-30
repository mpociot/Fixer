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
     * Create a report builder instance.
     *
     * @param \StyleCI\Git\RepositoryFactory $factory
     * @param \StyleCI\Fixer\Analyser        $analyser
     *
     * @return void
     */
    public function __construct(RepositoryFactory $factory, Analyser $analyser)
    {
        $this->factory = $factory;
        $this->analyser = $analyser;
    }

    /**
     * Analyse the commit and return the results.
     *
     * @param string $name
     * @param int    $id
     * @param string $commit
     *
     * @return \StyleCI\Fixer\Report
     */
    public function analyse($name, $id, $commit)
    {
        $repo = $this->factory->make($repo, (string) $id);

        $this->setup($repo, $commit);

        $this->analyser->analyse($repo->path());

        return new Report($repo->diff());
    }

    /**
     * Set things the repository locally for analysis.
     *
     * @param \StyleCI\Git\Repositories\RepositoryInterface $repo
     * @param string                                        $commit
     *
     * @return void
     */
    protected function setup(RepositoryInterface $repo, $commit)
    {
        if (!$repo->exists()) {
            $repo->get();
        }

        $repo->fetch();

        $repo->reset($commit);
    }
}
