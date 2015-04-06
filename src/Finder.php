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

use Symfony\Component\Finder\Finder as SymfonyFinder;
use Symfony\CS\FinderInterface;

/**
 * This is the finder class.
 *
 * @author Graham Campbell <graham@mineuk.com>
 */
class Finder extends SymfonyFinder implements FinderInterface
{
    /**
     * Create a finder instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();

        $this->files()->ignoreDotFiles(true)->ignoreVCS(true)->exclude('vendor');
    }

    /**
     * Sets the directory that needs to be scanned for files to validate.
     *
     * @param string $dir
     *
     * @return void
     */
    public function setDir($dir)
    {
        $this->in([$dir]);
    }
}
