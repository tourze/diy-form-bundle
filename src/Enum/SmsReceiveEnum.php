<?php

declare(strict_types=1);

namespace DiyFormBundle\Enum;

use Tourze\EnumExtra\BadgeInterface;
use Tourze\EnumExtra\Itemable;
use Tourze\EnumExtra\ItemTrait;
use Tourze\EnumExtra\Labelable;
use Tourze\EnumExtra\Selectable;
use Tourze\EnumExtra\SelectTrait;

/**
 * SMS发送状态
 */
enum SmsReceiveEnum: int implements Labelable, Itemable, Selectable, BadgeInterface
{
    use ItemTrait;
    use SelectTrait;
    case SENT = 1;
    case REJECT = 0;

    public function getLabel(): string
    {
        return match ($this) {
            self::SENT => '已发送',
            self::REJECT => '已退回',
        };
    }

    public function getBadge(): string
    {
        return match ($this) {
            self::SENT => self::SUCCESS,
            self::REJECT => self::DANGER,
        };
    }
}
