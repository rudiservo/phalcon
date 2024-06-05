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

namespace Phalcon\Mvc;

/**
 * Phalcon\Mvc\ViewInterface
 *
 * Interface for Phalcon\Mvc\View and Phalcon\Mvc\View\Simple
 */
interface ViewBaseInterface
{
    /**
     * Returns cached output from another view stage
     *
     * @return string
     */
    public function getContent(): string;

    /**
     * Returns parameters to views
     *
     * @return array
     */
    public function getParamsToView(): array;

    /**
     * Gets views directory
     *
     * @return string | array
     */
    public function getViewsDir(): string | array;

    /**
     * Renders a partial view
     *
     * @param ?string $partialPath
     * @param mixed $params
     * @return void
     */
    public function partial(?string $partialPath, mixed $params = null): void;

    /**
     * Externally sets the view content
     *
     * @param ?string $content
     * @return void
     */
    public function setContent(?string $content): ViewBaseInterface;

    /**
     * Adds parameters to views (alias of setVar)
     *
     * @param ?string $key
     * @param mixed $value
     * @return void
     */
    public function setParamToView(?string $key, mixed $value): ViewBaseInterface;

    /**
     * Adds parameters to views
     *
     * @param ?string $key
     * @param mixed $value
     * @return void
     */
    public function setVar(?string $key, mixed $value): ViewBaseInterface;

    /**
     * Sets views directory. Depending of your platform, always add a trailing
     * slash or backslash
     *
     * @param array|string $viewsDir
     * @return void
     */
    public function setViewsDir(array|string $viewsDir): ViewBaseInterface;
}
