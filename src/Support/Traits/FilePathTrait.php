<?php

/**
 * This file is part of the Phalcon.
 *
 * (c) Phalcon Team <team@phalcon.io>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Phalcon\Support\Traits;

use function str_replace;
use function hash;

trait FilePathTrait
{
    /**
     * Returns a prepared virtual path based on the original path
     *
     * @param string $key
     *
     * @return string
     */
    public function prepareVirtualPath(string $key): string
    {
        return str_replace(['/', '\\', ':'], '_', $key);
    }

    /**
     * Returns a unique key for the path
     *
     * @param string $path
     *
     * @return string
     */
    public function uniquePathKey(string $path): string
    {
        return hash('xxh3', $path);
    }
}
