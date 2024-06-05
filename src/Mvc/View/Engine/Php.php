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
 * Adapter to use PHP itself as templating engine
 */
class Php extends AbstractEngine
{
    /**
     * Renders a view using the template engine
     *
     * @param string $path
     * @param array $params
     * @param bool $mustClean
     */
    public function render(?string $path, mixed $params, bool $mustClean = false): void
    {
        if ($mustClean) {
            ob_clean();
        }

        /**
         * Create the variables in local symbol table
         */
        if (true === is_array($params)) {
            foreach ($params as $key => $value) {
                $$key = $value;
            }
        }

        /**
         * Require the file
         */
        require $path;

        if ($mustClean) {
            $this->view->setContent(ob_get_contents());
        }
    }
}
