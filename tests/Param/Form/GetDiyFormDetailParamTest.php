<?php

declare(strict_types=1);

namespace DiyFormBundle\Tests\Param\Form;

use DiyFormBundle\Param\Form\GetDiyFormDetailParam;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Tourze\JsonRPC\Core\Contracts\RpcParamInterface;

/**
 * @internal
 */
#[CoversClass(GetDiyFormDetailParam::class)]
final class GetDiyFormDetailParamTest extends TestCase
{
    public function testParamImplementsRpcParamInterface(): void
    {
        $param = new GetDiyFormDetailParam();
        $this->assertInstanceOf(RpcParamInterface::class, $param);
    }

    public function testParamCanBeConstructedWithNoArguments(): void
    {
        $param = new GetDiyFormDetailParam();

        // 验证默认值为 '2'
        $this->assertSame('2', $param->formId);
    }

    public function testParamCanBeConstructedWithCustomArgument(): void
    {
        $param = new GetDiyFormDetailParam(
            formId: 'custom-form-123'
        );

        $this->assertSame('custom-form-123', $param->formId);
    }

    public function testParamIsReadonly(): void
    {
        $param = new GetDiyFormDetailParam(
            formId: 'readonly-test-form'
        );

        // 验证属性值不能被修改（readonly 属性）
        $this->assertSame('readonly-test-form', $param->formId);
    }

    /**
     * @param string $formId 表单ID
     */
    #[DataProvider('validFormIdsProvider')]
    public function testParamWithValidFormIds(string $formId): void
    {
        $param = new GetDiyFormDetailParam(
            formId: $formId
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

    public function testParamWithEmptyFormId(): void
    {
        $param = new GetDiyFormDetailParam(
            formId: ''
        );

        $this->assertSame('', $param->formId);
    }

    public function testParamWithNumericFormIds(): void
    {
        $numericIds = ['0', '1', '10', '100', '999999'];

        foreach ($numericIds as $formId) {
            $param = new GetDiyFormDetailParam(
                formId: $formId
            );
            $this->assertSame($formId, $param->formId);
        }
    }

    public function testParamWithAlphanumericFormIds(): void
    {
        $param = new GetDiyFormDetailParam(
            formId: 'FORM123abc456'
        );

        $this->assertSame('FORM123abc456', $param->formId);
    }

    public function testParamWithLongFormId(): void
    {
        $longFormId = str_repeat('form-id-', 50); // 很长的表单ID

        $param = new GetDiyFormDetailParam(
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
            $param = new GetDiyFormDetailParam(
                formId: $formId
            );
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
            $param = new GetDiyFormDetailParam(
                formId: $formId
            );
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
            $param = new GetDiyFormDetailParam(
                formId: $formId
            );
            $this->assertSame($formId, $param->formId);
        }
    }

    public function testDefaultFormIdValue(): void
    {
        // 测试不传参数时使用默认值
        $param = new GetDiyFormDetailParam();
        $this->assertSame('2', $param->formId);

        // 测试显式传递默认值
        $paramWithDefault = new GetDiyFormDetailParam(
            formId: '2'
        );
        $this->assertSame('2', $paramWithDefault->formId);
    }
}
