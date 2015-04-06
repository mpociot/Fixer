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

/**
 * This is the analyser interface.
 *
 * @author Graham Campbell <graham@mineuk.com>
 */
class Analyser
{
    /**
     * Analyse the project.
     *
     * @param string $path
     *
     * @return void
     */
    public function analyse($path);
}
