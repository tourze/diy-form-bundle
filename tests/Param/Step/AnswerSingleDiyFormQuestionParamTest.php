<?php

declare(strict_types=1);

namespace DiyFormBundle\Tests\Param\Step;

use DiyFormBundle\Param\Step\AnswerSingleDiyFormQuestionParam;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Tourze\JsonRPC\Core\Contracts\RpcParamInterface;

/**
 * @internal
 */
#[CoversClass(AnswerSingleDiyFormQuestionParam::class)]
final class AnswerSingleDiyFormQuestionParamTest extends TestCase
{
    public function testParamImplementsRpcParamInterface(): void
    {
        $param = new AnswerSingleDiyFormQuestionParam();
        $this->assertInstanceOf(RpcParamInterface::class, $param);
    }

    public function testParamCanBeConstructedWithNoArguments(): void
    {
        $param = new AnswerSingleDiyFormQuestionParam();

        // 验证默认值
        $this->assertSame('2', $param->formId);
        $this->assertSame(0, $param->recordId);
        $this->assertSame(0, $param->fieldId);
        $this->assertSame('', $param->input);
        $this->assertFalse($param->skip);
    }

    public function testParamCanBeConstructedWithCustomArguments(): void
    {
        $param = new AnswerSingleDiyFormQuestionParam(
            formId: 'custom-form-123',
            recordId: 456,
            fieldId: 789,
            input: 'test answer',
            skip: true
        );

        $this->assertSame('custom-form-123', $param->formId);
        $this->assertSame(456, $param->recordId);
        $this->assertSame(789, $param->fieldId);
        $this->assertSame('test answer', $param->input);
        $this->assertTrue($param->skip);
    }

    public function testParamIsReadonly(): void
    {
        $param = new AnswerSingleDiyFormQuestionParam(
            formId: 'readonly-form',
            recordId: 123,
            fieldId: 456,
            input: 'readonly-input',
            skip: true
        );

        // 验证属性值不能被修改（readonly 属性）
        $this->assertSame('readonly-form', $param->formId);
        $this->assertSame(123, $param->recordId);
        $this->assertSame(456, $param->fieldId);
        $this->assertSame('readonly-input', $param->input);
        $this->assertTrue($param->skip);
    }

    /**
     * @param string $formId 表单ID
     */
    #[DataProvider('validFormIdsProvider')]
    public function testParamWithValidFormIds(string $formId): void
    {
        $param = new AnswerSingleDiyFormQuestionParam(
            formId: $formId,
            recordId: 123,
            fieldId: 456,
            input: 'test'
        );

        $this->assertSame($formId, $param->formId);
    }

    public static function validFormIdsProvider(): array
    {
        return [
            ['1'],
            ['2'], // 默认值
            ['123'],
            ['abc'],
            ['form-123'],
            ['FORM_WITH_UNDERSCORES'],
            ['form-with-dashes'],
            ['123456789'],
            ['uuid-like-string'],
            ['form_特殊字符'],
        ];
    }

    /**
     * @param int $recordId 记录ID
     */
    #[DataProvider('validRecordIdsProvider')]
    public function testParamWithValidRecordIds(int $recordId): void
    {
        $param = new AnswerSingleDiyFormQuestionParam(
            formId: '2',
            recordId: $recordId
        );

        $this->assertSame($recordId, $param->recordId);
    }

    public static function validRecordIdsProvider(): array
    {
        return [
            [0], // 默认值
            [1],
            [10],
            [100],
            [999],
            [999999],
            [PHP_INT_MAX],
        ];
    }

    /**
     * @param int $fieldId 题目/字段ID
     */
    #[DataProvider('validFieldIdsProvider')]
    public function testParamWithValidFieldIds(int $fieldId): void
    {
        $param = new AnswerSingleDiyFormQuestionParam(
            formId: '2',
            recordId: 0,
            fieldId: $fieldId
        );

        $this->assertSame($fieldId, $param->fieldId);
    }

    public static function validFieldIdsProvider(): array
    {
        return [
            [0], // 默认值
            [1],
            [10],
            [100],
            [999],
            [999999],
            [PHP_INT_MAX],
        ];
    }

    /**
     * @param string|array<int, mixed>|int $input 输入值
     */
    #[DataProvider('validInputsProvider')]
    public function testParamWithValidInputs(string|array|int $input): void
    {
        $param = new AnswerSingleDiyFormQuestionParam(
            formId: '2',
            recordId: 0,
            fieldId: 0,
            input: $input
        );

        $this->assertSame($input, $param->input);
    }

    public static function validInputsProvider(): array
    {
        return [
            [''], // 默认值
            ['simple text'],
            ['text with spaces'],
            ['123'],
            ['special-chars!@#$%'],
            ['中文输入'],
            ['日本語入力'],
            ['한국어 입력'],
            ['العربية'],
            [123],
            [0],
            [-1],
            [999999],
            [1, 2, 3], // 数组
            ['option1', 'option2', 'option3'], // 字符串数组
            [100, 200, 300], // 整数数组
            ['mixed'], // mixed type as string
        ];
    }

    #[DataProvider('validSkipValuesProvider')]
    public function testParamWithValidSkipValues(bool $skip): void
    {
        $param = new AnswerSingleDiyFormQuestionParam(
            formId: '2',
            recordId: 0,
            fieldId: 0,
            input: 'test',
            skip: $skip
        );

        $this->assertSame($skip, $param->skip);
    }

    public static function validSkipValuesProvider(): array
    {
        return [
            [false], // 默认值
            [true],
        ];
    }

    public function testParamWithEmptyFormId(): void
    {
        $param = new AnswerSingleDiyFormQuestionParam(
            formId: '',
            recordId: 123
        );

        $this->assertSame('', $param->formId);
    }

    public function testParamWithZeroValues(): void
    {
        $param = new AnswerSingleDiyFormQuestionParam(
            formId: '0',
            recordId: 0,
            fieldId: 0,
            input: '0'
        );

        $this->assertSame('0', $param->formId);
        $this->assertSame(0, $param->recordId);
        $this->assertSame(0, $param->fieldId);
        $this->assertSame('0', $param->input);
        $this->assertFalse($param->skip);
    }

    public function testParamWithNegativeIds(): void
    {
        $param = new AnswerSingleDiyFormQuestionParam(
            formId: '-1',
            recordId: -1,
            fieldId: -1
        );

        $this->assertSame('-1', $param->formId);
        $this->assertSame(-1, $param->recordId);
        $this->assertSame(-1, $param->fieldId);
    }

    public function testParamWithArrayInput(): void
    {
        $arrayInput = ['option1', 'option2', 'option3'];
        $param = new AnswerSingleDiyFormQuestionParam(
            formId: '2',
            recordId: 0,
            fieldId: 0,
            input: $arrayInput
        );

        $this->assertSame($arrayInput, $param->input);
        $this->assertIsArray($param->input);
    }

    public function testParamWithIntegerInput(): void
    {
        $integerInput = 12345;
        $param = new AnswerSingleDiyFormQuestionParam(
            formId: '2',
            recordId: 0,
            fieldId: 0,
            input: $integerInput
        );

        $this->assertSame($integerInput, $param->input);
        $this->assertIsInt($param->input);
    }

    public function testParamWithLongFormId(): void
    {
        $longFormId = str_repeat('form-id-', 50); // 很长的表单ID

        $param = new AnswerSingleDiyFormQuestionParam(
            formId: $longFormId
        );

        $this->assertSame($longFormId, $param->formId);
    }

    public function testParamWithSpecialCharactersInFormId(): void
    {
        $specialFormIds = [
            'form-with-dashes',
            'form_with_underscores',
            'form.with.dots',
            'form@with@symbols',
            'form#with#hash',
        ];

        foreach ($specialFormIds as $formId) {
            $param = new AnswerSingleDiyFormQuestionParam(
                formId: $formId
            );
            $this->assertSame($formId, $param->formId);
        }
    }

    public function testDefaultValues(): void
    {
        // 测试不传参数时使用默认值
        $param = new AnswerSingleDiyFormQuestionParam();
        $this->assertSame('2', $param->formId);
        $this->assertSame(0, $param->recordId);
        $this->assertSame(0, $param->fieldId);
        $this->assertSame('', $param->input);
        $this->assertFalse($param->skip);

        // 测试显式传递默认值
        $paramWithDefaults = new AnswerSingleDiyFormQuestionParam(
            formId: '2',
            recordId: 0,
            fieldId: 0,
            input: '',
            skip: false
        );
        $this->assertSame('2', $paramWithDefaults->formId);
        $this->assertSame(0, $paramWithDefaults->recordId);
        $this->assertSame(0, $paramWithDefaults->fieldId);
        $this->assertSame('', $paramWithDefaults->input);
        $this->assertFalse($paramWithDefaults->skip);
    }

    public function testParamWithUnicodeCharacters(): void
    {
        $unicodeValues = [
            '表单-中文',
            'フォーム-日本語',
            '폼-한국어',
            'form-العربية',
            'form- français',
        ];

        foreach ($unicodeValues as $formId) {
            $param = new AnswerSingleDiyFormQuestionParam(
                formId: $formId,
                input: $formId
            );
            $this->assertSame($formId, $param->formId);
            $this->assertSame($formId, $param->input);
        }
    }
}
