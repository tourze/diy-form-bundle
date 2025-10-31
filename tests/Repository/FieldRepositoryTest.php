<?php

declare(strict_types=1);

namespace DiyFormBundle\Tests\Repository;

use DiyFormBundle\Entity\Field;
use DiyFormBundle\Entity\Form;
use DiyFormBundle\Enum\FieldType;
use DiyFormBundle\Repository\FieldRepository;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Tourze\PHPUnitSymfonyKernelTest\AbstractRepositoryTestCase;

/**
 * @internal
 */
#[CoversClass(FieldRepository::class)]
#[RunTestsInSeparateProcesses]
final class FieldRepositoryTest extends AbstractRepositoryTestCase
{
    private EntityManagerInterface $em;

    private FieldRepository $repository;

    private Form $testForm;

    protected function onSetUp(): void
    {
        $this->em = self::getEntityManager();
        $repository = $this->em->getRepository(Field::class);
        self::assertInstanceOf(FieldRepository::class, $repository);
        $this->repository = $repository;

        // 创建测试用的Form
        $this->testForm = new Form();
        $this->testForm->setTitle('Test Form for Field');
        $this->testForm->setStartTime(new \DateTimeImmutable());
        $this->testForm->setEndTime(new \DateTimeImmutable('+1 month'));
        $this->em->persist($this->testForm);
        $this->em->flush();
    }

    public function testSave(): void
    {
        $field = new Field();
        $field->setForm($this->testForm);
        $field->setSn('save_test_field');
        $field->setType(FieldType::TEXT);
        $field->setTitle('Save Test Field');
        $field->setRequired(true);
        $field->setValid(true);

        $this->repository->save($field, true);

        self::assertGreaterThan(0, $field->getId());

        $foundField = $this->repository->find($field->getId());
        self::assertInstanceOf(Field::class, $foundField);
        self::assertSame('Save Test Field', $foundField->getTitle());
    }

    public function testRemove(): void
    {
        $field = $this->createTestField();
        $fieldId = $field->getId();

        $this->repository->remove($field, true);

        $foundField = $this->repository->find($fieldId);
        self::assertNull($foundField);
    }

    public function testFindOneByWithValidFieldShouldReturnEntity(): void
    {
        $field = $this->createTestField();

        $foundField = $this->repository->findOneBy(['sn' => 'test_field_sn']);
        self::assertInstanceOf(Field::class, $foundField);
        self::assertSame($field->getId(), $foundField->getId());
    }

    public function testFindOneByWithNullFieldShouldReturnMatchingEntity(): void
    {
        $field = new Field();
        $field->setForm($this->testForm);
        $field->setSn('null_description_field');
        $field->setType(FieldType::STRING);
        $field->setTitle('Null Description Field');
        $field->setDescription(null);
        $field->setValid(true);
        $this->em->persist($field);
        $this->em->flush();

        $foundField = $this->repository->findOneBy(['description' => null]);
        self::assertInstanceOf(Field::class, $foundField);
        self::assertNull($foundField->getDescription());
    }

    public function testFindByFormShouldReturnAllFieldsForForm(): void
    {
        $field1 = $this->createTestField();
        $field2 = new Field();
        $field2->setForm($this->testForm);
        $field2->setSn('second_field');
        $field2->setType(FieldType::INTEGER);
        $field2->setTitle('Second Field');
        $field2->setValid(true);
        $this->em->persist($field2);
        $this->em->flush();

        $fields = $this->repository->findBy(['form' => $this->testForm]);
        self::assertGreaterThanOrEqual(2, count($fields));

        foreach ($fields as $field) {
            self::assertInstanceOf(Field::class, $field);
            $form = $field->getForm();
            self::assertNotNull($form);
            self::assertSame($this->testForm->getId(), $form->getId());
        }
    }

    public function testFindByTypeShouldReturnMatchingFields(): void
    {
        $field1 = $this->createTestField(); // STRING type
        $field2 = new Field();
        $field2->setForm($this->testForm);
        $field2->setSn('integer_field');
        $field2->setType(FieldType::INTEGER);
        $field2->setTitle('Integer Field');
        $field2->setValid(true);
        $this->em->persist($field2);
        $this->em->flush();

        $stringFields = $this->repository->findBy(['type' => FieldType::STRING]);
        self::assertNotEmpty($stringFields);

        foreach ($stringFields as $field) {
            self::assertSame(FieldType::STRING, $field->getType());
        }
    }

    public function testFindByRequiredTrueShouldReturnRequiredFields(): void
    {
        $requiredField = new Field();
        $requiredField->setForm($this->testForm);
        $requiredField->setSn('required_field');
        $requiredField->setType(FieldType::STRING);
        $requiredField->setTitle('Required Field');
        $requiredField->setRequired(true);
        $requiredField->setValid(true);
        $optionalField = new Field();
        $optionalField->setForm($this->testForm);
        $optionalField->setSn('optional_field');
        $optionalField->setType(FieldType::STRING);
        $optionalField->setTitle('Optional Field');
        $optionalField->setRequired(false);
        $optionalField->setValid(true);

        $this->em->persist($requiredField);
        $this->em->persist($optionalField);
        $this->em->flush();

        $requiredFields = $this->repository->findBy(['required' => true]);
        self::assertNotEmpty($requiredFields);

        foreach ($requiredFields as $field) {
            self::assertTrue($field->isRequired());
        }
    }

    public function testFindByValidTrueShouldReturnValidFields(): void
    {
        $validField = $this->createTestField();
        $invalidField = new Field();
        $invalidField->setForm($this->testForm);
        $invalidField->setSn('invalid_field');
        $invalidField->setType(FieldType::STRING);
        $invalidField->setTitle('Invalid Field');
        $invalidField->setValid(false);
        $this->em->persist($invalidField);
        $this->em->flush();

        $validFields = $this->repository->findBy(['valid' => true]);
        self::assertNotEmpty($validFields);

        foreach ($validFields as $field) {
            self::assertTrue($field->isValid());
        }
    }

    protected function createNewEntity(): object
    {
        $field = new Field();
        $field->setForm($this->testForm);
        $field->setSn('create_new_entity_' . uniqid());
        $field->setType(FieldType::STRING);
        $field->setTitle('Create New Entity Field');
        $field->setValid(true);

        return $field;
    }

    private function createTestField(): Field
    {
        $field = new Field();
        $field->setForm($this->testForm);
        $field->setSn('test_field_sn');
        $field->setType(FieldType::STRING);
        $field->setTitle('Test Field');
        $field->setPlaceholder('Enter test value');
        $field->setDescription('Test field description');
        $field->setRequired(false);
        $field->setMaxInput(100);
        $field->setSortNumber(1);
        $field->setValid(true);

        $this->em->persist($field);
        $this->em->flush();

        return $field;
    }

    /**
     * @return FieldRepository
     */
    protected function getRepository(): ServiceEntityRepository
    {
        return $this->repository;
    }
}
