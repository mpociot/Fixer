<?php

/*
 * This file is part of StyleCI Fixer.
 *
 * (c) Graham Campbell <graham@mineuk.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace StyleCI\Fixer\Analysers;

use Symfony\Component\Stopwatch\Stopwatch;

/**
 * This is the analyser manger class.
 *
 * @author Graham Campbell <graham@mineuk.com>
 */
class AnalyserManager
{
    /**
     * The stopwatch instance.
     *
     * @var \Symfony\Component\Stopwatch\Stopwatch
     */
    protected $stopwatch;

    /**
     * The registered analysers.
     *
     * @var \StyleCI\Fixer\Analysers\AnalyserInterface[]
     */
    protected $analysers;

    /**
     * Create an analyser manager instance.
     *
     * @param \Symfony\Component\Stopwatch\Stopwatch       $stopwatch
     * @param \StyleCI\Fixer\Analysers\AnalyserInterface[] $analysers
     *
     * @return void
     */
    public function __construct(Stopwatch $stopwatch, array $analysers)
    {
        $this->stopwatch = $stopwatch;
        $this->analysers = $analysers;
    }

    /**
     * Run all the analysers on the project.
     *
     * @param string $path
     *
     * @return float
     */
    public function run($path)
    {
        $this->stopwatch->start('fixFiles');

        foreach ($this->analysers as $analyser) {
            $this->analysers->analyse($path);
        }

        $this->stopwatch->stop('fixFiles');

        return round($this->stopwatch->getEvent('fixFiles')->getDuration() / 1000, 3);
    }
}
