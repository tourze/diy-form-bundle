<?php

namespace DiyFormBundle\Tests\Unit\ExpressionLanguage\Function;

use DiyFormBundle\ExpressionLanguage\Function\AnswerItemCountFunction;
use PHPUnit\Framework\TestCase;

class AnswerItemCountFunctionTest extends TestCase
{
    public function testClass_存在()
    {
        $this->assertTrue(class_exists(AnswerItemCountFunction::class));
    }
}