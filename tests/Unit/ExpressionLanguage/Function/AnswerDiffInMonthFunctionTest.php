<?php

namespace DiyFormBundle\Tests\Unit\ExpressionLanguage\Function;

use DiyFormBundle\ExpressionLanguage\Function\AnswerDiffInMonthFunction;
use PHPUnit\Framework\TestCase;

class AnswerDiffInMonthFunctionTest extends TestCase
{
    public function testClass_存在()
    {
        $this->assertTrue(class_exists(AnswerDiffInMonthFunction::class));
    }
}