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

namespace Phalcon\Tests\Unit\Assets\Asset\Css;

use Phalcon\Assets\Asset\Css;
use Phalcon\Tests\Fixtures\Traits\AssetsTrait;
use Phalcon\Tests\AbstractUnitTestCase;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;

final class GetSetTypeTest extends AbstractUnitTestCase
{
    use AssetsTrait;

    /**
     * Tests Phalcon\Assets\Asset\Css :: getType()/setType()
     *
     * @return void
     *
     * @author       Phalcon Team <team@phalcon.io>
     * @since        2020-09-09
     */
    #[DataProvider('providerCss')]
    public function testAssetsAssetCssGetSetType(
        string $path,
        bool $local
    ): void {
        $asset = new Css($path, $local);

        $expected = 'css';
        $actual   = $asset->getType();
        $this->assertSame($expected, $actual);
    }
}
