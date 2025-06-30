<?php

namespace DiyFormBundle\Tests\Unit\ExpressionLanguage\Function;

use DiyFormBundle\ExpressionLanguage\Function\AnswerTagIncludeFunction;
use PHPUnit\Framework\TestCase;

class AnswerTagIncludeFunctionTest extends TestCase
{
    public function testClass_存在()
    {
        $this->assertTrue(class_exists(AnswerTagIncludeFunction::class));
    }
}