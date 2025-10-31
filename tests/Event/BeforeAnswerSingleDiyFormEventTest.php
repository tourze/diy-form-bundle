<?php

declare(strict_types=1);

namespace DiyFormBundle\Tests\Event;

use DiyFormBundle\Event\BeforeAnswerSingleDiyFormEvent;
use PHPUnit\Framework\Attributes\CoversClass;
use Tourze\PHPUnitSymfonyUnitTest\AbstractEventTestCase;

/**
 * @internal
 */
#[CoversClass(BeforeAnswerSingleDiyFormEvent::class)]
final class BeforeAnswerSingleDiyFormEventTest extends AbstractEventTestCase
{
    public function testInputProperty(): void
    {
        $event = new BeforeAnswerSingleDiyFormEvent();

        $input = ['answer' => 'test value'];
        $event->setInput($input);
        $this->assertSame($input, $event->getInput());

        $stringInput = 'test string';
        $event->setInput($stringInput);
        $this->assertSame($stringInput, $event->getInput());
    }
}
