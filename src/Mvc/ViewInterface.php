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
 * Interface for Phalcon\Mvc\View
 */
interface ViewInterface extends ViewBaseInterface
{
    /**
     * Resets any template before layouts
     *
     * @return ViewInterface
     */
    public function cleanTemplateAfter(): ViewInterface;

    /**
     * Resets any template before layouts
     *
     * @return ViewInterface
     */
    public function cleanTemplateBefore(): ViewInterface;

    /**
     * Disables the auto-rendering process
     *
     * @return ViewInterface
     */
    public function disable(): ViewInterface;

    /**
     * Enables the auto-rendering process
     *
     * @return ViewInterface
     */
    public function enable(): ViewInterface;

    /**
     * Finishes the render process by stopping the output buffering
     *
     * @return ViewInterface
     */
    public function finish(): ViewInterface;

    /**
     * Gets the name of the action rendered
     *
     * @return string
     */
    public function getActionName(): string;

    /**
     * Returns the path of the view that is currently rendered
     *
     * @return string | array
     */
    public function getActiveRenderPath(): string | array; // TODO: return either array or null

    /**
     * Gets base path
     *
     * @return string
     */
    public function getBasePath(): string;

    /**
     * Gets the name of the controller rendered
     */
    public function getControllerName(): string;

    /**
     * Returns the name of the main view
     */
    public function getLayout(): string;

    /**
     * Gets the current layouts sub-directory
     *
     * @return string
     */
    public function getLayoutsDir(): string;

    /**
     * Returns the name of the main view
     *
     * @return string
     */
    public function getMainView(): string;

    /**
     * Gets the current partials sub-directory
     *
     * @return string
     */
    public function getPartialsDir(): string;

    /**
     * Whether the automatic rendering is disabled
     *
     * @return bool
     */
    public function isDisabled(): bool;

    /**
     * Choose a view different to render than last-controller/last-action
     *
     * @param ?string $renderView
     * @return ViewInterface
     */
    public function pick(?string $renderView): ViewInterface;

    /**
     * Register templating engines
     *
     * @param ?array $engines
     * @return ViewInterface
     */
    public function registerEngines(?array $engines): ViewInterface;

    /**
     * Executes render process from dispatching data
     *
     * @param ?string $controllerName
     * @param ?string $actionName
     * @param array $params
     * @return ViewInterface|bool
     */
    public function render(
        ?string $controllerName,
        ?string $actionName,
        array $params = []
    ): ViewInterface | bool; // TODO: change return to ViewInterface or null

    /**
     * Resets the view component to its factory default values
     *
     * @return ViewInterface
     */
    public function reset(): ViewInterface;

    /**
     * Sets base path. Depending of your platform, always add a trailing slash
     * or backslash
     *
     * @param ?string $basePath
     * @return ViewInterface
     */
    public function setBasePath(?string $basePath): ViewInterface;

    /**
     * Change the layout to be used instead of using the name of the latest
     * controller name
     *
     * @param ?string $layout
     * @return ViewInterface
     */
    public function setLayout(?string $layout): ViewInterface;

    /**
     * Sets the layouts sub-directory. Must be a directory under the views
     * directory. Depending of your platform, always add a trailing slash or
     * backslash
     *
     * @param ?string $layoutsDir
     * @return ViewInterface
     */
    public function setLayoutsDir(?string $layoutsDir): ViewInterface;

    /**
     * Sets default view name. Must be a file without extension in the views
     * directory
     *
     * @param ?string $viewPath
     * @return ViewInterface
     */
    public function setMainView(?string $viewPath): ViewInterface;

    /**
     * Sets a partials sub-directory. Must be a directory under the views
     * directory. Depending of your platform, always add a trailing slash or
     * backslash
     *
     * @param ?string $partialsDir
     * @return ViewInterface
     */
    public function setPartialsDir(?string $partialsDir): ViewInterface;

    /**
     * Sets the render level for the view
     * @param int $level
     * @return ViewInterface
     */
    public function setRenderLevel(int $level): ViewInterface;

    /**
     * Appends template after controller layout
     *
     * @param string|array templateAfter
     * @return ViewInterface
     */
    public function setTemplateAfter(string|array $templateAfter): ViewInterface; // TODO: fix type hint to only array

    /**
     * Appends template before controller layout
     *
     * @param string|array templateBefore
     * @return ViewInterface
     */
    public function setTemplateBefore(string|array $templateBefore): ViewInterface; // TODO: fix type hint to only array

    /**
     * Starts rendering process enabling the output buffering
     *
     * @return ViewInterface
     */
    public function start(): ViewInterface;
}
