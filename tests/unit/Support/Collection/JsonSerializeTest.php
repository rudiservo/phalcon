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

namespace Phalcon\Tests\Unit\Support\Collection;

use Phalcon\Support\Collection;
use Phalcon\Tests\Fixtures\Support\Collection\JsonFixture;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;

final class JsonSerializeTest extends AbstractCollectionTestCase
{
    /**
     * Tests Phalcon\Support\Collection :: jsonSerialize()
     *
     * @return void
     *
     * @author Phalcon Team <team@phalcon.io>
     * @since  2020-09-09
     */
    #[DataProvider('getClasses')]
    public function testSupportCollectionJsonSerialize(): void
    {
        $data = $this->getData();
        $collection = new Collection($data);

        $expected = $data;
        $actual   = $collection->jsonSerialize();
        $this->assertSame($expected, $actual);

        $data = [
            'one'    => 'two',
            'three'  => 'four',
            'five'   => 'six',
            'object' => new JsonFixture(),
        ];

        $expected = [
            'one'    => 'two',
            'three'  => 'four',
            'five'   => 'six',
            'object' => [
                'one' => 'two',
            ],
        ];

        $collection = new Collection($data);

        $actual = $collection->jsonSerialize();
        $this->assertSame($expected, $actual);
    }
}
