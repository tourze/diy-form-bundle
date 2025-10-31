<?php

declare(strict_types=1);

namespace DiyFormBundle\Tests\Session;

use DiyFormBundle\Entity\Option;
use DiyFormBundle\Session\NextField;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 */
#[CoversClass(NextField::class)]
final class NextFieldTest extends TestCase
{
    protected function onSetUp(): void
    {
    }

    public function testSessionCanBeInstantiated(): void
    {
        $session = new NextField();
        $this->assertInstanceOf(NextField::class, $session);
    }

    public function testIndexProperty(): void
    {
        $session = new NextField();
        $session->setIndex(5);
        $this->assertSame(5, $session->getIndex());
    }

    public function testAddOptionMethod(): void
    {
        $session = new NextField();

        $option1 = $this->createMock(Option::class);
        $option1->method('getText')->willReturn('option1');

        $option2 = $this->createMock(Option::class);
        $option2->method('getText')->willReturn('option2');

        $session->addOption($option1);
        $session->addOption($option2);

        $options = $session->getOptions();
        $this->assertCount(2, $options);
        $this->assertInstanceOf(Option::class, $options[0]);
        $this->assertInstanceOf(Option::class, $options[1]);
    }

    public function testShowBackProperty(): void
    {
        $session = new NextField();
        $this->assertTrue($session->isShowBack()); // 默认为true

        $session->setShowBack(false);
        $this->assertFalse($session->isShowBack());
    }

    public function testOptionsProperty(): void
    {
        $session = new NextField();
        $this->assertSame([], $session->getOptions());

        $options = [
            $this->createMock(Option::class),
            $this->createMock(Option::class),
        ];
        $session->setOptions($options);
        $this->assertSame($options, $session->getOptions());
    }
}
