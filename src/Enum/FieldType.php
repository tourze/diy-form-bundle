<?php

declare(strict_types=1);

namespace DiyFormBundle\Enum;

use Tourze\EnumExtra\Itemable;
use Tourze\EnumExtra\ItemTrait;
use Tourze\EnumExtra\Labelable;
use Tourze\EnumExtra\Selectable;
use Tourze\EnumExtra\SelectTrait;

enum FieldType: string implements Labelable, Itemable, Selectable
{
    use ItemTrait;
    use SelectTrait;
    case SINGLE_SELECT = 'single-select';
    case RADIO_SELECT = 'radio-select';
    case MULTIPLE_SELECT = 'multiple-select';
    case CHECKBOX_SELECT = 'checkbox-select';
    case DATE = 'date';
    case DATE_TIME = 'date-time';
    case DECIMAL = 'decimal';
    case INTEGER = 'integer';
    case STRING = 'string';
    case TEXT = 'text';
    case RICH_TEXT = 'rich-text';
    case SINGLE_IMAGE = 'single-image';
    case MULTIPLE_IMAGE = 'multiple-image';
    case SINGLE_FILE = 'single-file';
    case CAPTCHA_MOBILE_PHONE = 'captcha-mobile-phone';

    public function getLabel(): string
    {
        return match ($this) {
            self::SINGLE_SELECT => '下拉单选',
            self::RADIO_SELECT => '带ICON单选',
            self::MULTIPLE_SELECT => '下拉多选',
            self::CHECKBOX_SELECT => '带ICON多选',
            self::DATE => '日期',
            self::DATE_TIME => '日期+时间',
            self::DECIMAL => '小数',
            self::INTEGER => '整数',
            self::STRING => '字符串',
            self::TEXT => '长文本',
            self::RICH_TEXT => '富文本',
            self::SINGLE_IMAGE => '单图',
            self::MULTIPLE_IMAGE => '多图',
            self::SINGLE_FILE => '单文件',
            self::CAPTCHA_MOBILE_PHONE => '手机号码(验证码)',
        };
    }
}
