# DIY Form Bundle

[English](README.md) | [中文](README.zh-CN.md)

[![Latest Version](https://img.shields.io/packagist/v/tourze/diy-form-bundle.svg?style=flat-square)](https://packagist.org/packages/tourze/diy-form-bundle)
[![PHP Version](https://img.shields.io/packagist/php-v/tourze/diy-form-bundle.svg?style=flat-square)](https://packagist.org/packages/tourze/diy-form-bundle)
[![Total Downloads](https://img.shields.io/packagist/dt/tourze/diy-form-bundle.svg?style=flat-square)](https://packagist.org/packages/tourze/diy-form-bundle)
[![License](https://img.shields.io/packagist/l/tourze/diy-form-bundle.svg?style=flat-square)](https://packagist.org/packages/tourze/diy-form-bundle)
[![Build Status](https://img.shields.io/github/actions/workflow/status/tourze/diy-form-bundle/ci.yml)](https://github.com/tourze/diy-form-bundle/actions)
[![Code Coverage](https://img.shields.io/codecov/c/github/tourze/diy-form-bundle)](https://codecov.io/gh/tourze/diy-form-bundle)

为 Symfony 应用程序提供的全面动态表单构建系统，
专为创建调查问卷、自定义表单和高级表单功能而设计。

## 目录

- [功能特性](#功能特性)
- [安装](#安装)
- [快速开始](#快速开始)
- [配置](#配置)
- [字段类型](#字段类型)
- [高级用法](#高级用法)
- [API 文档](#api-文档)
- [依赖关系](#依赖关系)
- [测试](#测试)
- [贡献](#贡献)
- [安全](#安全)
- [许可证](#许可证)

## 功能特性

- **动态表单创建**: 创建包含各种字段类型的表单，包括文本、选择、单选、复选、日期、文件上传等
- **手机验证支持**: 内置手机号码验证和短信验证码功能
- **丰富字段类型**: 支持 13 种不同的字段类型，包括富文本、图片和文件
- **表单分析**: 跟踪表单提交并分析响应数据
- **EasyAdmin 集成**: 管理表单和响应的后台管理界面
- **会话管理**: 分步表单完成，支持会话跟踪
- **表达式语言**: 高级表单逻辑，支持条件字段
- **JSON-RPC API**: 完整的表单管理和提交 API
- **多语言支持**: 完整的国际化支持

## 安装

通过 Composer 安装包：

```bash
composer require tourze/diy-form-bundle
```

## 快速开始

### 1. 启用 Bundle

将 bundle 添加到 `config/bundles.php`：

```php
<?php

return [
    // ... 其他 bundles
    DiyFormBundle\DiyFormBundle::class => ['all' => true],
];
```

### 2. 配置数据库

运行迁移以创建所需的表：

```bash
php bin/console doctrine:migrations:migrate
```

### 3. 基本用法

通过编程方式创建简单表单：

```php
<?php

use DiyFormBundle\Entity\Form;
use DiyFormBundle\Entity\Field;
use DiyFormBundle\Enum\FieldType;

// 创建新表单
$form = new Form();
$form->setTitle('客户调查');
$form->setDescription('请帮助我们改进服务');

// 添加字段
$field = new Field();
$field->setTitle('您的姓名');
$field->setType(FieldType::STRING);
$field->setRequired(true);
$form->addField($field);

// 保存表单
$entityManager->persist($form);
$entityManager->flush();
```

### 4. JSON-RPC API 用法

```php
// 获取表单详情
$result = $jsonRpcClient->call('GetDiyFormDetail', [
    'formId' => '1'
]);

// 提交表单数据
$result = $jsonRpcClient->call('SubmitDiyFormFullRecord', [
    'formId' => '1',
    'data' => [
        'field1' => 'value1',
        'field2' => 'value2'
    ]
]);
```

## 配置

### 服务配置

Bundle 提供可配置的服务：

```yaml
# config/services.yaml
services:
    DiyFormBundle\Service\SmsService:
        arguments:
            $smsProvider: '@your_sms_provider'
    
    DiyFormBundle\Service\ExpressionService:
        arguments:
            $expressionLanguage: '@expression_language'
```

### 路由配置

```yaml
# config/routes.yaml
diy_form:
    resource: '@DiyFormBundle/Resources/config/routes.yaml'
    prefix: /api/diy-form
```

## 字段类型

Bundle 支持以下字段类型：

- **文本字段**: `STRING`, `TEXT`, `RICH_TEXT`
- **选择字段**: `SINGLE_SELECT`, `RADIO_SELECT`, `MULTIPLE_SELECT`, `CHECKBOX_SELECT`
- **日期字段**: `DATE`, `DATE_TIME`
- **数字字段**: `INTEGER`, `DECIMAL`
- **文件字段**: `SINGLE_IMAGE`, `MULTIPLE_IMAGE`, `SINGLE_FILE`
- **特殊字段**: `CAPTCHA_MOBILE_PHONE`

## 高级用法

### 表单逻辑和条件字段

使用表达式语言创建动态表单：

```php
$field->setShowExpression('answer_item("previous_field") == "yes"');
```

### 分析和报告

跟踪表单提交并生成报告：

```php
use DiyFormBundle\Service\TagCalculator;

$calculator = new TagCalculator();
$analytics = $calculator->calculateTags($form, $records);
```

### 自定义字段类型

通过实现自定义处理器扩展字段类型：

```php
use DiyFormBundle\Event\FieldFormatEvent;

class CustomFieldHandler
{
    public function onFieldFormat(FieldFormatEvent $event): void
    {
        // 自定义字段格式化逻辑
    }
}
```

## API 文档

### 表单管理
- `GetDiyFormDetail` - 获取表单配置
- `GetFullDiyFormDetail` - 获取完整表单及所有字段

### 记录管理
- `CreateDiyFormRecord` - 开始新的表单会话
- `SubmitDiyFormFullRecord` - 提交完整表单数据
- `GetDiyFormRecordDetail` - 获取提交详情

### 分步表单
- `GetNextDiyFormField` - 获取下一个字段
- `AnswerSingleDiyFormQuestion` - 回答单个问题

## 依赖关系

### 核心要求

- **PHP**: ^8.1
- **Symfony**: ^6.4
- **Doctrine ORM**: ^3.0
- **EasyAdmin Bundle**: ^4.0

### 主要功能依赖

- **Expression Language**: 用于条件字段逻辑
- **Notifier 组件**: 用于短信验证码功能
- **Serializer 组件**: 用于 API 数据转换
- **Security Bundle**: 用于用户认证和授权

### 可选集成

- **JSON-RPC Bundle**: 用于 API 端点 (tourze/json-rpc-*)
- **Doctrine 扩展**: 用于高级实体功能 (tourze/doctrine-*)
- **文件存储**: 用于文件上传处理

## 测试

### 运行单元测试

Bundle 包含覆盖所有业务逻辑的全面单元测试：

```bash
vendor/bin/phpunit packages/diy-form-bundle/tests
```

**测试覆盖情况：**
- ✅ **187 个单元测试**: 所有单元测试通过，覆盖实体、枚举、事件和核心业务逻辑
- ⚠️ **23 个集成测试**: 由于复杂的依赖配置，目前正在开发中（参见 [Issue #811](https://github.com/tourze/php-monorepo/issues/811)）

### 测试分类

- **实体测试**: 数据模型和关系
- **枚举测试**: 字段类型和验证
- **事件测试**: 表单提交事件
- **服务测试**: 业务逻辑服务
- **控制器测试**: API 端点（集成测试）

## 贡献

详情请参见 [CONTRIBUTING.md](CONTRIBUTING.md)。

## 安全

如果您发现任何安全相关问题，请发邮件至 security@tourze.com，
而不是使用问题跟踪器。

## 许可证

MIT 许可证 (MIT)。更多信息请参见 [许可证文件](LICENSE)。

## 设计参考

此 Bundle 的灵感来自各种表单系统：
- [问卷星](https://www.wjx.cn/)
- [腾讯问卷](https://wj.qq.com/)
- [SurveyMonkey](https://www.surveymonkey.com/)
- [麦客 CRM](https://www.mikecrm.com/)