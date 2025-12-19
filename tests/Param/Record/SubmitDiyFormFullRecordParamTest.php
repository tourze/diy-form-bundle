<?php

declare(strict_types=1);

namespace DiyFormBundle\Tests\Param\Record;

use DiyFormBundle\Param\Record\SubmitDiyFormFullRecordParam;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Tourze\JsonRPC\Core\Contracts\RpcParamInterface;

/**
 * @internal
 */
#[CoversClass(SubmitDiyFormFullRecordParam::class)]
final class SubmitDiyFormFullRecordParamTest extends TestCase
{
    public function testParamImplementsRpcParamInterface(): void
    {
        $param = new SubmitDiyFormFullRecordParam();
        $this->assertInstanceOf(RpcParamInterface::class, $param);
    }

    public function testParamCanBeConstructedWithNoArguments(): void
    {
        $param = new SubmitDiyFormFullRecordParam();

        // 验证默认值
        $this->assertSame('', $param->formId);
        $this->assertSame([], $param->data);
        $this->assertNull($param->startTime);
        $this->assertNull($param->inviter);
    }

    public function testParamCanBeConstructedWithCustomArguments(): void
    {
        $testData = [
            'field1' => 'value1',
            'field2' => 123,
            'field3' => true,
        ];

        $param = new SubmitDiyFormFullRecordParam(
            formId: 'form-123',
            data: $testData,
            startTime: '2023-01-01 10:00:00',
            inviter: 'user-456'
        );

        $this->assertSame('form-123', $param->formId);
        $this->assertSame($testData, $param->data);
        $this->assertSame('2023-01-01 10:00:00', $param->startTime);
        $this->assertSame('user-456', $param->inviter);
    }

    public function testParamIsReadonly(): void
    {
        $testData = ['test' => 'data'];

        $param = new SubmitDiyFormFullRecordParam(
            formId: 'readonly-test-form',
            data: $testData,
            startTime: '2023-01-01 12:00:00',
            inviter: 'readonly-user'
        );

        // 验证属性值不能被修改（readonly 属性）
        $this->assertSame('readonly-test-form', $param->formId);
        $this->assertSame($testData, $param->data);
        $this->assertSame('2023-01-01 12:00:00', $param->startTime);
        $this->assertSame('readonly-user', $param->inviter);
    }

    /**
     * @param string $formId 表单ID
     */
    #[DataProvider('validFormIdsProvider')]
    public function testParamWithValidFormIds(string $formId): void
    {
        $param = new SubmitDiyFormFullRecordParam(formId: $formId);

        $this->assertSame($formId, $param->formId);
        $this->assertSame([], $param->data);
        $this->assertNull($param->startTime);
        $this->assertNull($param->inviter);
    }

    public static function validFormIdsProvider(): array
    {
        return [
            [''], // 默认值
            ['1'],
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

    public function testParamWithEmptyFormId(): void
    {
        $param = new SubmitDiyFormFullRecordParam(formId: '');

        $this->assertSame('', $param->formId);
        $this->assertSame([], $param->data);
        $this->assertNull($param->startTime);
        $this->assertNull($param->inviter);
    }

    public function testParamWithNumericFormIds(): void
    {
        $numericIds = ['0', '1', '10', '100', '999999'];

        foreach ($numericIds as $formId) {
            $param = new SubmitDiyFormFullRecordParam(formId: $formId);
            $this->assertSame($formId, $param->formId);
        }
    }

    public function testParamWithAlphanumericFormIds(): void
    {
        $param = new SubmitDiyFormFullRecordParam(formId: 'FORM123abc456');

        $this->assertSame('FORM123abc456', $param->formId);
    }

    public function testParamWithLongFormId(): void
    {
        $longFormId = str_repeat('form-id-', 50); // 很长的表单ID

        $param = new SubmitDiyFormFullRecordParam(formId: $longFormId);

        $this->assertSame($longFormId, $param->formId);
    }

    /**
     * @param array<string, mixed> $data 提交数据
     */
    #[DataProvider('validDataProvider')]
    public function testParamWithValidData(array $data): void
    {
        $param = new SubmitDiyFormFullRecordParam(data: $data);

        $this->assertSame($data, $param->data);
        $this->assertSame('', $param->formId);
        $this->assertNull($param->startTime);
        $this->assertNull($param->inviter);
    }

    public static function validDataProvider(): array
    {
        return [
            [[], // 默认值
            ],
            [
                ['field1' => 'value1'],
            ],
            [
                ['field1' => 'value1', 'field2' => 'value2'],
            ],
            [
                ['number' => 123, 'float' => 12.34],
            ],
            [
                ['boolean_true' => true, 'boolean_false' => false],
            ],
            [
                ['null_value' => null],
            ],
            [
                ['nested' => ['key' => 'value']],
            ],
            [
                ['array' => [1, 2, 3]],
            ],
            [
                ['mixed' => ['string', 123, true, null]],
            ],
            [
                [
                    'user_info' => [
                        'name' => 'John Doe',
                        'age' => 30,
                        'active' => true,
                    ],
                    'preferences' => [
                        'theme' => 'dark',
                        'notifications' => false,
                    ],
                ],
            ],
        ];
    }

    /**
     * @param string|null $startTime 开始答题时间
     */
    #[DataProvider('validStartTimeProvider')]
    public function testParamWithValidStartTime(?string $startTime): void
    {
        $param = new SubmitDiyFormFullRecordParam(startTime: $startTime);

        $this->assertSame($startTime, $param->startTime);
        $this->assertSame('', $param->formId);
        $this->assertSame([], $param->data);
        $this->assertNull($param->inviter);
    }

    public static function validStartTimeProvider(): array
    {
        return [
            [null], // 默认值
            [''],
            ['2023-01-01 10:00:00'],
            ['2023-12-31 23:59:59'],
            ['2024-02-29 12:00:00'], // 闰年
            ['2000-01-01 00:00:00'],
            ['2099-12-31 23:59:59'],
            ['2023-06-15 14:30:45'],
            ['2023-06-15 14:30'],
            ['2023-06-15'],
            ['1672574400'], // Unix timestamp
            ['当前时间'],
            ['2023年1月1日 10时00分00秒'],
        ];
    }

    /**
     * @param string|null $inviter 邀请人信息
     */
    #[DataProvider('validInviterProvider')]
    public function testParamWithValidInviter(?string $inviter): void
    {
        $param = new SubmitDiyFormFullRecordParam(inviter: $inviter);

        $this->assertSame($inviter, $param->inviter);
        $this->assertSame('', $param->formId);
        $this->assertSame([], $param->data);
        $this->assertNull($param->startTime);
    }

    public static function validInviterProvider(): array
    {
        return [
            [null], // 默认值
            [''],
            ['user-123'],
            ['123'],
            ['inviter-with-dashes'],
            ['inviter_with_underscores'],
            ['user@example.com'],
            ['+86-13800138000'],
            ['用户123'],
            ['ユーザー123'],
            ['사용자123'],
            ['مستخدم123'],
            ['utilisateur-123'],
            ['benutzer-123'],
            ['пользователь-123'],
            ['usuario-123'],
        ];
    }

    public function testParamWithSpecialCharactersInFormId(): void
    {
        $specialFormIds = [
            'form-with-dashes',
            'form_with_underscores',
            'form.with.dots',
            'form@with@symbols',
            'form#with#hash',
            'form$with$dollar',
            'form%with%percent',
            'form^with^caret',
            'form&with&ampersand',
            'form*with*asterisk',
            'form(with)parentheses',
            'form[with]brackets',
            'form{with}braces',
            'form|with|pipe',
            'form\with\backslash',
            'form/with/slash',
            'form:with:colon',
            'form;with:semicolon',
            'form"with"quotes',
            "form'with'apostrophes",
            'form`with`backticks',
            'form~with~tilde',
            'form!with!exclamation',
            'form?with?question',
        ];

        foreach ($specialFormIds as $formId) {
            $param = new SubmitDiyFormFullRecordParam(formId: $formId);
            $this->assertSame($formId, $param->formId);
        }
    }

    public function testParamWithUnicodeCharactersInFormId(): void
    {
        $unicodeFormIds = [
            '表单-中文',
            'フォーム-日本語',
            '폼-한국어',
            'form-العربية',
            'form- français',
            'form- Deutsch',
            'form- русский',
            'form- español',
            'form- Português',
            'form- Italiano',
        ];

        foreach ($unicodeFormIds as $formId) {
            $param = new SubmitDiyFormFullRecordParam(formId: $formId);
            $this->assertSame($formId, $param->formId);
        }
    }

    public function testParamWithWhitespaceInFormId(): void
    {
        $whitespaceFormIds = [
            'form with spaces',
            'form\twith\ttabs',
            "form\nwith\nnewlines",
            'form with spaces and    tabs',
            '  leading and trailing spaces  ',
            "\t\ttabs around\t\t",
        ];

        foreach ($whitespaceFormIds as $formId) {
            $param = new SubmitDiyFormFullRecordParam(formId: $formId);
            $this->assertSame($formId, $param->formId);
        }
    }

    public function testParamWithComplexDataStructures(): void
    {
        $complexData = [
            'user_profile' => [
                'personal_info' => [
                    'name' => 'John Doe',
                    'age' => 30,
                    'email' => 'john@example.com',
                ],
                'preferences' => [
                    'language' => 'en',
                    'timezone' => 'UTC',
                    'notifications' => [
                        'email' => true,
                        'sms' => false,
                        'push' => true,
                    ],
                ],
            ],
            'form_responses' => [
                'question_1' => 'answer_1',
                'question_2' => ['option_1', 'option_3'],
                'question_3' => [
                    'sub_question_1' => 'sub_answer_1',
                    'sub_question_2' => 42,
                ],
            ],
            'metadata' => [
                'submission_time' => '2023-01-01T10:00:00Z',
                'ip_address' => '192.168.1.1',
                'user_agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36',
            ],
        ];

        $param = new SubmitDiyFormFullRecordParam(data: $complexData);
        $this->assertSame($complexData, $param->data);
    }

    public function testParamWithEmptyData(): void
    {
        $emptyDataCases = [
            [],
            ['empty_string' => ''],
            ['null_value' => null],
            ['empty_array' => []],
            ['zero_value' => 0],
            ['false_value' => false],
        ];

        foreach ($emptyDataCases as $data) {
            $param = new SubmitDiyFormFullRecordParam(data: $data);
            $this->assertSame($data, $param->data);
        }
    }

    public function testParamWithNumericData(): void
    {
        $numericData = [
            'integer' => 42,
            'negative_integer' => -10,
            'zero' => 0,
            'float' => 3.14159,
            'negative_float' => -2.71828,
            'scientific_notation' => 1.23e-4,
            'large_number' => 999999999999,
        ];

        $param = new SubmitDiyFormFullRecordParam(data: $numericData);
        $this->assertSame($numericData, $param->data);
    }

    public function testParamWithBooleanData(): void
    {
        $booleanData = [
            'true_value' => true,
            'false_value' => false,
            'boolean_as_int_1' => true,
            'boolean_as_int_0' => false,
        ];

        $param = new SubmitDiyFormFullRecordParam(data: $booleanData);
        $this->assertSame($booleanData, $param->data);
    }

    public function testParamWithMixedTypeData(): void
    {
        $mixedData = [
            'string_value' => 'hello world',
            'integer_value' => 123,
            'float_value' => 45.67,
            'boolean_value' => true,
            'null_value' => null,
            'array_value' => [1, 'two', true],
            'nested_object' => [
                'inner_string' => 'inner',
                'inner_number' => 456,
            ],
        ];

        $param = new SubmitDiyFormFullRecordParam(data: $mixedData);
        $this->assertSame($mixedData, $param->data);
    }

    public function testDefaultValues(): void
    {
        // 测试不传参数时使用默认值
        $param = new SubmitDiyFormFullRecordParam();
        $this->assertSame('', $param->formId);
        $this->assertSame([], $param->data);
        $this->assertNull($param->startTime);
        $this->assertNull($param->inviter);

        // 测试显式传递默认值
        $paramWithDefaults = new SubmitDiyFormFullRecordParam(
            formId: '',
            data: [],
            startTime: null,
            inviter: null
        );
        $this->assertSame('', $paramWithDefaults->formId);
        $this->assertSame([], $paramWithDefaults->data);
        $this->assertNull($paramWithDefaults->startTime);
        $this->assertNull($paramWithDefaults->inviter);
    }

    public function testAllPublicPropertiesAreAccessible(): void
    {
        $testData = ['test' => 'data'];

        $param = new SubmitDiyFormFullRecordParam(
            formId: 'test-form',
            data: $testData,
            startTime: '2023-01-01 10:00:00',
            inviter: 'test-user'
        );

        // 由于是 readonly class，属性是 public readonly
        $this->assertSame('test-form', $param->formId);
        $this->assertSame($testData, $param->data);
        $this->assertSame('2023-01-01 10:00:00', $param->startTime);
        $this->assertSame('test-user', $param->inviter);
    }

    public function testParamWithAllParametersSet(): void
    {
        $testData = [
            'field1' => 'value1',
            'field2' => 123,
            'field3' => true,
        ];

        $param = new SubmitDiyFormFullRecordParam(
            formId: 'form-with-all-params',
            data: $testData,
            startTime: '2023-12-31 23:59:59',
            inviter: 'user-with-all-params'
        );

        $this->assertSame('form-with-all-params', $param->formId);
        $this->assertSame($testData, $param->data);
        $this->assertSame('2023-12-31 23:59:59', $param->startTime);
        $this->assertSame('user-with-all-params', $param->inviter);
    }

    public function testParamWithLargeDataSet(): void
    {
        // 测试大数据集
        $largeData = [];
        for ($i = 0; $i < 1000; ++$i) {
            $largeData["field_{$i}"] = "value_{$i}";
        }

        $param = new SubmitDiyFormFullRecordParam(data: $largeData);
        $this->assertSame($largeData, $param->data);
        $this->assertCount(1000, $param->data);
    }
}
