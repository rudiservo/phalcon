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

namespace Phalcon\Cli;

use Phalcon\Application\AbstractApplication;
use Phalcon\Cli\Console\Exception;
use Phalcon\Cli\Router\Route;
use Phalcon\Events\Exception as EventsException;

use function array_merge;
use function array_shift;
use function class_exists;
use function explode;
use function file_exists;
use function implode;
use function is_array;
use function is_string;
use function ltrim;
use function strncmp;

/**
 * This component allows to create CLI applications using Phalcon
 */
class Console extends AbstractApplication
{
    /**
     * @var array|string
     */
    protected array | string $arguments = [];

    /**
     * @var array
     */
    protected array $options = [];

    /**
     * Handle the whole command-line tasks
     *
     * @param array $arguments
     *
     * @return bool|mixed|null
     * @throws Exception
     * @throws Router\Exception
     * @throws EventsException
     */
    public function handle(array $arguments = [])
    {
        $this->checkContainer(Exception::class, 'internal services');

        /**
         * Call boot event, this allows the developer to perform initialization
         * actions
         */
        if (false === $this->fireManagerEvent("console:boot")) {
            return false;
        }

        /** @var Router $router */
        $router = $this->container->getShared("router");

        if (empty($arguments) && !empty($this->arguments)) {
            $router->handle($this->arguments);
        } else {
            $router->handle($arguments);
        }

        /**
         * If the router doesn't return a valid module we use the default module
         */
        $moduleName = $router->getModuleName();

        if (empty($moduleName)) {
            $moduleName = $this->defaultModule;
        }

        if (!empty($moduleName)) {
            if (false === $this->fireManagerEvent("console:beforeStartModule", $moduleName)) {
                return false;
            }

            if (!isset($this->modules[$moduleName])) {
                throw new Exception(
                    "Module '" . $moduleName . "' isn't registered in the console container"
                );
            }

            $module = $this->modules[$moduleName];

            if (!is_array($module)) {
                throw new Exception("Invalid module definition path");
            }

            $className = $module["className"] ?? "Module";

            if (isset($module["path"])) {
                $path = $module["path"];
                if (true !== file_exists($path)) {
                    throw new Exception(
                        "Module definition path '" . $path . "' doesn't exist"
                    );
                }

                if (true !== class_exists($className, false)) {
                    require_once $path;
                }
            }

            $moduleObject = $this->container->get($className);

            $moduleObject->registerAutoloaders($this->container);
            $moduleObject->registerServices($this->container);

            if (false === $this->fireManagerEvent("console:afterStartModule", $moduleObject)) {
                return false;
            }
        }

        /** @var Dispatcher $dispatcher */
        $dispatcher = $this->container->getShared("dispatcher");

        $dispatcher->setModuleName($router->getModuleName());
        $dispatcher->setTaskName($router->getTaskName());
        $dispatcher->setActionName($router->getActionName());
        $dispatcher->setParams($router->getParams());
        $dispatcher->setOptions($this->options);

        if (false === $this->fireManagerEvent("console:beforeHandleTask", $dispatcher)) {
            return false;
        }

        $task = $dispatcher->dispatch();

        $this->fireManagerEvent("console:afterHandleTask", $task);

        return $task;
    }

    /**
     * Set a specific argument
     *
     * @param array $arguments
     * @param bool  $str
     * @param bool  $shift
     *
     * @return $this
     */
    public function setArgument(
        array $arguments = [],
        bool $str = true,
        bool $shift = true
    ): Console {
        $args       = [];
        $opts       = [];
        $handleArgs = [];

        if (true === $shift && !empty($arguments)) {
            array_shift($arguments);
        }

        foreach ($arguments as $argument) {
            if (is_string($argument)) {
                if (0 === strncmp($argument, "--", 2)) {
                    $parts    = explode("=", $argument);
                    $parts[0] = ltrim($parts[0], '-');
                    $parts[1] = $parts[1] ?? true;

                    $opts[$parts[0]] = $parts[1];
                } elseif (0 === strncmp($argument, "-", 1)) {
                    $argument        = ltrim($argument, '-');
                    $opts[$argument] = true;
                } else {
                    $args[] = $argument;
                }
            } else {
                $args[] = $argument;
            }
        }

        if (true === $str) {
            $this->arguments = implode(Route::getDelimiter(), $args);
        } else {
            if (!empty($args)) {
                $handleArgs["task"] = array_shift($args);
            }

            if (!empty($args)) {
                $handleArgs["action"] = array_shift($args);
            }

            if (!empty($args)) {
                $handleArgs = array_merge($handleArgs, $args);
            }

            $this->arguments = $handleArgs;
        }

        $this->options = $opts;

        return $this;
    }
}
