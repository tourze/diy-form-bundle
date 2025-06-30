<?php

namespace DiyFormBundle\Tests\Unit\Procedure\Step;

use DiyFormBundle\Procedure\Step\AnswerSingleDiyFormQuestion;
use PHPUnit\Framework\TestCase;

class AnswerSingleDiyFormQuestionTest extends TestCase
{
    public function testClass_存在()
    {
        $this->assertTrue(class_exists(AnswerSingleDiyFormQuestion::class));
    }
}