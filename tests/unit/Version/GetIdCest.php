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

namespace Phalcon\Tests\Unit\Version;

use Phalcon\Tests\Fixtures\Traits\VersionTrait;
use Phalcon\Version\Version;
use UnitTester;

use function is_string;

/**
 * Class GetIdCest
 *
 * @package Phalcon\Tests\Unit\Version
 */
class GetIdCest
{
    use VersionTrait;

    /**
     * Tests Phalcon\Version :: getId()
     *
     * @param UnitTester $I
     *
     * @author Phalcon Team <team@phalcon.io>
     * @since  2020-09-09
     */
    public function versionGetId(UnitTester $I)
    {
        $I->wantToTest('Version - getId()');

        $I->assertTrue(is_string(Version::getId()));
    }

    /**
     * Tests the get() translation to getId()
     *
     * @param UnitTester $I
     *
     * @author Phalcon Team <team@phalcon.io>
     * @since  2020-09-09
     */
    public function versionGetToGetId(UnitTester $I)
    {
        $I->wantToTest('Version - get() to getId()');

        $version = Version::get();
        $chunks  = explode('-', $version);

        $special   = '4';
        $specialNo = '0';

        // There are pre-release version parts (eg. 4.0.0-alpha.2)
        if (count($chunks) > 1) {
            if (false === strpos($chunks[1], '.')) { // 4.0.0-alpha
                $special = $this->specialToNumber(
                    $chunks[1]
                );
            } else { // 4.0.0-alpha.2
                $specialNo = substr(
                    $chunks[1],
                    strpos($chunks[1], '.') + 1
                );

                $special = $this->specialToNumber(
                    substr(
                        $chunks[1],
                        0,
                        strpos(
                            $chunks[1],
                            '.'
                        )
                    )
                );
            }
        }

        // Now the version itself
        $verChunks = explode('.', $chunks[0]);
        $major     = intval($verChunks[0]);
        $med       = substr('00' . intval($verChunks[1]), -2);
        $min       = substr('00' . intval($verChunks[2]), -2);

        $expected = "{$major}{$med}{$min}{$special}{$specialNo}";
        $actual   = Version::getId();
        $I->assertEquals($expected, $actual);
    }
}
