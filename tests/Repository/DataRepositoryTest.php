<?php

declare(strict_types=1);

namespace DiyFormBundle\Tests\Repository;

use DiyFormBundle\Entity\Data;
use DiyFormBundle\Entity\Field;
use DiyFormBundle\Entity\Form;
use DiyFormBundle\Entity\Record;
use DiyFormBundle\Enum\FieldType;
use DiyFormBundle\Repository\DataRepository;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Tourze\PHPUnitSymfonyKernelTest\AbstractRepositoryTestCase;

/**
 * @internal
 */
#[CoversClass(DataRepository::class)]
#[RunTestsInSeparateProcesses]
final class DataRepositoryTest extends AbstractRepositoryTestCase
{
    private EntityManagerInterface $em;

    private DataRepository $repository;

    private Form $testForm;

    private Field $testField;

    private Record $testRecord;

    protected function createNewEntity(): object
    {
        // 为每个调用创建不同的 Field 以避免唯一约束冲突
        $uniqueField = new Field();
        $uniqueField->setForm($this->testForm);
        $uniqueField->setSn('test_field_' . uniqid());
        $uniqueField->setType(FieldType::STRING);
        $uniqueField->setTitle('Test Field ' . uniqid());
        $uniqueField->setValid(true);
        $this->em->persist($uniqueField);

        $data = new Data();
        $data->setRecord($this->testRecord);
        $data->setField($uniqueField);
        $data->setInput('Test input value ' . uniqid());
        $data->setSkip(false);
        $data->setDeletable(true);

        return $data;
    }

    protected function onSetUp(): void
    {
        $this->em = self::getEntityManager();
        $repository = $this->em->getRepository(Data::class);
        self::assertInstanceOf(DataRepository::class, $repository);
        $this->repository = $repository;

        // 创建测试用的Form
        $this->testForm = new Form();
        $this->testForm->setTitle('Test Form for Data');
        $this->testForm->setStartTime(new \DateTimeImmutable());
        $this->testForm->setEndTime(new \DateTimeImmutable('+1 month'));
        $this->em->persist($this->testForm);

        // 创建测试用的Field
        $this->testField = new Field();
        $this->testField->setForm($this->testForm);
        $this->testField->setSn('test_field_001');
        $this->testField->setType(FieldType::STRING);
        $this->testField->setTitle('Test Field');
        $this->testField->setValid(true);
        $this->em->persist($this->testField);

        // 创建测试用的Record
        $this->testRecord = new Record();
        $this->testRecord->setForm($this->testForm);
        $this->testRecord->setStartTime(new \DateTimeImmutable());
        $this->testRecord->setFinished(false);
        $this->em->persist($this->testRecord);

        $this->em->flush();
    }

    public function testSave(): void
    {
        $data = new Data();
        $data->setRecord($this->testRecord);
        $data->setField($this->testField);
        $data->setInput('Save Test Input');
        $data->setSkip(false);
        $data->setDeletable(true);

        $this->repository->save($data, true);

        self::assertGreaterThan(0, $data->getId());

        $foundData = $this->repository->find($data->getId());
        self::assertNotNull($foundData);
        self::assertSame('Save Test Input', $foundData->getInput());
    }

    public function testRemove(): void
    {
        $data = $this->createTestData();
        $dataId = $data->getId();

        $this->repository->remove($data, true);

        $foundData = $this->repository->find($dataId);
        self::assertNull($foundData);
    }

    public function testFindOneByWithValidFieldShouldReturnEntity(): void
    {
        $data = $this->createTestData();

        $foundData = $this->repository->findOneBy(['input' => 'Test input value']);
        self::assertInstanceOf(Data::class, $foundData);
        self::assertSame($data->getId(), $foundData->getId());
    }

    public function testFindOneByWithNullFieldShouldReturnMatchingEntity(): void
    {
        $data = new Data();
        $data->setRecord($this->testRecord);
        $data->setField($this->testField);
        $data->setInput('Test with null skip');
        $data->setSkip(null);
        $this->em->persist($data);
        $this->em->flush();

        $foundData = $this->repository->findOneBy(['skip' => null]);
        self::assertInstanceOf(Data::class, $foundData);
        self::assertNull($foundData->isSkip());
    }

    public function testFindByRecordShouldReturnAllDataForRecord(): void
    {
        // 首先清理所有已存在的 Data 记录
        $existingData = $this->repository->findAll();
        foreach ($existingData as $data) {
            $this->em->remove($data);
        }
        $this->em->flush();

        // 创建另一个 Field 以避免唯一约束冲突
        $otherField = new Field();
        $otherField->setForm($this->testForm);
        $otherField->setSn('test_field_002');
        $otherField->setType(FieldType::STRING);
        $otherField->setTitle('Test Field 2');
        $otherField->setValid(true);
        $this->em->persist($otherField);
        $this->em->flush();

        $data1 = $this->createTestData();
        $data2 = new Data();
        $data2->setRecord($this->testRecord);
        $data2->setField($otherField);
        $data2->setInput('Second data entry');
        $data2->setSkip(false);
        $this->em->persist($data2);
        $this->em->flush();

        $dataList = $this->repository->findBy(['record' => $this->testRecord]);
        self::assertGreaterThanOrEqual(2, count($dataList));

        foreach ($dataList as $data) {
            $record = $data->getRecord();
            self::assertNotNull($record);
            self::assertSame($this->testRecord->getId(), $record->getId());
        }
    }

    public function testFindBySkipFalseShouldReturnNonSkippedData(): void
    {
        // 首先清理所有已存在的 Data 记录
        $existingData = $this->repository->findAll();
        foreach ($existingData as $data) {
            $this->em->remove($data);
        }
        $this->em->flush();

        // 创建另一个 Field 以避免唯一约束冲突
        $otherField = new Field();
        $otherField->setForm($this->testForm);
        $otherField->setSn('test_field_003');
        $otherField->setType(FieldType::STRING);
        $otherField->setTitle('Test Field 3');
        $otherField->setValid(true);
        $this->em->persist($otherField);
        $this->em->flush();

        $data1 = $this->createTestData();
        $data2 = new Data();
        $data2->setRecord($this->testRecord);
        $data2->setField($otherField);
        $data2->setInput('Skipped data');
        $data2->setSkip(true);
        $this->em->persist($data2);
        $this->em->flush();

        $nonSkippedData = $this->repository->findBy(['skip' => false]);
        self::assertNotEmpty($nonSkippedData);

        foreach ($nonSkippedData as $data) {
            self::assertFalse($data->isSkip());
        }
    }

    public function testFindOneByWithOrderByShouldReturnFirstMatchingEntity(): void
    {
        // 首先清理所有已存在的 Data 记录
        $existingData = $this->repository->findAll();
        foreach ($existingData as $data) {
            $this->em->remove($data);
        }
        $this->em->flush();

        // 创建另一个 Field 以避免唯一约束冲突
        $otherField = new Field();
        $otherField->setForm($this->testForm);
        $otherField->setSn('test_field_006');
        $otherField->setType(FieldType::STRING);
        $otherField->setTitle('Test Field 6');
        $otherField->setValid(true);
        $this->em->persist($otherField);
        $this->em->flush();

        $data1 = new Data();
        $data1->setRecord($this->testRecord);
        $data1->setField($this->testField);
        $data1->setInput('Same Skip Data 1');
        $data1->setSkip(false);
        $data2 = new Data();
        $data2->setRecord($this->testRecord);
        $data2->setField($otherField);
        $data2->setInput('Same Skip Data 2');
        $data2->setSkip(false);

        $this->em->persist($data1);
        $this->em->persist($data2);
        $this->em->flush();

        $foundData = $this->repository->findOneBy(['skip' => false], ['input' => 'ASC']);
        self::assertNotNull($foundData);
    }

    public function testFindByWithNullFieldShouldReturnMatchingEntities(): void
    {
        // 首先清理所有已存在的 Data 记录
        $existingData = $this->repository->findAll();
        foreach ($existingData as $data) {
            $this->em->remove($data);
        }
        $this->em->flush();

        $data1 = new Data();
        $data1->setRecord($this->testRecord);
        $data1->setField(null);
        $data1->setInput('Null Field Data 1');
        $data1->setSkip(false);
        $data2 = new Data();
        $data2->setRecord($this->testRecord);
        $data2->setField(null);
        $data2->setInput('Null Field Data 2');
        $data2->setSkip(false);
        $data3 = new Data();
        $data3->setRecord($this->testRecord);
        $data3->setField($this->testField);
        $data3->setInput('With Field Data');
        $data3->setSkip(false);

        $this->em->persist($data1);
        $this->em->persist($data2);
        $this->em->persist($data3);
        $this->em->flush();

        $dataList = $this->repository->findBy(['field' => null]);
        self::assertCount(2, $dataList);
        foreach ($dataList as $data) {
            self::assertNull($data->getField());
        }
    }

    public function testFindByWithNullSkipShouldReturnMatchingEntities(): void
    {
        // 首先清理所有已存在的 Data 记录
        $existingData = $this->repository->findAll();
        foreach ($existingData as $data) {
            $this->em->remove($data);
        }
        $this->em->flush();

        // 创建多个 Field 以避免唯一约束冲突
        $field1 = new Field();
        $field1->setForm($this->testForm);
        $field1->setSn('test_field_skip_1');
        $field1->setType(FieldType::STRING);
        $field1->setTitle('Test Field Skip 1');
        $field1->setValid(true);
        $field2 = new Field();
        $field2->setForm($this->testForm);
        $field2->setSn('test_field_skip_2');
        $field2->setType(FieldType::STRING);
        $field2->setTitle('Test Field Skip 2');
        $field2->setValid(true);
        $field3 = new Field();
        $field3->setForm($this->testForm);
        $field3->setSn('test_field_skip_3');
        $field3->setType(FieldType::STRING);
        $field3->setTitle('Test Field Skip 3');
        $field3->setValid(true);
        $this->em->persist($field1);
        $this->em->persist($field2);
        $this->em->persist($field3);
        $this->em->flush();

        $data1 = new Data();
        $data1->setRecord($this->testRecord);
        $data1->setField($field1);
        $data1->setInput('Null Skip Data 1');
        $data1->setSkip(null);
        $data2 = new Data();
        $data2->setRecord($this->testRecord);
        $data2->setField($field2);
        $data2->setInput('Null Skip Data 2');
        $data2->setSkip(null);
        $data3 = new Data();
        $data3->setRecord($this->testRecord);
        $data3->setField($field3);
        $data3->setInput('With Skip Data');
        $data3->setSkip(false);

        $this->em->persist($data1);
        $this->em->persist($data2);
        $this->em->persist($data3);
        $this->em->flush();

        $dataList = $this->repository->findBy(['skip' => null]);
        self::assertCount(2, $dataList);
        foreach ($dataList as $data) {
            self::assertNull($data->isSkip());
        }
    }

    public function testFindByWithNullAnswerTagsShouldReturnMatchingEntities(): void
    {
        // 首先清理所有已存在的 Data 记录
        $existingData = $this->repository->findAll();
        foreach ($existingData as $data) {
            $this->em->remove($data);
        }
        $this->em->flush();

        // 创建多个 Field 以避免唯一约束冲突
        $field1 = new Field();
        $field1->setForm($this->testForm);
        $field1->setSn('test_field_tags_1');
        $field1->setType(FieldType::STRING);
        $field1->setTitle('Test Field Tags 1');
        $field1->setValid(true);
        $field2 = new Field();
        $field2->setForm($this->testForm);
        $field2->setSn('test_field_tags_2');
        $field2->setType(FieldType::STRING);
        $field2->setTitle('Test Field Tags 2');
        $field2->setValid(true);
        $field3 = new Field();
        $field3->setForm($this->testForm);
        $field3->setSn('test_field_tags_3');
        $field3->setType(FieldType::STRING);
        $field3->setTitle('Test Field Tags 3');
        $field3->setValid(true);
        $this->em->persist($field1);
        $this->em->persist($field2);
        $this->em->persist($field3);
        $this->em->flush();

        $data1 = new Data();
        $data1->setRecord($this->testRecord);
        $data1->setField($field1);
        $data1->setInput('Null Tags Data 1');
        $data1->setSkip(false);
        $data1->setAnswerTags(null);
        $data2 = new Data();
        $data2->setRecord($this->testRecord);
        $data2->setField($field2);
        $data2->setInput('Null Tags Data 2');
        $data2->setSkip(false);
        $data2->setAnswerTags(null);
        $data3 = new Data();
        $data3->setRecord($this->testRecord);
        $data3->setField($field3);
        $data3->setInput('With Tags Data');
        $data3->setSkip(false);
        $data3->setAnswerTags(['tag1', 'tag2']);

        $this->em->persist($data1);
        $this->em->persist($data2);
        $this->em->persist($data3);
        $this->em->flush();

        $dataList = $this->repository->findBy(['answerTags' => null]);
        self::assertCount(2, $dataList);
        foreach ($dataList as $data) {
            self::assertNull($data->getAnswerTags());
        }
    }

    public function testCountWithNullFieldShouldReturnCorrectNumber(): void
    {
        // 首先清理所有已存在的 Data 记录
        $existingData = $this->repository->findAll();
        foreach ($existingData as $data) {
            $this->em->remove($data);
        }
        $this->em->flush();

        $data1 = new Data();
        $data1->setRecord($this->testRecord);
        $data1->setField(null);
        $data1->setInput('Count Null Field 1');
        $data1->setSkip(false);
        $data2 = new Data();
        $data2->setRecord($this->testRecord);
        $data2->setField(null);
        $data2->setInput('Count Null Field 2');
        $data2->setSkip(false);
        $data3 = new Data();
        $data3->setRecord($this->testRecord);
        $data3->setField($this->testField);
        $data3->setInput('Count With Field');
        $data3->setSkip(false);

        $this->em->persist($data1);
        $this->em->persist($data2);
        $this->em->persist($data3);
        $this->em->flush();

        $count = $this->repository->count(['field' => null]);
        self::assertSame(2, $count);
    }

    public function testCountWithNullSkipShouldReturnCorrectNumber(): void
    {
        // 首先清理所有已存在的 Data 记录
        $existingData = $this->repository->findAll();
        foreach ($existingData as $data) {
            $this->em->remove($data);
        }
        $this->em->flush();

        // 创建多个 Field 以避免唯一约束冲突
        $field1 = new Field();
        $field1->setForm($this->testForm);
        $field1->setSn('test_field_count_skip_1');
        $field1->setType(FieldType::STRING);
        $field1->setTitle('Test Field Count Skip 1');
        $field1->setValid(true);
        $field2 = new Field();
        $field2->setForm($this->testForm);
        $field2->setSn('test_field_count_skip_2');
        $field2->setType(FieldType::STRING);
        $field2->setTitle('Test Field Count Skip 2');
        $field2->setValid(true);
        $field3 = new Field();
        $field3->setForm($this->testForm);
        $field3->setSn('test_field_count_skip_3');
        $field3->setType(FieldType::STRING);
        $field3->setTitle('Test Field Count Skip 3');
        $field3->setValid(true);
        $this->em->persist($field1);
        $this->em->persist($field2);
        $this->em->persist($field3);
        $this->em->flush();

        $data1 = new Data();
        $data1->setRecord($this->testRecord);
        $data1->setField($field1);
        $data1->setInput('Count Null Skip 1');
        $data1->setSkip(null);
        $data2 = new Data();
        $data2->setRecord($this->testRecord);
        $data2->setField($field2);
        $data2->setInput('Count Null Skip 2');
        $data2->setSkip(null);
        $data3 = new Data();
        $data3->setRecord($this->testRecord);
        $data3->setField($field3);
        $data3->setInput('Count With Skip');
        $data3->setSkip(false);

        $this->em->persist($data1);
        $this->em->persist($data2);
        $this->em->persist($data3);
        $this->em->flush();

        $count = $this->repository->count(['skip' => null]);
        self::assertSame(2, $count);
    }

    public function testCountWithNullAnswerTagsShouldReturnCorrectNumber(): void
    {
        // 首先清理所有已存在的 Data 记录
        $existingData = $this->repository->findAll();
        foreach ($existingData as $data) {
            $this->em->remove($data);
        }
        $this->em->flush();

        // 创建多个 Field 以避免唯一约束冲突
        $field1 = new Field();
        $field1->setForm($this->testForm);
        $field1->setSn('test_field_count_tags_1');
        $field1->setType(FieldType::STRING);
        $field1->setTitle('Test Field Count Tags 1');
        $field1->setValid(true);
        $field2 = new Field();
        $field2->setForm($this->testForm);
        $field2->setSn('test_field_count_tags_2');
        $field2->setType(FieldType::STRING);
        $field2->setTitle('Test Field Count Tags 2');
        $field2->setValid(true);
        $field3 = new Field();
        $field3->setForm($this->testForm);
        $field3->setSn('test_field_count_tags_3');
        $field3->setType(FieldType::STRING);
        $field3->setTitle('Test Field Count Tags 3');
        $field3->setValid(true);
        $this->em->persist($field1);
        $this->em->persist($field2);
        $this->em->persist($field3);
        $this->em->flush();

        $data1 = new Data();
        $data1->setRecord($this->testRecord);
        $data1->setField($field1);
        $data1->setInput('Count Null Tags 1');
        $data1->setSkip(false);
        $data1->setAnswerTags(null);
        $data2 = new Data();
        $data2->setRecord($this->testRecord);
        $data2->setField($field2);
        $data2->setInput('Count Null Tags 2');
        $data2->setSkip(false);
        $data2->setAnswerTags(null);
        $data3 = new Data();
        $data3->setRecord($this->testRecord);
        $data3->setField($field3);
        $data3->setInput('Count With Tags');
        $data3->setSkip(false);
        $data3->setAnswerTags(['tag1']);

        $this->em->persist($data1);
        $this->em->persist($data2);
        $this->em->persist($data3);
        $this->em->flush();

        $count = $this->repository->count(['answerTags' => null]);
        self::assertSame(2, $count);
    }

    public function testFindByRecordAssociationShouldReturnMatchingEntities(): void
    {
        // 首先清理所有已存在的 Data 记录
        $existingData = $this->repository->findAll();
        foreach ($existingData as $data) {
            $this->em->remove($data);
        }
        $this->em->flush();

        $otherRecord = new Record();
        $otherRecord->setForm($this->testForm);
        $otherRecord->setStartTime(new \DateTimeImmutable());
        $otherRecord->setFinished(false);
        $this->em->persist($otherRecord);
        $this->em->flush();

        $data1 = new Data();
        $data1->setRecord($this->testRecord);
        $data1->setField($this->testField);
        $data1->setInput('Data for Test Record');
        $data1->setSkip(false);
        $data2 = new Data();
        $data2->setRecord($otherRecord);
        $data2->setField($this->testField);
        $data2->setInput('Data for Other Record');
        $data2->setSkip(false);

        $this->em->persist($data1);
        $this->em->persist($data2);
        $this->em->flush();

        $dataList = $this->repository->findBy(['record' => $this->testRecord]);
        self::assertGreaterThanOrEqual(1, count($dataList));
        foreach ($dataList as $data) {
            $record = $data->getRecord();
            self::assertNotNull($record);
            self::assertSame($this->testRecord->getId(), $record->getId());
        }
    }

    public function testFindByFieldAssociationShouldReturnMatchingEntities(): void
    {
        // 首先清理所有已存在的 Data 记录
        $existingData = $this->repository->findAll();
        foreach ($existingData as $data) {
            $this->em->remove($data);
        }
        $this->em->flush();

        $otherField = new Field();
        $otherField->setForm($this->testForm);
        $otherField->setSn('other_field_001');
        $otherField->setType(FieldType::STRING);
        $otherField->setTitle('Other Field');
        $otherField->setValid(true);
        $this->em->persist($otherField);
        $this->em->flush();

        $data1 = new Data();
        $data1->setRecord($this->testRecord);
        $data1->setField($this->testField);
        $data1->setInput('Data for Test Field');
        $data1->setSkip(false);
        $data2 = new Data();
        $data2->setRecord($this->testRecord);
        $data2->setField($otherField);
        $data2->setInput('Data for Other Field');
        $data2->setSkip(false);

        $this->em->persist($data1);
        $this->em->persist($data2);
        $this->em->flush();

        $dataList = $this->repository->findBy(['field' => $this->testField]);
        self::assertGreaterThanOrEqual(1, count($dataList));
        foreach ($dataList as $data) {
            $field = $data->getField();
            self::assertNotNull($field);
            self::assertSame($this->testField->getId(), $field->getId());
        }
    }

    public function testCountByRecordAssociationShouldReturnCorrectNumber(): void
    {
        // 首先清理所有已存在的 Data 记录
        $existingData = $this->repository->findAll();
        foreach ($existingData as $data) {
            $this->em->remove($data);
        }
        $this->em->flush();

        $otherRecord = new Record();
        $otherRecord->setForm($this->testForm);
        $otherRecord->setStartTime(new \DateTimeImmutable());
        $otherRecord->setFinished(false);
        $this->em->persist($otherRecord);
        $this->em->flush();

        $initialCount = $this->repository->count(['record' => $this->testRecord]);

        // 创建多个 Field 以避免唯一约束冲突
        $field1 = new Field();
        $field1->setForm($this->testForm);
        $field1->setSn('test_field_count_record_1');
        $field1->setType(FieldType::STRING);
        $field1->setTitle('Test Field Count Record 1');
        $field1->setValid(true);
        $field2 = new Field();
        $field2->setForm($this->testForm);
        $field2->setSn('test_field_count_record_2');
        $field2->setType(FieldType::STRING);
        $field2->setTitle('Test Field Count Record 2');
        $field2->setValid(true);
        $field3 = new Field();
        $field3->setForm($this->testForm);
        $field3->setSn('test_field_count_record_3');
        $field3->setType(FieldType::STRING);
        $field3->setTitle('Test Field Count Record 3');
        $field3->setValid(true);
        $this->em->persist($field1);
        $this->em->persist($field2);
        $this->em->persist($field3);
        $this->em->flush();

        $data1 = new Data();
        $data1->setRecord($this->testRecord);
        $data1->setField($field1);
        $data1->setInput('Count Data for Test Record 1');
        $data1->setSkip(false);
        $data2 = new Data();
        $data2->setRecord($this->testRecord);
        $data2->setField($field2);
        $data2->setInput('Count Data for Test Record 2');
        $data2->setSkip(false);
        $data3 = new Data();
        $data3->setRecord($otherRecord);
        $data3->setField($field3);
        $data3->setInput('Count Data for Other Record');
        $data3->setSkip(false);

        $this->em->persist($data1);
        $this->em->persist($data2);
        $this->em->persist($data3);
        $this->em->flush();

        $finalCount = $this->repository->count(['record' => $this->testRecord]);
        self::assertSame($initialCount + 2, $finalCount);
    }

    public function testCountByFieldAssociationShouldReturnCorrectNumber(): void
    {
        // 首先清理所有已存在的 Data 记录
        $existingData = $this->repository->findAll();
        foreach ($existingData as $data) {
            $this->em->remove($data);
        }
        $this->em->flush();

        $otherField = new Field();
        $otherField->setForm($this->testForm);
        $otherField->setSn('count_field_001');
        $otherField->setType(FieldType::STRING);
        $otherField->setTitle('Count Field');
        $otherField->setValid(true);
        $this->em->persist($otherField);
        $this->em->flush();

        $initialCount = $this->repository->count(['field' => $this->testField]);

        // 创建另一个 Record 以避免唯一约束冲突
        $otherRecord = new Record();
        $otherRecord->setForm($this->testForm);
        $otherRecord->setStartTime(new \DateTimeImmutable());
        $otherRecord->setFinished(false);
        $this->em->persist($otherRecord);
        $this->em->flush();

        $data1 = new Data();
        $data1->setRecord($this->testRecord);
        $data1->setField($this->testField);
        $data1->setInput('Count Data for Test Field 1');
        $data1->setSkip(false);
        $data2 = new Data();
        $data2->setRecord($otherRecord);
        $data2->setField($this->testField);
        $data2->setInput('Count Data for Test Field 2');
        $data2->setSkip(false);
        $data3 = new Data();
        $data3->setRecord($this->testRecord);
        $data3->setField($otherField);
        $data3->setInput('Count Data for Other Field');
        $data3->setSkip(false);

        $this->em->persist($data1);
        $this->em->persist($data2);
        $this->em->persist($data3);
        $this->em->flush();

        $finalCount = $this->repository->count(['field' => $this->testField]);
        self::assertSame($initialCount + 2, $finalCount);
    }

    private function createTestData(): Data
    {
        $data = new Data();
        $data->setRecord($this->testRecord);
        $data->setField($this->testField);
        $data->setInput('Test input value');
        $data->setSkip(false);
        $data->setDeletable(true);
        $data->setAnswerTags(['tag1', 'tag2']);

        $this->em->persist($data);
        $this->em->flush();

        return $data;
    }

    /**
     * @return DataRepository
     */
    protected function getRepository(): ServiceEntityRepository
    {
        return $this->repository;
    }
}
