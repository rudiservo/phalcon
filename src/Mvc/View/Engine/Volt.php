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

use Phalcon\Di\DiInterface;
use Phalcon\Events\EventsAwareInterface;
use Phalcon\Events\ManagerInterface;
use Phalcon\Html\Link\Link;
use Phalcon\Html\Link\Serializer\Header;
use Phalcon\Mvc\View\Engine\Volt\Compiler;
use Phalcon\Mvc\View\Exception;

/**
 * Designer friendly and fast template engine for PHP written in Zephir/C
 */
class Volt extends AbstractEngine implements EventsAwareInterface
{
    /**
     * @var Compiler
     */
    protected Compiler $compiler;

    /**
     * @var ManagerInterface|null
     */
    protected ?ManagerInterface $eventsManager;

    /**
     * @var array
     */
    protected array $macros = [];

    /**
     * @var array
     */
    protected array $options = [];

    /**
     * Checks if a macro is defined and calls it
     *
     * @params ?string name
     * @params array arguments
     *
     * @return mixed
     * @throws Exception
     */
    public function callMacro(?string $name, array $arguments = []): mixed
    {

        if (false === isset($this->macro[$name])) {
            throw new Exception("Macro '" . $name . "' does not exist");
        }
        $macro = $this->macros[$name];
        return call_user_func($macro, $arguments);
    }

    /**
     * Performs a string conversion
     *
     * @return string
     */
    public function convertEncoding(string $text, ?string $from, ?string $to): string
    {
        if (function_exists("mb_convert_encoding")) {
            return mb_convert_encoding($text, $from, $to);
        }

        /**
         * There are no enough extensions available
         */
        throw new Exception(
            "'mbstring' is required to perform the charset conversion"
        );
    }

    /**
     * Returns the Volt's compiler
     *
     * @return Compiler
     */
    public function getCompiler(): Compiler
    {

        $compiler = $this->compiler;

        if (false === is_object($compiler)) {
            $compiler = new Compiler($this->view);

            /**
             * Pass the IoC to the compiler only of it's an object
             * @var DiInterface $container
             */
            $container =  $this->container;

            if (true === is_object($container)) {
                $compiler->setDi($container);
            }

            /**
             * Pass the options to the compiler only if they're an array
             */
            $options = $this->options;

            if (true === is_array($options)) {
                $compiler->setOptions($options);
            }

            $this->compiler = $compiler;
        }

        return $compiler;
    }

    /**
     * Returns the internal event manager
     *
     * @return ?ManagerInterface
     */
    public function getEventsManager(): ?ManagerInterface
    {
        return $this->eventsManager;
    }

    /**
     * Return Volt's options
     *
     * @return array
     */
    public function getOptions(): array
    {
        return $this->options;
    }

    /**
     * Checks if the needle is included in the haystack
     *
     * @param mixed needle
     * @param array|string haystack
     *
     * @return bool
     */
    public function isIncluded(mixed $needle, array|string $haystack): bool // TODO: Change so single type of variable
    {
        if (null === $needle) {
            $needle = "";
        }

        if (true === is_array($haystack)) {
            return in_array($needle, $haystack);
        }

        if (true === is_string($haystack)) {
            if (function_exists("mb_strpos")) {
                return mb_strpos($haystack, $needle) !== false;
            }

            return strpos($haystack, $needle) !== false;
        }

        throw new Exception("Invalid haystack");
    }

    /**
     * Length filter. If an array/object is passed a count is performed otherwise a strlen/mb_strlen
     *
     * @param mixed item
     *
     * @return int
     */
    public function length(mixed $item): int
    {
        if (null === $item) {
            $item = "";
        }

        if (true === is_object($item) || true === is_array($item)) {
            return count($item);
        }

        if (function_exists("mb_strlen")) {
            return mb_strlen($item);
        }

        return strlen($item);
    }

    /**
     * Parses the preload element passed and sets the necessary link headers
     * @todo find a better way to handle this
     *
     * @param mixed $parameters
     *
     * @return string
     */
    public function preload(mixed $parameters): string
    {

        $params = [];

        if (false === is_array($parameters)) {
            $params = [$parameters];
        } else {
            $params = $parameters;
        }

        /**
         * Grab the element
         */
        $href = $params[0];

        $container = $this->container;

        /**
         * Check if we have the response object in the container
         */
        if ($container->has("response")) {
            if (isset($params[1])) {
                $attributes = $params[1];
            } else {
                $attributes = ["as" => "style"];
            }

            /**
             * href comes wrapped with ''. Remove them
             */
            $response = $container->get("response");
            $link     = new Link(
                "preload",
                str_replace("'", "", $href),
                $attributes
            );
            $header   = "Link: " . (new Header())->serialize([$link]);

            $response->setRawHeader($header);
        }

        return $href;
    }

    /**
     * Renders a view using the template engine
     *
     * @param string path
     * @param mixed params
     * @params bool mustClean
     *
     * @return void
     */
    public function render(?string $path, mixed $params, bool $mustClean = false) // TODO: Make params array
    {

        if ($mustClean) {
            ob_clean();
        }

        /**
         * The compilation process is done by Phalcon\Mvc\View\Engine\Volt\Compiler
         */
        $compiler      = $this->getCompiler();
        $eventsManager = $this->eventsManager;

        if (true === is_object($eventsManager)) {
            if ($eventsManager->fire("view:beforeCompile", $this) === false) {
                return null;
            }
        }

        $compiler->compile($path);

        if (true === is_array($eventsManager)) {
            if ($eventsManager->fire("view:afterCompile", $this) === false) {
                return null;
            }
        }

        $compiledTemplatePath = $compiler->getCompiledTemplatePath();

        /**
         * Export the variables the current symbol table
         */
        if (true === is_array($params)) {
            foreach ($params as $key => $value) {
                $$key = $value;
            }
        }

        require $compiledTemplatePath;

        if ($mustClean) {
            $this->view->setContent(ob_get_contents());
        }
    }

    /**
     * Sets the events manager
     *
     * @param ManagerInterface eventsManager
     *
     * @return void
     */
    public function setEventsManager(ManagerInterface $eventsManager): void
    {
        $this->eventsManager = $eventsManager;
    }

    /**
     * Set Volt's options
     *
     * @param array options
     *
     * @return void
     */
    public function setOptions(?array $options): void
    {
        $this->options = $options;
    }

    /**
     * Extracts a slice from a string/array/traversable object value
     *
     * @param mixed value
     * @param int start
     * @param mixed end
     *
     * @return string
     */
    public function slice(mixed $value, int $start = 0, mixed $end = null): string
    {
        /**
         * Objects must implement a Traversable interface
         */
        if (true === is_object($value)) {
            if ($end === null) {
                $end = count($value) - 1;
            }

            $position = 0;
            $slice = [];

            $value->rewind();

            while ($value->valid()) {
                if ($position >= $start && $position <= $end) {
                    $slice[] = $value->current();
                }

                $value->next();

                $position++;
            }

            return $slice;
        }

        /**
         * Calculate the slice length
         */
        if ($end !== null) {
            $length = ($end - $start) + 1;
        } else {
            $length = null;
        }

        /**
         * Use array_slice on arrays
         */
        if (true === is_array($value)) {
            return array_slice($value, $start, $length);
        }

        /**
         * Use mb_substr if available
         */
        if (function_exists("mb_substr")) {
            if ($length !== null) {
                return mb_substr($value, $start, $length);
            }

            return mb_substr($value, $start);
        }

        /**
         * Use the standard substr function
         */
        if ($length !== null) {
            return substr($value, $start, $length);
        }

        return substr($value, $start);
    }

    /**
     * Sorts an array
     *
     * @param array $value
     *
     * @return array
     */
    public function sort(array $value): array
    {
        asort($value);

        return $value;
    }
}
