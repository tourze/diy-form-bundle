<?php

declare(strict_types=1);

namespace DiyFormBundle\Tests\Param\Step;

use DiyFormBundle\Param\Step\SubmitDiyFormRecordParam;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Tourze\JsonRPC\Core\Contracts\RpcParamInterface;

/**
 * @internal
 */
#[CoversClass(SubmitDiyFormRecordParam::class)]
final class SubmitDiyFormRecordParamTest extends TestCase
{
    public function testParamImplementsRpcParamInterface(): void
    {
        $param = new SubmitDiyFormRecordParam();
        $this->assertInstanceOf(RpcParamInterface::class, $param);
    }

    public function testParamCanBeConstructedWithNoArguments(): void
    {
        $param = new SubmitDiyFormRecordParam();

        // 验证默认值
        $this->assertSame(2, $param->formId);
        $this->assertSame(0, $param->recordId);
    }

    public function testParamCanBeConstructedWithCustomArguments(): void
    {
        $param = new SubmitDiyFormRecordParam(
            formId: 123,
            recordId: 456
        );

        $this->assertSame(123, $param->formId);
        $this->assertSame(456, $param->recordId);
    }

    public function testParamIsReadonly(): void
    {
        $param = new SubmitDiyFormRecordParam(
            formId: 999,
            recordId: 888
        );

        // 验证属性值不能被修改（readonly 属性）
        $this->assertSame(999, $param->formId);
        $this->assertSame(888, $param->recordId);
    }

    /**
     * @param int $formId 表单ID
     */
    #[DataProvider('validFormIdsProvider')]
    public function testParamWithValidFormIds(int $formId): void
    {
        $param = new SubmitDiyFormRecordParam(
            formId: $formId,
            recordId: 0
        );

        $this->assertSame($formId, $param->formId);
    }

    public static function validFormIdsProvider(): array
    {
        return [
            [0],
            [1],
            [2], // 默认值
            [3],
            [10],
            [100],
            [999],
            [999999],
            [123456789],
            [PHP_INT_MAX],
        ];
    }

    /**
     * @param int $recordId 记录ID
     */
    #[DataProvider('validRecordIdsProvider')]
    public function testParamWithValidRecordIds(int $recordId): void
    {
        $param = new SubmitDiyFormRecordParam(
            formId: 2,
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
            [123456789],
            [PHP_INT_MAX],
        ];
    }

    #[DataProvider('boundaryFormIdsProvider')]
    public function testParamWithBoundaryFormIds(int $formId): void
    {
        $param = new SubmitDiyFormRecordParam(
            formId: $formId,
            recordId: 0
        );

        $this->assertSame($formId, $param->formId);
    }

    public static function boundaryFormIdsProvider(): array
    {
        return [
            [PHP_INT_MIN],
            [-999999],
            [-1],
            [0],
            [1],
            [2], // 默认值
            [3],
            [999999],
            [PHP_INT_MAX],
        ];
    }

    #[DataProvider('boundaryRecordIdsProvider')]
    public function testParamWithBoundaryRecordIds(int $recordId): void
    {
        $param = new SubmitDiyFormRecordParam(
            formId: 2,
            recordId: $recordId
        );

        $this->assertSame($recordId, $param->recordId);
    }

    public static function boundaryRecordIdsProvider(): array
    {
        return [
            [PHP_INT_MIN],
            [-999999],
            [-1],
            [0], // 默认值
            [1],
            [10],
            [999999],
            [PHP_INT_MAX],
        ];
    }

    public function testParamWithZeroFormId(): void
    {
        $param = new SubmitDiyFormRecordParam(
            formId: 0,
            recordId: 123
        );

        $this->assertSame(0, $param->formId);
        $this->assertSame(123, $param->recordId);
    }

    public function testParamWithZeroRecordId(): void
    {
        $param = new SubmitDiyFormRecordParam(
            formId: 123,
            recordId: 0
        );

        $this->assertSame(123, $param->formId);
        $this->assertSame(0, $param->recordId);
    }

    public function testParamWithNegativeIds(): void
    {
        $param = new SubmitDiyFormRecordParam(
            formId: -1,
            recordId: -1
        );

        $this->assertSame(-1, $param->formId);
        $this->assertSame(-1, $param->recordId);
    }

    public function testParamWithLargeIds(): void
    {
        $param = new SubmitDiyFormRecordParam(
            formId: 999999999,
            recordId: 888888888
        );

        $this->assertSame(999999999, $param->formId);
        $this->assertSame(888888888, $param->recordId);
    }

    public function testDefaultValues(): void
    {
        // 测试不传参数时使用默认值
        $param = new SubmitDiyFormRecordParam();
        $this->assertSame(2, $param->formId);
        $this->assertSame(0, $param->recordId);

        // 测试显式传递默认值
        $paramWithDefaults = new SubmitDiyFormRecordParam(
            formId: 2,
            recordId: 0
        );
        $this->assertSame(2, $paramWithDefaults->formId);
        $this->assertSame(0, $paramWithDefaults->recordId);
    }

    public function testParamWithMaximumIds(): void
    {
        $maxFormId = PHP_INT_MAX;
        $maxRecordId = PHP_INT_MAX;
        $param = new SubmitDiyFormRecordParam(
            formId: $maxFormId,
            recordId: $maxRecordId
        );

        $this->assertSame($maxFormId, $param->formId);
        $this->assertSame($maxRecordId, $param->recordId);
    }

    public function testParamWithMinimumIds(): void
    {
        $minFormId = PHP_INT_MIN;
        $minRecordId = PHP_INT_MIN;
        $param = new SubmitDiyFormRecordParam(
            formId: $minFormId,
            recordId: $minRecordId
        );

        $this->assertSame($minFormId, $param->formId);
        $this->assertSame($minRecordId, $param->recordId);
    }

    public function testParamWithVariousFormIds(): void
    {
        $formIds = [
            0, 1, 2, 3, 4, 5, 10, 50, 100, 500, 1000,
            10000, 100000, 1000000, 10000000, 100000000,
            123, 456, 789, 999, 1234, 5678, 9012,
        ];

        foreach ($formIds as $formId) {
            $param = new SubmitDiyFormRecordParam(
                formId: $formId,
                recordId: 0
            );
            $this->assertSame($formId, $param->formId);
        }
    }

    public function testParamWithVariousRecordIds(): void
    {
        $recordIds = [
            0, 1, 2, 3, 4, 5, 10, 50, 100, 500, 1000,
            10000, 100000, 1000000, 10000000, 100000000,
            123, 456, 789, 999, 1234, 5678, 9012,
        ];

        foreach ($recordIds as $recordId) {
            $param = new SubmitDiyFormRecordParam(
                formId: 2,
                recordId: $recordId
            );
            $this->assertSame($recordId, $param->recordId);
        }
    }

    public function testParamPropertiesAreOfTypeInt(): void
    {
        $param = new SubmitDiyFormRecordParam(
            formId: 123,
            recordId: 456
        );

        $this->assertIsInt($param->formId);
        $this->assertIsInt($param->recordId);
    }

    public function testParamWithCommonIds(): void
    {
        $commonIds = [1, 2, 3, 4, 5, 6, 7, 8, 9, 10];

        foreach ($commonIds as $id) {
            $param = new SubmitDiyFormRecordParam(
                formId: $id,
                recordId: $id
            );
            $this->assertSame($id, $param->formId);
            $this->assertSame($id, $param->recordId);
        }
    }

    public function testParamWithFormIdOne(): void
    {
        $param = new SubmitDiyFormRecordParam(
            formId: 1
        );

        $this->assertSame(1, $param->formId);
        $this->assertSame(0, $param->recordId);
    }

    public function testParamWithRecordIdOne(): void
    {
        $param = new SubmitDiyFormRecordParam(
            formId: 2,
            recordId: 1
        );

        $this->assertSame(2, $param->formId);
        $this->assertSame(1, $param->recordId);
    }

    public function testParamWithFormIdTwo(): void
    {
        $param = new SubmitDiyFormRecordParam(
            formId: 2
        );

        $this->assertSame(2, $param->formId);
        $this->assertSame(0, $param->recordId);
    }

    public function testParamWithRecordIdTwo(): void
    {
        $param = new SubmitDiyFormRecordParam(
            formId: 2,
            recordId: 2
        );

        $this->assertSame(2, $param->formId);
        $this->assertSame(2, $param->recordId);
    }
}
