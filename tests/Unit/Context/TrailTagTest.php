<?php

declare(strict_types=1);

namespace j45l\either\Test\Unit\Context;

use j45l\either\Context\Trail;
use j45l\either\None;
use j45l\either\Result\Failure;
use j45l\either\Result\Reason;
use j45l\either\Some;
use j45l\either\Tags\TagCreator;
use j45l\either\Tags\Untagged;
use PHPUnit\Framework\TestCase;

use function Functional\invoke;

/**
 * @covers \j45l\either\Context\Trail
 * @covers \j45l\either\Tags\StringTag
 * @covers \j45l\either\Tags\Untagged
 */
final class TrailTagTest extends TestCase
{
    public function testCanPushEitherTagged(): void
    {
        $trail = Trail::create()
            ->push(Some::from(42), TagCreator::from('42'))
            ->push(Some::from(43), TagCreator::from('43'))
        ;

        self::assertEquals(['42' => 42, '43' => 43], $trail->taggedValues());
        self::assertEquals(['42' => Some::from('42'), '43' => Some::from('43')], $trail->tagged());
    }

    public function testCanPushEitherUnTagged(): void
    {
        $trail = Trail::create()
            ->push(Some::from(42), TagCreator::from('42'))
            ->push(Some::from(43), TagCreator::from('43'))
            ->push(Some::from(44), new Untagged())
        ;

        self::assertEquals(['42' => 42, '43' => 43], $trail->taggedValues());
        self::assertEquals(['42' => Some::from('42'), '43' => Some::from('43')], $trail->tagged());
    }

    public function testTaggedValuesJustForSome(): void
    {
        $trail = Trail::create()
            ->push(Some::from(42), TagCreator::from('42'))
            ->push(None::create(), TagCreator::from('43'))
        ;

        self::assertEquals(['42' => 42], $trail->taggedValues());
        self::assertEquals(['42' => Some::from('42'), '43' => None::create()], $trail->tagged());
    }

    public function testCreatingAnEmptyTagResultsInAnUntagged(): void
    {
        self::assertInstanceOf(Untagged::class, TagCreator::from(''));
    }

    public function testCanPushTaggedEither(): void
    {
        $trail = Trail::create()
            ->push(Some::from(42), TagCreator::from('42'))
            ->push(Some::from(43), TagCreator::from('43'))
        ;

        self::assertEquals(['42' => 42, '43' => 43], $trail->taggedValues());
        self::assertEquals(['42' => Some::from('42'), '43' => Some::from('43')], $trail->tagged());
    }

    public function testTaggedFailuresReasons(): void
    {
        $trail = Trail::create()
            ->push(Some::from(42), TagCreator::from('42'))
            ->push(Failure::from(Reason::from('because failed')), TagCreator::from('43'))
        ;

        self::assertEquals(
            ['43' => 'because failed'],
            invoke($trail->taggedFailureReasons(), 'toString')
        );
        self::assertEquals(
            ['42' => Some::from('42'), '43' => Failure::from(Reason::from('because failed'))],
            $trail->tagged()
        );
    }
}
