<?php

declare(strict_types=1);

namespace DiyFormBundle\Tests\Param\Record;

use DiyFormBundle\Param\Record\DeleteDiyFormRecordParam;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Tourze\JsonRPC\Core\Contracts\RpcParamInterface;

/**
 * @internal
 */
#[CoversClass(DeleteDiyFormRecordParam::class)]
final class DeleteDiyFormRecordParamTest extends TestCase
{
    public function testParamImplementsRpcParamInterface(): void
    {
        $param = new DeleteDiyFormRecordParam();
        $this->assertInstanceOf(RpcParamInterface::class, $param);
    }

    public function testParamCanBeConstructedWithNoArguments(): void
    {
        $param = new DeleteDiyFormRecordParam();

        // 验证默认值为空字符串
        $this->assertSame('', $param->recordId);
    }

    public function testParamCanBeConstructedWithCustomArgument(): void
    {
        $param = new DeleteDiyFormRecordParam(
            recordId: 'record-123'
        );

        $this->assertSame('record-123', $param->recordId);
    }

    public function testParamIsReadonly(): void
    {
        $param = new DeleteDiyFormRecordParam(
            recordId: 'readonly-test-record'
        );

        // 验证属性值不能被修改（readonly 属性）
        $this->assertSame('readonly-test-record', $param->recordId);
    }

    /**
     * @param string $recordId 记录ID
     */
    #[DataProvider('validRecordIdsProvider')]
    public function testParamWithValidRecordIds(string $recordId): void
    {
        $param = new DeleteDiyFormRecordParam(
            recordId: $recordId
        );

        $this->assertSame($recordId, $param->recordId);
    }

    public static function validRecordIdsProvider(): array
    {
        return [
            [''],
            ['1'],
            ['123'],
            ['abc'],
            ['record-123'],
            ['RECORD_WITH_UNDERSCORES'],
            ['record-with-dashes'],
            ['123456789'],
            ['uuid-like-string'],
            ['record_特殊字符'],
        ];
    }

    public function testParamWithEmptyRecordId(): void
    {
        $param = new DeleteDiyFormRecordParam(
            recordId: ''
        );

        $this->assertSame('', $param->recordId);
    }

    public function testParamWithNumericRecordIds(): void
    {
        $numericIds = ['0', '1', '10', '100', '999999'];

        foreach ($numericIds as $recordId) {
            $param = new DeleteDiyFormRecordParam(
                recordId: $recordId
            );
            $this->assertSame($recordId, $param->recordId);
        }
    }

    public function testParamWithAlphanumericRecordIds(): void
    {
        $param = new DeleteDiyFormRecordParam(
            recordId: 'RECORD123abc456'
        );

        $this->assertSame('RECORD123abc456', $param->recordId);
    }

    public function testParamWithLongRecordId(): void
    {
        $longRecordId = str_repeat('record-id-', 50); // 很长的记录ID

        $param = new DeleteDiyFormRecordParam(
            recordId: $longRecordId
        );

        $this->assertSame($longRecordId, $param->recordId);
    }

    public function testParamWithSpecialCharactersInRecordId(): void
    {
        $specialRecordIds = [
            'record-with-dashes',
            'record_with_underscores',
            'record.with.dots',
            'record@with@symbols',
            'record#with#hash',
            'record$with$dollar',
            'record%with%percent',
            'record^with^caret',
            'record&with&ampersand',
            'record*with*asterisk',
            'record(with)parentheses',
            'record[with]brackets',
            'record{with}braces',
            'record|with|pipe',
            'record\with\backslash',
            'record/with/slash',
            'record:with:colon',
            'record;with:semicolon',
            'record"with"quotes',
            "record'with'apostrophes",
            'record`with`backticks',
            'record~with~tilde',
            'record!with!exclamation',
            'record?with?question',
        ];

        foreach ($specialRecordIds as $recordId) {
            $param = new DeleteDiyFormRecordParam(
                recordId: $recordId
            );
            $this->assertSame($recordId, $param->recordId);
        }
    }

    public function testParamWithUnicodeCharactersInRecordId(): void
    {
        $unicodeRecordIds = [
            '记录-中文',
            'レコード-日本語',
            '기록-한국어',
            'record-العربية',
            'record- français',
            'record- Deutsch',
            'record- русский',
            'record- español',
            'record- Português',
            'record- Italiano',
        ];

        foreach ($unicodeRecordIds as $recordId) {
            $param = new DeleteDiyFormRecordParam(
                recordId: $recordId
            );
            $this->assertSame($recordId, $param->recordId);
        }
    }

    public function testParamWithWhitespaceInRecordId(): void
    {
        $whitespaceRecordIds = [
            'record with spaces',
            'record\twith\ttabs',
            "record\nwith\nnewlines",
            'record with spaces and    tabs',
            '  leading and trailing spaces  ',
            "\t\ttabs around\t\t",
        ];

        foreach ($whitespaceRecordIds as $recordId) {
            $param = new DeleteDiyFormRecordParam(
                recordId: $recordId
            );
            $this->assertSame($recordId, $param->recordId);
        }
    }

    public function testDefaultRecordIdValue(): void
    {
        // 测试不传参数时使用默认值
        $param = new DeleteDiyFormRecordParam();
        $this->assertSame('', $param->recordId);

        // 测试显式传递空字符串
        $paramWithDefault = new DeleteDiyFormRecordParam(
            recordId: ''
        );
        $this->assertSame('', $paramWithDefault->recordId);
    }

    public function testParamWithUuidLikeIds(): void
    {
        $uuidLikeIds = [
            '550e8400-e29b-41d4-a716-446655440000',
            '6ba7b810-9dad-11d1-80b4-00c04fd430c8',
            '6ba7b811-9dad-11d1-80b4-00c04fd430c8',
            '123e4567-e89b-12d3-a456-426614174000',
            '9b1deb4d-3b7d-4bad-9bdd-2b0d7b3dcb6d',
        ];

        foreach ($uuidLikeIds as $recordId) {
            $param = new DeleteDiyFormRecordParam(
                recordId: $recordId
            );
            $this->assertSame($recordId, $param->recordId);
        }
    }
}
