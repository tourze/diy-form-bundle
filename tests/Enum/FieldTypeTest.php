<?php

namespace DiyFormBundle\Tests\Enum;

use DiyFormBundle\Enum\FieldType;
use PHPUnit\Framework\TestCase;

class FieldTypeTest extends TestCase
{
    public function testGetLabel_返回正确的标签()
    {
        $this->assertEquals('下拉单选', FieldType::SINGLE_SELECT->getLabel());
        $this->assertEquals('带ICON单选', FieldType::RADIO_SELECT->getLabel());
        $this->assertEquals('下拉多选', FieldType::MULTIPLE_SELECT->getLabel());
        $this->assertEquals('带ICON多选', FieldType::CHECKBOX_SELECT->getLabel());
        $this->assertEquals('日期', FieldType::DATE->getLabel());
        $this->assertEquals('日期+时间', FieldType::DATE_TIME->getLabel());
        $this->assertEquals('小数', FieldType::DECIMAL->getLabel());
        $this->assertEquals('整数', FieldType::INTEGER->getLabel());
        $this->assertEquals('字符串', FieldType::STRING->getLabel());
        $this->assertEquals('长文本', FieldType::TEXT->getLabel());
        $this->assertEquals('富文本', FieldType::RICH_TEXT->getLabel());
        $this->assertEquals('单图', FieldType::SINGLE_IMAGE->getLabel());
        $this->assertEquals('多图', FieldType::MULTIPLE_IMAGE->getLabel());
        $this->assertEquals('单文件', FieldType::SINGLE_FILE->getLabel());
        $this->assertEquals('手机号码(验证码)', FieldType::CAPTCHA_MOBILE_PHONE->getLabel());
    }

    public function testToArray_返回正确的项描述()
    {
        $singleSelectItem = FieldType::SINGLE_SELECT->toArray();
        $this->assertEquals(FieldType::SINGLE_SELECT->value, $singleSelectItem['value']);
        $this->assertEquals(FieldType::SINGLE_SELECT->getLabel(), $singleSelectItem['label']);

        $radioSelectItem = FieldType::RADIO_SELECT->toArray();
        $this->assertEquals(FieldType::RADIO_SELECT->value, $radioSelectItem['value']);
        $this->assertEquals(FieldType::RADIO_SELECT->getLabel(), $radioSelectItem['label']);
        
        // 测试所有类型
        foreach (FieldType::cases() as $type) {
            $item = $type->toArray();
            $this->assertEquals($type->value, $item['value']);
            $this->assertEquals($type->getLabel(), $item['label']);
        }
    }

    public function testGenOptions_返回所有枚举项的描述数组()
    {
        $items = FieldType::genOptions();
        $this->assertGreaterThanOrEqual(count(FieldType::cases()), count($items));
        
        // 验证第一个选项的结构
        $firstItem = $items[0];
        $this->assertArrayHasKey('label', $firstItem);
        $this->assertArrayHasKey('value', $firstItem);
        $this->assertArrayHasKey('text', $firstItem);
        $this->assertArrayHasKey('name', $firstItem);
    }
    
    public function testToSelectItem_返回正确的选项数组()
    {
        $option = FieldType::STRING->toSelectItem();
        $this->assertEquals(FieldType::STRING->value, $option['value']);
        $this->assertEquals(FieldType::STRING->getLabel(), $option['label']);
        $this->assertEquals(FieldType::STRING->getLabel(), $option['text']);
        $this->assertEquals(FieldType::STRING->getLabel(), $option['name']);
    }
    
    public function testCases_返回所有枚举值()
    {
        $cases = FieldType::cases();
        $this->assertGreaterThanOrEqual(15, count($cases)); // 至少有15种类型
        
        $values = array_map(fn($case) => $case->value, $cases);
        $this->assertContains('string', $values);
        $this->assertContains('integer', $values);
        $this->assertContains('date', $values);
    }
} 