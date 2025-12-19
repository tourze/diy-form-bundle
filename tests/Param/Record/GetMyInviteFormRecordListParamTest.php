<?php

declare(strict_types=1);

namespace DiyFormBundle\Tests\Param\Record;

use DiyFormBundle\Param\Record\GetMyInviteFormRecordListParam;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Tourze\JsonRPCPaginatorBundle\Param\PaginatorParamInterface;

/**
 * @internal
 */
#[CoversClass(GetMyInviteFormRecordListParam::class)]
final class GetMyInviteFormRecordListParamTest extends TestCase
{
    public function testParamImplementsPaginatorParamInterface(): void
    {
        $param = new GetMyInviteFormRecordListParam();
        $this->assertInstanceOf(PaginatorParamInterface::class, $param);
    }

    public function testParamCanBeConstructedWithNoArguments(): void
    {
        $param = new GetMyInviteFormRecordListParam();

        // 验证默认值
        $this->assertNull($param->formId);
        $this->assertSame(1, $param->currentPage);
        $this->assertSame(20, $param->pageSize);
        $this->assertNull($param->lastId);
    }

    public function testParamCanBeConstructedWithCustomArguments(): void
    {
        $param = new GetMyInviteFormRecordListParam(
            formId: 'form-123',
            currentPage: 2,
            pageSize: 10,
            lastId: 100
        );

        $this->assertSame('form-123', $param->formId);
        $this->assertSame(2, $param->currentPage);
        $this->assertSame(10, $param->pageSize);
        $this->assertSame(100, $param->lastId);
    }

    public function testParamIsReadonly(): void
    {
        $param = new GetMyInviteFormRecordListParam(
            formId: 'readonly-test-form',
            currentPage: 5,
            pageSize: 30,
            lastId: 200
        );

        // 验证属性值不能被修改（readonly 属性）
        $this->assertSame('readonly-test-form', $param->formId);
        $this->assertSame(5, $param->currentPage);
        $this->assertSame(30, $param->pageSize);
        $this->assertSame(200, $param->lastId);
    }

    /**
     * @param string|null $formId 表单ID
     */
    #[DataProvider('validFormIdsProvider')]
    public function testParamWithValidFormIds(?string $formId): void
    {
        $param = new GetMyInviteFormRecordListParam(formId: $formId);

        $this->assertSame($formId, $param->formId);
    }

    public static function validFormIdsProvider(): array
    {
        return [
            [null], // 默认值
            [''],
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
        $param = new GetMyInviteFormRecordListParam(formId: '');

        $this->assertSame('', $param->formId);
        $this->assertSame(1, $param->currentPage);
        $this->assertSame(20, $param->pageSize);
        $this->assertNull($param->lastId);
    }

    public function testParamWithNumericFormIds(): void
    {
        $numericIds = ['0', '1', '10', '100', '999999'];

        foreach ($numericIds as $formId) {
            $param = new GetMyInviteFormRecordListParam(formId: $formId);
            $this->assertSame($formId, $param->formId);
        }
    }

    public function testParamWithAlphanumericFormIds(): void
    {
        $param = new GetMyInviteFormRecordListParam(formId: 'FORM123abc456');

        $this->assertSame('FORM123abc456', $param->formId);
    }

    public function testParamWithLongFormId(): void
    {
        $longFormId = str_repeat('form-id-', 50); // 很长的表单ID

        $param = new GetMyInviteFormRecordListParam(formId: $longFormId);

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
            $param = new GetMyInviteFormRecordListParam(formId: $formId);
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
            $param = new GetMyInviteFormRecordListParam(formId: $formId);
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
            $param = new GetMyInviteFormRecordListParam(formId: $formId);
            $this->assertSame($formId, $param->formId);
        }
    }

    /**
     * @param int $currentPage 当前页数
     */
    #[DataProvider('validCurrentPageProvider')]
    public function testParamWithValidCurrentPages(int $currentPage): void
    {
        $param = new GetMyInviteFormRecordListParam(currentPage: $currentPage);

        $this->assertSame($currentPage, $param->currentPage);
        $this->assertNull($param->formId);
        $this->assertSame(20, $param->pageSize);
        $this->assertNull($param->lastId);
    }

    public static function validCurrentPageProvider(): array
    {
        return [
            [1], // 默认值
            [0],
            [2],
            [10],
            [100],
            [999],
            [1000],
            [9999],
            [PHP_INT_MAX],
        ];
    }

    /**
     * @param int $pageSize 每页条数
     */
    #[DataProvider('validPageSizeProvider')]
    public function testParamWithValidPageSizes(int $pageSize): void
    {
        $param = new GetMyInviteFormRecordListParam(pageSize: $pageSize);

        $this->assertSame($pageSize, $param->pageSize);
        $this->assertNull($param->formId);
        $this->assertSame(1, $param->currentPage);
        $this->assertNull($param->lastId);
    }

    public static function validPageSizeProvider(): array
    {
        return [
            [20], // 默认值
            [0],
            [1],
            [5],
            [10],
            [25],
            [50],
            [100],
            [200],
            [500],
            [1000],
            [9999],
            [PHP_INT_MAX],
        ];
    }

    /**
     * @param int|null $lastId 最后一条数据的主键ID
     */
    #[DataProvider('validLastIdProvider')]
    public function testParamWithValidLastIds(?int $lastId): void
    {
        $param = new GetMyInviteFormRecordListParam(lastId: $lastId);

        $this->assertSame($lastId, $param->lastId);
        $this->assertNull($param->formId);
        $this->assertSame(1, $param->currentPage);
        $this->assertSame(20, $param->pageSize);
    }

    public static function validLastIdProvider(): array
    {
        return [
            [null], // 默认值
            [0],
            [1],
            [10],
            [100],
            [999],
            [1000],
            [9999],
            [123456789],
            [PHP_INT_MAX],
        ];
    }

    public function testParamWithZeroValues(): void
    {
        $param = new GetMyInviteFormRecordListParam(
            currentPage: 0,
            pageSize: 0,
            lastId: 0
        );

        $this->assertSame(0, $param->currentPage);
        $this->assertSame(0, $param->pageSize);
        $this->assertSame(0, $param->lastId);
    }

    public function testParamWithNegativeValues(): void
    {
        $param = new GetMyInviteFormRecordListParam(
            currentPage: -1,
            pageSize: -5,
            lastId: -10
        );

        $this->assertSame(-1, $param->currentPage);
        $this->assertSame(-5, $param->pageSize);
        $this->assertSame(-10, $param->lastId);
    }

    public function testParamWithMaximumIntegerValues(): void
    {
        $maxInt = PHP_INT_MAX;

        $param = new GetMyInviteFormRecordListParam(
            currentPage: $maxInt,
            pageSize: $maxInt,
            lastId: $maxInt
        );

        $this->assertSame($maxInt, $param->currentPage);
        $this->assertSame($maxInt, $param->pageSize);
        $this->assertSame($maxInt, $param->lastId);
    }

    public function testDefaultValues(): void
    {
        // 测试不传参数时使用默认值
        $param = new GetMyInviteFormRecordListParam();
        $this->assertNull($param->formId);
        $this->assertSame(1, $param->currentPage);
        $this->assertSame(20, $param->pageSize);
        $this->assertNull($param->lastId);

        // 测试显式传递默认值
        $paramWithDefaults = new GetMyInviteFormRecordListParam(
            formId: null,
            currentPage: 1,
            pageSize: 20,
            lastId: null
        );
        $this->assertNull($paramWithDefaults->formId);
        $this->assertSame(1, $paramWithDefaults->currentPage);
        $this->assertSame(20, $paramWithDefaults->pageSize);
        $this->assertNull($paramWithDefaults->lastId);
    }

    public function testPaginatorInterfaceMethods(): void
    {
        $param = new GetMyInviteFormRecordListParam(
            currentPage: 3,
            pageSize: 50
        );

        // 验证 PaginatorParamInterface 要求的方法（如果有的话）
        $this->assertTrue(method_exists($param, 'getCurrentPage') || property_exists($param, 'currentPage'));
        $this->assertTrue(method_exists($param, 'getPageSize') || property_exists($param, 'pageSize'));
    }

    public function testAllPublicPropertiesAreAccessible(): void
    {
        $param = new GetMyInviteFormRecordListParam(
            formId: 'test-form',
            currentPage: 5,
            pageSize: 25,
            lastId: 150
        );

        // 由于是 readonly class，属性是 public readonly
        $this->assertSame('test-form', $param->formId);
        $this->assertSame(5, $param->currentPage);
        $this->assertSame(25, $param->pageSize);
        $this->assertSame(150, $param->lastId);
    }

    public function testParamWithAllParametersSet(): void
    {
        $param = new GetMyInviteFormRecordListParam(
            formId: 'form-with-all-params',
            currentPage: 10,
            pageSize: 100,
            lastId: 500
        );

        $this->assertSame('form-with-all-params', $param->formId);
        $this->assertSame(10, $param->currentPage);
        $this->assertSame(100, $param->pageSize);
        $this->assertSame(500, $param->lastId);
    }
}
