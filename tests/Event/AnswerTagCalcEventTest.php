<?php

declare(strict_types=1);

namespace DiyFormBundle\Tests\Event;

use DiyFormBundle\Event\AnswerTagCalcEvent;
use PHPUnit\Framework\Attributes\CoversClass;
use Tourze\PHPUnitSymfonyUnitTest\AbstractEventTestCase;

/**
 * @internal
 */
#[CoversClass(AnswerTagCalcEvent::class)]
final class AnswerTagCalcEventTest extends AbstractEventTestCase
{
    public function testAnswerTagsCanBeSetAndRetrieved(): void
    {
        $event = new AnswerTagCalcEvent();
        // answerTags是标签字符串列表，不是关联数组
        $tags = ['tag1', 'tag2', 'category1', 'status'];

        $event->setAnswerTags($tags);
        $this->assertSame($tags, $event->getAnswerTags());
    }
}
