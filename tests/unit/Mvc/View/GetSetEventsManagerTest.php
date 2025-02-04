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

namespace Phalcon\Tests\Unit\Mvc\View;

use Phalcon\Mvc\View;
use Phalcon\Tests\Fixtures\Traits\DiTrait;
use Phalcon\Tests\AbstractUnitTestCase;

class GetSetEventsManagerTest extends AbstractUnitTestCase
{
    use DiTrait;

    /**
     * Tests Phalcon\Mvc\View :: getEventsManager()/setEventsManager()
     *
     * @author Phalcon Team <team@phalcon.io>
     * @since  2018-11-13
     */
    public function testMvcViewGetSetEventsManager(): void
    {
        $manager = $this->newService('eventsManager');
        $view    = new View();

        $view->setEventsManager($manager);

        $actual = $view->getEventsManager();
        $this->assertEquals($manager, $actual);
    }
}
