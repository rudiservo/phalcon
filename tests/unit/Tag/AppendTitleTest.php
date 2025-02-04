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

namespace Phalcon\Tests\Unit\Tag;

use Phalcon\Tag;
use PHPUnit\Framework\Attributes\Test;

class AppendTitleTest extends AbstractTagSetup
{
    /**
     * Tests Phalcon\Tag :: appendTitle()
     *
     * @author Phalcon Team <team@phalcon.io>
     * @since  2012-09-05
     */
    public function testTagAppendTitle(): void
    {
        Tag::resetInput();

        Tag::setTitle('Title');

        Tag::appendTitle('Class');

        $this->assertSame(
            'Title',
            Tag::getTitle(false, false)
        );

        $this->assertSame(
            'TitleClass',
            Tag::getTitle(false, true)
        );

        $this->assertSame(
            '<title>TitleClass</title>' . PHP_EOL,
            Tag::renderTitle()
        );
    }

    /**
     * Tests Phalcon\Tag :: appendTitle() - array
     *
     * @author Phalcon Team <team@phalcon.io>
     * @since  2012-09-05
     */
    public function testTagAppendTitleArray(): void
    {
        Tag::resetInput();

        Tag::setTitle('Main');
        Tag::setTitleSeparator(' - ');

        Tag::appendTitle(['Category', 'Title']);

        $this->assertSame(
            'Main',
            Tag::getTitle(false, false)
        );

        $this->assertSame(
            'Main - Category - Title',
            Tag::getTitle(false, true)
        );

        $this->assertSame(
            '<title>Main - Category - Title</title>' . PHP_EOL,
            Tag::renderTitle()
        );
    }

    /**
     * Tests Phalcon\Tag :: appendTitle() - double call
     *
     * @author Phalcon Team <team@phalcon.io>
     * @since  2012-09-05
     */
    public function testTagAppendTitleDoubleCall(): void
    {
        Tag::resetInput();

        Tag::setTitle('Main');
        Tag::setTitleSeparator(' - ');

        Tag::appendTitle('Category');
        Tag::appendTitle('Title');

        $this->assertSame(
            'Main',
            Tag::getTitle(false, false)
        );

        $this->assertSame(
            'Main - Category - Title',
            Tag::getTitle(false, true)
        );

        $this->assertSame(
            '<title>Main - Category - Title</title>' . PHP_EOL,
            Tag::renderTitle()
        );
    }

    /**
     * Tests Phalcon\Tag :: appendTitle() - empty array
     *
     * @author Phalcon Team <team@phalcon.io>
     * @since  2012-09-05
     */
    public function testTagAppendTitleEmptyArray(): void
    {
        Tag::resetInput();

        Tag::setTitle('Main');
        Tag::setTitleSeparator(' - ');

        Tag::appendTitle('Category');
        Tag::appendTitle([]);

        $this->assertSame(
            'Main',
            Tag::getTitle(false, false)
        );

        $this->assertSame(
            'Main',
            Tag::getTitle(false, true)
        );

        $this->assertSame(
            '<title>Main</title>' . PHP_EOL,
            Tag::renderTitle()
        );
    }

    /**
     * Tests Phalcon\Tag :: appendTitle() - separator
     *
     * @author Phalcon Team <team@phalcon.io>
     * @since  2012-09-05
     */
    public function testTagAppendTitleSeparator(): void
    {
        Tag::resetInput();

        Tag::setTitle('Title');
        Tag::setTitleSeparator('|');

        Tag::appendTitle('Class');

        $this->assertSame(
            'Title',
            Tag::getTitle(false, false)
        );

        $this->assertSame(
            'Title|Class',
            Tag::getTitle(false, true)
        );

        $this->assertSame(
            '<title>Title|Class</title>' . PHP_EOL,
            Tag::renderTitle()
        );
    }
}
