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

/**
 * This is the config tester class.
 *
 * @author Graham Campbell <graham@alt-three.com>
 */
class ConfigTester
{
    /**
     * The analyzer resolver.
     *
     * @var \Closure
     */
    protected $analyzer;

    /**
     * The local storage path.
     *
     * @var string
     */
    protected $path;

    /**
     * Create a new config tester instance.
     *
     * @param \Closure $analyzer
     * @param string   $path
     *
     * @return void
     */
    public function __construct(Closure $analyzer, $path)
    {
        $this->analyzer = $analyzer;
        $this->path = $path;
    }

    /**
     * Analyze the sample and return the results.
     *
     * @param string      $sample
     * @param string|null $config
     * @param string|null $header
     *
     * @return \StyleCI\Fixer\Results
     */
    public function analyze($sample, $config = null, $header = null)
    {
        $path = $this->path.'/'.str_random(8);
        $file = $path.'/Test.php';

        try {
            mkdir($path);
            file_put_contents($file, $sample);

            $errors = $this->getAnalyzer()->analyze($path, false, $config, $header);
            $fixed = file_get_contents($file);
        } finally {
            unlink($file);
            rmdir($path);
        }

        return new Results($errors, $path, $fixed);
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
