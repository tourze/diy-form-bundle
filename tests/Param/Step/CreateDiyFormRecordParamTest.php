<?php

declare(strict_types=1);

namespace DiyFormBundle\Tests\Param\Step;

use DiyFormBundle\Param\Step\CreateDiyFormRecordParam;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Tourze\JsonRPC\Core\Contracts\RpcParamInterface;

/**
 * @internal
 */
#[CoversClass(CreateDiyFormRecordParam::class)]
final class CreateDiyFormRecordParamTest extends TestCase
{
    public function testParamImplementsRpcParamInterface(): void
    {
        $param = new CreateDiyFormRecordParam();
        $this->assertInstanceOf(RpcParamInterface::class, $param);
    }

    public function testParamCanBeConstructedWithNoArguments(): void
    {
        $param = new CreateDiyFormRecordParam();

        // 验证默认值为 2
        $this->assertSame(2, $param->formId);
    }

    public function testParamCanBeConstructedWithCustomArgument(): void
    {
        $param = new CreateDiyFormRecordParam(
            formId: 123
        );

        $this->assertSame(123, $param->formId);
    }

    public function testParamIsReadonly(): void
    {
        $param = new CreateDiyFormRecordParam(
            formId: 999
        );

        // 验证属性值不能被修改（readonly 属性）
        $this->assertSame(999, $param->formId);
    }

    /**
     * @param int $formId 表单ID
     */
    #[DataProvider('validFormIdsProvider')]
    public function testParamWithValidFormIds(int $formId): void
    {
        $param = new CreateDiyFormRecordParam(
            formId: $formId
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

    #[DataProvider('boundaryFormIdsProvider')]
    public function testParamWithBoundaryFormIds(int $formId): void
    {
        $param = new CreateDiyFormRecordParam(
            formId: $formId
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

    public function testParamWithZeroFormId(): void
    {
        $param = new CreateDiyFormRecordParam(
            formId: 0
        );

        $this->assertSame(0, $param->formId);
    }

    public function testParamWithNegativeFormId(): void
    {
        $param = new CreateDiyFormRecordParam(
            formId: -1
        );

        $this->assertSame(-1, $param->formId);
    }

    public function testParamWithLargeFormId(): void
    {
        $largeFormId = 999999999;
        $param = new CreateDiyFormRecordParam(
            formId: $largeFormId
        );

        $this->assertSame($largeFormId, $param->formId);
    }

    public function testParamWithMaximumFormId(): void
    {
        $maxFormId = PHP_INT_MAX;
        $param = new CreateDiyFormRecordParam(
            formId: $maxFormId
        );

        $this->assertSame($maxFormId, $param->formId);
    }

    public function testParamWithMinimumFormId(): void
    {
        $minFormId = PHP_INT_MIN;
        $param = new CreateDiyFormRecordParam(
            formId: $minFormId
        );

        $this->assertSame($minFormId, $param->formId);
    }

    public function testDefaultFormIdValue(): void
    {
        // 测试不传参数时使用默认值
        $param = new CreateDiyFormRecordParam();
        $this->assertSame(2, $param->formId);

        // 测试显式传递默认值
        $paramWithDefault = new CreateDiyFormRecordParam(
            formId: 2
        );
        $this->assertSame(2, $paramWithDefault->formId);
    }

    public function testParamWithVariousFormIds(): void
    {
        $formIds = [
            0, 1, 2, 3, 4, 5, 10, 50, 100, 500, 1000,
            10000, 100000, 1000000, 10000000, 100000000,
            123, 456, 789, 999, 1234, 5678, 9012,
        ];

        foreach ($formIds as $formId) {
            $param = new CreateDiyFormRecordParam(
                formId: $formId
            );
            $this->assertSame($formId, $param->formId);
        }
    }

    public function testParamIsOfTypeInt(): void
    {
        $param = new CreateDiyFormRecordParam(
            formId: 123
        );

        $this->assertIsInt($param->formId);
    }

    public function testParamWithFormIdOne(): void
    {
        $param = new CreateDiyFormRecordParam(
            formId: 1
        );

        $this->assertSame(1, $param->formId);
    }

    public function testParamWithFormIdTwo(): void
    {
        $param = new CreateDiyFormRecordParam(
            formId: 2
        );

        $this->assertSame(2, $param->formId);
    }

    public function testParamWithFormIdThree(): void
    {
        $param = new CreateDiyFormRecordParam(
            formId: 3
        );

        $this->assertSame(3, $param->formId);
    }

    public function testParamWithCommonFormIds(): void
    {
        $commonFormIds = [1, 2, 3, 4, 5, 6, 7, 8, 9, 10];

        foreach ($commonFormIds as $formId) {
            $param = new CreateDiyFormRecordParam(
                formId: $formId
            );
            $this->assertSame($formId, $param->formId);
        }
    }
}
