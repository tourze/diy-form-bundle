# DIY Form Bundle

[English](README.md) | [中文](README.zh-CN.md)

[![Latest Version](https://img.shields.io/packagist/v/tourze/diy-form-bundle.svg?style=flat-square)](https://packagist.org/packages/tourze/diy-form-bundle)
[![PHP Version](https://img.shields.io/packagist/php-v/tourze/diy-form-bundle.svg?style=flat-square)](https://packagist.org/packages/tourze/diy-form-bundle)
[![Total Downloads](https://img.shields.io/packagist/dt/tourze/diy-form-bundle.svg?style=flat-square)](https://packagist.org/packages/tourze/diy-form-bundle)
[![License](https://img.shields.io/packagist/l/tourze/diy-form-bundle.svg?style=flat-square)](https://packagist.org/packages/tourze/diy-form-bundle)
[![Build Status](https://img.shields.io/github/actions/workflow/status/tourze/diy-form-bundle/ci.yml)](https://github.com/tourze/diy-form-bundle/actions)
[![Code Coverage](https://img.shields.io/codecov/c/github/tourze/diy-form-bundle)](https://codecov.io/gh/tourze/diy-form-bundle)

A comprehensive dynamic form builder system for Symfony applications, 
designed for creating surveys, questionnaires, and custom forms with advanced features.

## Table of Contents

- [Features](#features)
- [Installation](#installation)
- [Quick Start](#quick-start)
- [Configuration](#configuration)
- [Field Types](#field-types)
- [Advanced Usage](#advanced-usage)
- [API Documentation](#api-documentation)
- [Dependencies](#dependencies)
- [Testing](#testing)
- [Contributing](#contributing)
- [Security](#security)
- [License](#license)

## Features

- **Dynamic Form Creation**: Create forms with various field types including text, select, radio, 
  checkbox, date, file uploads, and more
- **Mobile Captcha Support**: Built-in mobile phone verification with SMS captcha
- **Rich Field Types**: Support for 13 different field types including rich text, images, and files
- **Form Analytics**: Track form submissions and analyze responses
- **EasyAdmin Integration**: Administrative interface for managing forms and responses
- **Session Management**: Step-by-step form completion with session tracking
- **Expression Language**: Advanced form logic with conditional fields
- **JSON-RPC API**: Complete API for form management and submission
- **Multi-language Support**: Full internationalization support

## Installation

Install the package via Composer:

```bash
composer require tourze/diy-form-bundle
```

## Quick Start

### 1. Enable the Bundle

Add the bundle to your `config/bundles.php`:

```php
<?php

return [
    // ... other bundles
    DiyFormBundle\DiyFormBundle::class => ['all' => true],
];
```

### 2. Configure Database

Run the migrations to create the required tables:

```bash
php bin/console doctrine:migrations:migrate
```

### 3. Basic Usage

Create a simple form programmatically:

```php
<?php

use DiyFormBundle\Entity\Form;
use DiyFormBundle\Entity\Field;
use DiyFormBundle\Enum\FieldType;

// Create a new form
$form = new Form();
$form->setTitle('Customer Survey');
$form->setDescription('Please help us improve our service');

// Add fields
$field = new Field();
$field->setTitle('Your Name');
$field->setType(FieldType::STRING);
$field->setRequired(true);
$form->addField($field);

// Save form
$entityManager->persist($form);
$entityManager->flush();
```

### 4. JSON-RPC API Usage

```php
// Get form details
$result = $jsonRpcClient->call('GetDiyFormDetail', [
    'formId' => '1'
]);

// Submit form data
$result = $jsonRpcClient->call('SubmitDiyFormFullRecord', [
    'formId' => '1',
    'data' => [
        'field1' => 'value1',
        'field2' => 'value2'
    ]
]);
```

## Configuration

### Services Configuration

The bundle provides several services that can be configured:

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

### Routing Configuration

```yaml
# config/routes.yaml
diy_form:
    resource: '@DiyFormBundle/Resources/config/routes.yaml'
    prefix: /api/diy-form
```

## Field Types

The bundle supports the following field types:

- **Text Fields**: `STRING`, `TEXT`, `RICH_TEXT`
- **Selection Fields**: `SINGLE_SELECT`, `RADIO_SELECT`, `MULTIPLE_SELECT`, `CHECKBOX_SELECT`
- **Date Fields**: `DATE`, `DATE_TIME`
- **Numeric Fields**: `INTEGER`, `DECIMAL`
- **File Fields**: `SINGLE_IMAGE`, `MULTIPLE_IMAGE`, `SINGLE_FILE`
- **Special Fields**: `CAPTCHA_MOBILE_PHONE`

## Advanced Usage

### Form Logic and Conditional Fields

Use expression language to create dynamic forms:

```php
$field->setShowExpression('answer_item("previous_field") == "yes"');
```

### Analytics and Reporting

Track form submissions and generate reports:

```php
use DiyFormBundle\Service\TagCalculator;

$calculator = new TagCalculator();
$analytics = $calculator->calculateTags($form, $records);
```

### Custom Field Types

Extend the field types by implementing custom handlers:

```php
use DiyFormBundle\Event\FieldFormatEvent;

class CustomFieldHandler
{
    public function onFieldFormat(FieldFormatEvent $event): void
    {
        // Custom field formatting logic
    }
}
```

## API Documentation

### Form Management
- `GetDiyFormDetail` - Get form configuration
- `GetFullDiyFormDetail` - Get complete form with all fields

### Record Management
- `CreateDiyFormRecord` - Start a new form session
- `SubmitDiyFormFullRecord` - Submit complete form data
- `GetDiyFormRecordDetail` - Get submission details

### Step-by-Step Forms
- `GetNextDiyFormField` - Get next field in sequence
- `AnswerSingleDiyFormQuestion` - Answer individual questions

## Dependencies

### Core Requirements

- **PHP**: ^8.1
- **Symfony**: ^6.4
- **Doctrine ORM**: ^3.0
- **EasyAdmin Bundle**: ^4.0

### Key Features Dependencies

- **Expression Language**: For conditional field logic
- **Notifier Component**: For SMS captcha functionality
- **Serializer Component**: For API data transformation
- **Security Bundle**: For user authentication and authorization

### Optional Integrations

- **JSON-RPC Bundle**: For API endpoints (tourze/json-rpc-*)
- **Doctrine Extensions**: For advanced entity features (tourze/doctrine-*)
- **File Storage**: For file upload handling

## Testing

### Running Unit Tests

The bundle includes comprehensive unit tests covering all business logic:

```bash
vendor/bin/phpunit packages/diy-form-bundle/tests
```

**Test Coverage:**
- ✅ **187 Unit Tests**: All unit tests pass, covering entities, enums, events, and core business logic
- ⚠️ **23 Integration Tests**: Currently under development due to complex dependency setup (see [Issue #811](https://github.com/tourze/php-monorepo/issues/811))

### Test Categories

- **Entity Tests**: Data models and relationships
- **Enum Tests**: Field types and validation
- **Event Tests**: Form submission events
- **Service Tests**: Business logic services
- **Controller Tests**: API endpoints (integration tests)

## Contributing

Please see [CONTRIBUTING.md](CONTRIBUTING.md) for details.

## Security

If you discover any security related issues, please email security@tourze.com 
instead of using the issue tracker.

## License

The MIT License (MIT). Please see [License File](LICENSE) for more information.

## Design References

This bundle was inspired by various form systems:
- [Wenjuanxing (问卷星)](https://www.wjx.cn/)
- [Tencent Questionnaire](https://wj.qq.com/)
- [SurveyMonkey](https://www.surveymonkey.com/)
- [Mikecrm](https://www.mikecrm.com/)