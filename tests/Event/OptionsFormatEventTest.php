<?php

declare(strict_types=1);

namespace DiyFormBundle\Tests\Event;

use DiyFormBundle\Entity\Option;
use DiyFormBundle\Event\OptionsFormatEvent;
use PHPUnit\Framework\Attributes\CoversClass;
use Tourze\PHPUnitSymfonyUnitTest\AbstractEventTestCase;

/**
 * @internal
 */
#[CoversClass(OptionsFormatEvent::class)]
final class OptionsFormatEventTest extends AbstractEventTestCase
{
    public function testResultProperty(): void
    {
        $event = new OptionsFormatEvent();
        $this->assertSame([], $event->getResult());

        $result = ['formatted' => true, 'data' => ['test']];
        $event->setResult($result);
        $this->assertSame($result, $event->getResult());
    }

    public function testOptionsProperty(): void
    {
        $event = new OptionsFormatEvent();

        // 创建实际的Option实体对象用于测试
        $option1 = new Option();
        $option1->setText('Option 1');

        $option2 = new Option();
        $option2->setText('Option 2');

        $options = [$option1, $option2];
        $event->setOptions($options);
        $this->assertSame($options, $event->getOptions());
    }
}
