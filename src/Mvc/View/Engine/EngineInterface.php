<?php

/**
 * This file is part of the Phalcon Framework.
 *
 * (c) Phalcon Team <team@phalcon.io>
 *
 * For the full copyright and license information, please view the LICENSE.txt
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Phalcon\Mvc\View\Engine;

/**
 * Interface for Phalcon\Mvc\View engine adapters
 */
interface EngineInterface
{
    /**
     * Returns cached output on another view stage
     *
     * @return string
     */
    public function getContent(): string;

    /**
     * Renders a partial inside another view
     *
     * @param string $partialPath
     * @param mixed $params
     *
     * @return void
     */
    public function partial(?string $partialPath, mixed $params = null): void;

    /**
     * Renders a view using the template engine
     *
     * @param string $path
     * @param mixed $params
     * @param bool $mustClean
     *
     * TODO: Change params to array type
     */
    public function render(string $path, mixed $params, bool $mustClean = false);
}
