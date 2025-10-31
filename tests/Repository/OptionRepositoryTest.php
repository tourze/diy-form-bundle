<?php

declare(strict_types=1);

namespace DiyFormBundle\Tests\Repository;

use DiyFormBundle\Entity\Field;
use DiyFormBundle\Entity\Form;
use DiyFormBundle\Entity\Option;
use DiyFormBundle\Enum\FieldType;
use DiyFormBundle\Repository\OptionRepository;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Tourze\PHPUnitSymfonyKernelTest\AbstractRepositoryTestCase;

/**
 * @internal
 */
#[CoversClass(OptionRepository::class)]
#[RunTestsInSeparateProcesses]
final class OptionRepositoryTest extends AbstractRepositoryTestCase
{
    private EntityManagerInterface $em;

    private OptionRepository $repository;

    private Form $testForm;

    private Field $testField;

    protected function onSetUp(): void
    {
        $this->em = self::getEntityManager();
        $repository = $this->em->getRepository(Option::class);
        self::assertInstanceOf(OptionRepository::class, $repository);
        $this->repository = $repository;

        // 创建测试用的Form
        $this->testForm = new Form();
        $this->testForm->setTitle('Test Form for Option');
        $this->testForm->setStartTime(new \DateTimeImmutable());
        $this->testForm->setEndTime(new \DateTimeImmutable('+1 month'));
        $this->em->persist($this->testForm);

        // 创建测试用的Field
        $this->testField = new Field();
        $this->testField->setForm($this->testForm);
        $this->testField->setSn('test_field_001');
        $this->testField->setType(FieldType::SINGLE_SELECT);
        $this->testField->setTitle('Test Field with Options');
        $this->testField->setValid(true);
        $this->em->persist($this->testField);

        $this->em->flush();
    }

    public function testSave(): void
    {
        $option = new Option();
        $option->setField($this->testField);
        $option->setSn('save_test_option');
        $option->setText('Save Test Option');
        $option->setDescription('Option for save test');
        $option->setAllowInput(false);
        $option->setAnswer(false);

        $this->repository->save($option, true);

        self::assertGreaterThan(0, $option->getId());

        $foundOption = $this->repository->find($option->getId());
        self::assertInstanceOf(Option::class, $foundOption);
        self::assertSame('Save Test Option', $foundOption->getText());
    }

    public function testRemove(): void
    {
        $option = $this->createTestOption();
        $optionId = $option->getId();

        $this->repository->remove($option, true);

        $foundOption = $this->repository->find($optionId);
        self::assertNull($foundOption);
    }

    public function testFindOneByWithValidFieldShouldReturnEntity(): void
    {
        $option = $this->createTestOption();

        $foundOption = $this->repository->findOneBy(['sn' => 'test_option_sn']);
        self::assertInstanceOf(Option::class, $foundOption);
        self::assertSame($option->getId(), $foundOption->getId());
    }

    public function testFindOneByWithNullFieldShouldReturnMatchingEntity(): void
    {
        $option = new Option();
        $option->setField($this->testField);
        $option->setSn('null_description_option');
        $option->setText('Null Description Option');
        $option->setDescription(null);
        $option->setAllowInput(false);
        $option->setAnswer(false);
        $this->em->persist($option);
        $this->em->flush();

        $foundOption = $this->repository->findOneBy(['description' => null]);
        self::assertInstanceOf(Option::class, $foundOption);
        self::assertNull($foundOption->getDescription());
    }

    public function testFindByFieldShouldReturnAllOptionsForField(): void
    {
        $option1 = $this->createTestOption();
        $option2 = new Option();
        $option2->setField($this->testField);
        $option2->setSn('second_option');
        $option2->setText('Second Option');
        $option2->setDescription('Second option description');
        $option2->setAllowInput(false);
        $option2->setAnswer(false);
        $this->em->persist($option2);
        $this->em->flush();

        $options = $this->repository->findBy(['field' => $this->testField]);
        self::assertGreaterThanOrEqual(2, count($options));

        foreach ($options as $option) {
            self::assertInstanceOf(Option::class, $option);
            self::assertNotNull($option->getField());
            self::assertSame($this->testField->getId(), $option->getField()->getId());
        }
    }

    public function testFindByAnswerTrueShouldReturnCorrectAnswers(): void
    {
        $correctOption = new Option();
        $correctOption->setField($this->testField);
        $correctOption->setSn('correct_option');
        $correctOption->setText('Correct Answer');
        $correctOption->setDescription('This is the correct answer');
        $correctOption->setAllowInput(false);
        $correctOption->setAnswer(true);
        $incorrectOption = new Option();
        $incorrectOption->setField($this->testField);
        $incorrectOption->setSn('incorrect_option');
        $incorrectOption->setText('Incorrect Answer');
        $incorrectOption->setDescription('This is not the correct answer');
        $incorrectOption->setAllowInput(false);
        $incorrectOption->setAnswer(false);

        $this->em->persist($correctOption);
        $this->em->persist($incorrectOption);
        $this->em->flush();

        $correctAnswers = $this->repository->findBy(['answer' => true]);
        self::assertNotEmpty($correctAnswers);

        foreach ($correctAnswers as $option) {
            self::assertTrue($option->isAnswer());
        }
    }

    public function testFindByAllowInputTrueShouldReturnInputAllowedOptions(): void
    {
        $inputAllowedOption = new Option();
        $inputAllowedOption->setField($this->testField);
        $inputAllowedOption->setSn('input_allowed_option');
        $inputAllowedOption->setText('Input Allowed Option');
        $inputAllowedOption->setDescription('User can input custom text');
        $inputAllowedOption->setAllowInput(true);
        $inputAllowedOption->setAnswer(false);
        $fixedOption = new Option();
        $fixedOption->setField($this->testField);
        $fixedOption->setSn('fixed_option');
        $fixedOption->setText('Fixed Option');
        $fixedOption->setDescription('Fixed option text');
        $fixedOption->setAllowInput(false);
        $fixedOption->setAnswer(false);

        $this->em->persist($inputAllowedOption);
        $this->em->persist($fixedOption);
        $this->em->flush();

        $inputOptions = $this->repository->findBy(['allowInput' => true]);
        self::assertNotEmpty($inputOptions);

        foreach ($inputOptions as $option) {
            self::assertTrue($option->isAllowInput());
        }
    }

    public function testFindByTagsShouldReturnMatchingOptions(): void
    {
        $taggedOption = new Option();
        $taggedOption->setField($this->testField);
        $taggedOption->setSn('tagged_option');
        $taggedOption->setText('Tagged Option');
        $taggedOption->setTags('category1,special');
        $taggedOption->setAllowInput(false);
        $taggedOption->setAnswer(false);
        $untaggedOption = new Option();
        $untaggedOption->setField($this->testField);
        $untaggedOption->setSn('untagged_option');
        $untaggedOption->setText('Untagged Option');
        $untaggedOption->setTags(null);
        $untaggedOption->setAllowInput(false);
        $untaggedOption->setAnswer(false);

        $this->em->persist($taggedOption);
        $this->em->persist($untaggedOption);
        $this->em->flush();

        $taggedOptions = $this->repository->findBy(['tags' => 'category1,special']);
        self::assertNotEmpty($taggedOptions);

        foreach ($taggedOptions as $option) {
            self::assertSame('category1,special', $option->getTags());
        }
    }

    protected function createNewEntity(): object
    {
        $option = new Option();
        $option->setField($this->testField);
        $option->setSn('create_new_entity_' . uniqid());
        $option->setText('Create New Entity Option');
        $option->setAllowInput(false);
        $option->setAnswer(false);

        return $option;
    }

    private function createTestOption(): Option
    {
        $option = new Option();
        $option->setField($this->testField);
        $option->setSn('test_option_sn');
        $option->setText('Test Option');
        $option->setDescription('Test option description');
        $option->setTags('test,option');
        $option->setAllowInput(false);
        $option->setAnswer(false);
        $option->setIcon('test-icon.png');
        $option->setSelectedIcon('test-selected-icon.png');
        $option->setMutex(null);

        $this->em->persist($option);
        $this->em->flush();

        return $option;
    }

    /**
     * @return OptionRepository
     */
    protected function getRepository(): ServiceEntityRepository
    {
        return $this->repository;
    }
}
