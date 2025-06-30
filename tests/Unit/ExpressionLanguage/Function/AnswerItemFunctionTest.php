<?php

namespace DiyFormBundle\Tests\Unit\ExpressionLanguage\Function;

use DiyFormBundle\ExpressionLanguage\Function\AnswerItemFunction;
use PHPUnit\Framework\TestCase;

class AnswerItemFunctionTest extends TestCase
{
    public function testClass_存在()
    {
        $this->assertTrue(class_exists(AnswerItemFunction::class));
    }
}