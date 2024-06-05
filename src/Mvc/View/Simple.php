<?php

/**
 * This file is part of the Phalcon Framework.
 *
 * (c) Phalcon Team <team@phalcon.io>
 *
 * For the full copyright and license information, please view the LICENSE.txt
 * file that was distributed with this source code.
 */

namespace Phalcon\Mvc\View;

use Closure;
use Phalcon\Di\DiInterface;
use Phalcon\Di\Injectable;
use Phalcon\Events\EventsAwareInterface;
use Phalcon\Events\ManagerInterface;
use Phalcon\Mvc\ViewBaseInterface;
use Phalcon\Mvc\View\Engine\EngineInterface;
use Phalcon\Mvc\View\Engine\Php as PhpEngine;
use Phalcon\Support\Traits\DirSeparatorTrait;

/**
 * Phalcon\Mvc\View\Simple
 *
 * This component allows to render views without hierarchical levels
 *
 *```php
 * use Phalcon\Mvc\View\Simple as View;
 *
 * $view = new View();
 *
 * // Render a view
 * echo $view->render(
 *     "templates/my-view",
 *     [
 *         "some" => $param,
 *     ]
 * );
 *
 * // Or with filename with extension
 * echo $view->render(
 *     "templates/my-view.volt",
 *     [
 *         "parameter" => $here,
 *     ]
 * );
 *```
 */
class Simple extends Injectable implements ViewBaseInterface, EventsAwareInterface
{
    use DirSeparatorTrait;

    /**
     * @var string
     */
    protected $activeRenderPath;

    /**
     * @var string
     */
    protected $content;

    /**
     * @var EngineInterface[]|false
     */
    protected $engines = false; // TODO: Change to default null or empty array

    /**
     * @var ManagerInterface|null
     */
    protected $eventsManager;

    /**
     * @var array
     */
    protected $options = [];

    /**
     * @var array
     */
    protected $registeredEngines = [];

    /**
     * @var string
     */
    protected $viewsDir;

    /**
     * @var array
     */
    protected $viewParams = [];

    /**
     * Phalcon\Mvc\View\Simple constructor
     *
     * @param array $options
     */
    public function __construct(array $options = [])
    {
        $this->options = $options;
    }

    /**
     * Magic method to retrieve a variable passed to the view
     *
     *```php
     * echo $this->view->products;
     *```
     *
     * @param string key
     * @return mixed
     */
    public function __get(?string $key): mixed // TODO: Avoid or remove Magic __get
    {
        if (false === isset($this->viewParams[$key])) {
            return null;
        }

        return $this->viewParams[$key];
    }

    /**
     * Magic method to pass variables to the views
     *
     *```php
     * $this->view->products = $products;
     *```
     *
     * @param string key
     * @param mixed value
     * @return void
     */
    public function __set(?string $key, mixed $value): void // TODO: Avoid or remove magic __set
    {
        $this->viewParams[$key] = $value;
    }

    /**
     * Returns the path of the view that is currently rendered
     *
     * @return string
     */
    public function getActiveRenderPath(): string
    {
        return $this->activeRenderPath;
    }

    /**
     * Returns output from another view stage
     *
     * @return string
     */
    public function getContent(): string
    {
        return $this->content;
    }

    /**
     * Returns the internal event manager
     *
     * @return ManagerInterface|null
     */
    public function getEventsManager(): ManagerInterface | null
    {
        return $this->eventsManager;
    }

    /**
     * Returns parameters to views
     *
     * @return array
     */
    public function getParamsToView(): array
    {
        return $this->viewParams;
    }

    /**
     * @return array
     */
    public function getRegisteredEngines(): array
    {
        return $this->registeredEngines;
    }

    /**
     * Returns a parameter previously set in the view
     *
     * @param string key
     * @return mixed
     */
    public function getVar(?string $key): mixed // TODO: settle on a proper return type instead of mixed
    {
        if (false === isset($this->viewParams[$key])) {
            return null;
        }

        return $this->viewParams[$key];
    }

    /**
     * Gets views directory
     *
     * @return string
     */
    public function getViewsDir(): string
    {
        return $this->viewsDir;
    }

    /**
     * Renders a partial view
     *
     * ```php
     * // Show a partial inside another view
     * $this->partial("shared/footer");
     * ```
     *
     * ```php
     * // Show a partial inside another view with parameters
     * $this->partial(
     *     "shared/footer",
     *     [
     *         "content" => $html,
     *     ]
     * );
     * ```
     *
     * @param string $partialPath
     * @param array $params
     * @return void
     */
    public function partial(?string $partialPath, mixed $params = null): void
    {
        /**
         * Start output buffering
         */
        ob_start();

        /**
         * If the developer pass an array of variables we create a new virtual
         * symbol table
         */
        if (true === is_array($params)) {
            $viewParams = $this->viewParams;

            /**
             * Merge or assign the new params as parameters
             */
            $mergedParams = array_merge($viewParams, $params);
        } else {
            $mergedParams = $params;
        }

        /**
         * Call engine render, this checks in every registered engine for the partial
         */
        $this->internalRender($partialPath, $mergedParams);

        /**
         * Now we need to restore the original view parameters
         */
        if (true === is_array($params)) {
            /**
             * Restore the original view params
             */
            $this->viewParams = $viewParams;
        }

        ob_end_clean();

        /**
         * Content is output to the parent view
         */
        echo $this->content;
    }

    /**
     * Register templating engines
     *
     *```php
     * $this->view->registerEngines(
     *     [
     *         ".phtml" => \Phalcon\Mvc\View\Engine\Php::class,
     *         ".volt"  => \Phalcon\Mvc\View\Engine\Volt::class,
     *         ".mhtml" => \MyCustomEngine::class,
     *     ]
     * );
     *```
     *
     * @param array $engines
     * @return void
     */
    public function registerEngines(?array $engines): void
    {
        $this->registeredEngines = $engines;
    }

    /**
     * Renders a view
     *
     * @param string $path
     * @param array $params
     * @return string
     */
    public function render(?string $path, array $params = []): string
    {
        ob_start();

        $viewParams = $this->viewParams;

        /**
         * Merge parameters
         */
        $mergedParams = array_merge($viewParams, $params);

        /**
         * internalRender is also reused by partials
         */
        $this->internalRender($path, $mergedParams);

        ob_end_clean();

        return $this->content;
    }

    /**
     * Externally sets the view content
     *
     *```php
     * $this->view->setContent("<h1>hello</h1>");
     *```
     *
     * @param string $content
     * @return Simple
     */
    public function setContent(?string $content): Simple
    {
        $this->content = $content;
        return $this;
    }

    /**
     * Sets the events manager
     *
     * @param ManagerInterface $eventsManager
     * @return void
     */
    public function setEventsManager(ManagerInterface $eventsManager): void
    {
        $this->eventsManager = $eventsManager;
    }

    /**
     * Adds parameters to views (alias of setVar)
     *
     *```php
     * $this->view->setParamToView("products", $products);
     *```
     *
     * @param ?string key
     * @param mixed value
     * @return Simple
     */
    public function setParamToView(?string $key, mixed $value): Simple
    {
        return $this->setVar($key, $value);
    }

    /**
     * Set a single view parameter
     *
     *```php
     * $this->view->setVar("products", $products);
     *```
     *
     * @param ?string $key
     * @param mixed $value
     * @return Simple
     */
    public function setVar(?string $key, mixed $value): Simple
    {
        $this->viewParams[$key] = $value;
        return $this;
    }

    /**
     * Set all the render params
     *
     *```php
     * $this->view->setVars(
     *     [
     *         "products" => $products,
     *     ]
     * );
     *```
     *
     * @param ?array $params
     * @param bool $merge
     * @return Simple
     */
    public function setVars(?array $params, bool $merge = true): Simple
    {
        if ($merge) {
            $params = array_merge($this->viewParams, $params);
        }

        $this->viewParams = $params;

        return $this;
    }

    /**
     * Sets views directory
     *
     * @param array|string $viewsDir
     * @return void
     */
    public function setViewsDir(array|string $viewsDir): Simple
    {
        $this->viewsDir = $this->getDirSeparator($viewsDir);
        return $this;
    }

    /**
     * Loads registered template engines, if none are registered it will use
     * Phalcon\Mvc\View\Engine\Php
     *
     * @return array
     */
    protected function loadTemplateEngines(): array
    {
        /**
         * If the engines aren't initialized 'engines' is false
         */
        $engines = $this->engines;

        if ($engines === false) {
            $di = $this->container;

            $engines = [];

            $registeredEngines = $this->registeredEngines;

            if (true === empty($registeredEngines)) {
                /**
                 * We use Phalcon\Mvc\View\Engine\Php as default
                 * Use .phtml as extension for the PHP engine
                 */
                $engines[".phtml"] = new PhpEngine($this, $di);
            } else {
                if (false === is_object($di)) {
                    throw new Exception(
                        "A dependency injection container is required to access the application services"
                    );
                }

                foreach ($registeredEngines as $extension => $engineService) {
                    if (true === is_object($engineService)) {
                        /**
                         * Engine can be a closure
                         */
                        if ($engineService instanceof Closure) {
                            $engineService = Closure::bind($engineService, $di);

                            $engineObject = call_user_func($engineService, $this);
                        } else {
                            $engineObject = $engineService;
                        }
                    } elseif (true === is_string($engineService)) {
                        /**
                         * Engine can be a string representing a service in the DI
                         */
                        $engineObject = $di->getShared(
                            $engineService,
                            [
                                $this
                            ]
                        );
                    } else {
                        throw new Exception(
                            "Invalid template engine registration for extension: " . $extension
                        );
                    }

                    $engines[$extension] = $engineObject;
                }
            }

            $this->engines = $engines;
        } else {
            $engines = $this->engines;
        }

        return $engines;
    }

    /**
     * Tries to render the view with every engine registered in the component
     *
     * @param string $path
     * @param array  $params
     *
     * @return void
     */
    final protected function internalRender(?string $path, array $params): void
    {
        $eventsManager = $this->eventsManager;

        if (true === is_object($eventsManager)) {
            $this->activeRenderPath = $path;
        }

        /**
         * Call beforeRender if there is an events manager
         */
        if (true === is_object($eventsManager)) {
            if ($eventsManager->fire("view:beforeRender", $this) === false) {
                return;
            }
        }

        $notExists = true;
        $mustClean = true;

        $viewsDirPath = $this->viewsDir . $path;

        /**
         * Load the template engines
         */
        $engines = $this->loadTemplateEngines();

        /**
         * Views are rendered in each engine
         */
        foreach ($engines as $extension => $engine) {
            if (file_exists($viewsDirPath . $extension)) {
                $viewEnginePath = $viewsDirPath . $extension;
            } elseif (substr($viewsDirPath, -strlen($extension)) == $extension && file_exists($viewsDirPath)) {
                /**
                 * if passed filename with engine extension
                 */

                $viewEnginePath = $viewsDirPath;
            } else {
                continue;
            }

            /**
             * Call beforeRenderView if there is an events manager available
             */
            if (true === is_object($eventsManager)) {
                if ($eventsManager->fire("view:beforeRenderView", $this, $viewEnginePath) === false) {
                    continue;
                }
            }

            $engine->render($viewEnginePath, $params, $mustClean);

            $notExists = false;

            /**
             * Call afterRenderView if there is an events manager available
             */
            if (true === is_object($eventsManager)) {
                $eventsManager->fire("view:afterRenderView", $this);
            }

            break;
        }

        /**
         * Always throw an exception if the view does not exist
         */
        if ($notExists) {
            throw new Exception(
                "View '" . $viewsDirPath . "' was not found in the views directory"
            );
        }

        /**
         * Call afterRender event
         */
        if (true === is_object($eventsManager)) {
            $eventsManager->fire("view:afterRender", $this);
        }
    }
}
