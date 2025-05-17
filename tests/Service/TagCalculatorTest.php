<?php

namespace DiyFormBundle\Tests\Service;

use DiyFormBundle\Entity\Data;
use DiyFormBundle\Entity\Field;
use DiyFormBundle\Entity\Option;
use DiyFormBundle\Entity\Record;
use DiyFormBundle\Event\AnswerTagCalcEvent;
use DiyFormBundle\Repository\DataRepository;
use DiyFormBundle\Service\TagCalculator;
use Doctrine\Common\Collections\ArrayCollection;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

class TagCalculatorTest extends TestCase
{
    private TagCalculator $tagCalculator;
    private LoggerInterface $logger;
    private EventDispatcherInterface $eventDispatcher;
    private DataRepository $dataRepository;

    protected function setUp(): void
    {
        $this->logger = $this->createMock(LoggerInterface::class);
        $this->eventDispatcher = $this->createMock(EventDispatcherInterface::class);
        $this->dataRepository = $this->createMock(DataRepository::class);
        
        $this->tagCalculator = new TagCalculator(
            $this->logger,
            $this->eventDispatcher,
            $this->dataRepository
        );
    }

    public function testFindByRecord_空数据返回空标签()
    {
        // 创建记录模拟对象
        $record = $this->createMock(Record::class);
        
        // 设置数据仓库返回空数据
        $this->dataRepository->expects($this->once())
            ->method('findBy')
            ->with(['record' => $record])
            ->willReturn([]);
        
        // 设置事件分发器行为
        $this->eventDispatcher->expects($this->once())
            ->method('dispatch')
            ->with($this->callback(function ($event) {
                return $event instanceof AnswerTagCalcEvent
                    && $event->getAnswerTags() === [];
            }))
            ->willReturnCallback(function ($event) {
                return $event;
            });
        
        // 调用并验证结果
        $result = $this->tagCalculator->findByRecord($record);
        $this->assertEquals([], $result);
    }

    public function testFindByRecord_数据包含标签选项()
    {
        // 创建记录模拟对象
        $record = $this->createMock(Record::class);
        
        // 创建选项1（包含标签）
        $option1 = $this->createMock(Option::class);
        $option1->method('getText')->willReturn('选项1');
        $option1->method('getTagList')->willReturn(['tag1', 'tag2']);
        
        // 创建选项2（包含标签）
        $option2 = $this->createMock(Option::class);
        $option2->method('getText')->willReturn('选项2');
        $option2->method('getTagList')->willReturn(['tag3']);
        
        // 创建选项3（没有标签）
        $option3 = $this->createMock(Option::class);
        $option3->method('getText')->willReturn('选项3');
        $option3->method('getTagList')->willReturn([]);
        
        // 创建字段，包含选项，使用ArrayCollection
        $field = $this->createMock(Field::class);
        $field->method('getOptions')->willReturn(new ArrayCollection([$option1, $option2, $option3]));
        
        // 创建数据对象
        $data = $this->createMock(Data::class);
        $data->method('getId')->willReturn('1');
        $data->method('getField')->willReturn($field);
        $data->method('getInputArray')->willReturn(['选项1', '选项3']); // 选择了选项1和选项3
        
        // 设置数据仓库返回测试数据
        $this->dataRepository->expects($this->once())
            ->method('findBy')
            ->with(['record' => $record])
            ->willReturn([$data]);
        
        // 设置事件分发器行为
        $this->eventDispatcher->expects($this->once())
            ->method('dispatch')
            ->with($this->callback(function ($event) {
                return $event instanceof AnswerTagCalcEvent
                    && $event->getAnswerTags() === ['tag1', 'tag2'];
            }))
            ->willReturnCallback(function ($event) {
                return $event;
            });
        
        // 调用并验证结果
        $result = $this->tagCalculator->findByRecord($record);
        $this->assertEquals(['tag1', 'tag2'], $result);
    }

    public function testFindByRecord_多个选项具有相同标签时去重()
    {
        // 创建记录模拟对象
        $record = $this->createMock(Record::class);
        
        // 创建选项1（包含标签）
        $option1 = $this->createMock(Option::class);
        $option1->method('getText')->willReturn('选项1');
        $option1->method('getTagList')->willReturn(['tag1', 'tag2']);
        
        // 创建选项2（包含标签，部分与选项1重复）
        $option2 = $this->createMock(Option::class);
        $option2->method('getText')->willReturn('选项2');
        $option2->method('getTagList')->willReturn(['tag2', 'tag3']);
        
        // 创建字段，包含选项，使用ArrayCollection
        $field = $this->createMock(Field::class);
        $field->method('getOptions')->willReturn(new ArrayCollection([$option1, $option2]));
        
        // 创建数据对象
        $data = $this->createMock(Data::class);
        $data->method('getId')->willReturn('1');
        $data->method('getField')->willReturn($field);
        $data->method('getInputArray')->willReturn(['选项1', '选项2']); // 选择了选项1和选项2
        
        // 设置数据仓库返回测试数据
        $this->dataRepository->expects($this->once())
            ->method('findBy')
            ->with(['record' => $record])
            ->willReturn([$data]);
        
        // 由于TagCalculator中使用了array_unique，我们预期最终的标签不会有重复
        $expectedTags = ['tag1', 'tag2', 'tag3'];
        
        // 设置事件分发器行为
        $this->eventDispatcher->expects($this->once())
            ->method('dispatch')
            ->with($this->callback(function ($event) use ($expectedTags) {
                $actualTags = $event->getAnswerTags();
                sort($actualTags); // 排序以便比较，因为array_values可能改变顺序
                
                return $event instanceof AnswerTagCalcEvent
                    && $actualTags === $expectedTags;
            }))
            ->willReturnCallback(function ($event) {
                return $event;
            });
        
        // 调用并验证结果
        $result = $this->tagCalculator->findByRecord($record);
        sort($result); // 排序以便比较
        $this->assertEquals($expectedTags, $result);
    }

    public function testFindByRecord_事件修改标签()
    {
        // 创建记录模拟对象
        $record = $this->createMock(Record::class);
        
        // 创建选项（包含标签）
        $option = $this->createMock(Option::class);
        $option->method('getText')->willReturn('选项1');
        $option->method('getTagList')->willReturn(['tag1']);
        
        // 创建字段，包含选项，使用ArrayCollection
        $field = $this->createMock(Field::class);
        $field->method('getOptions')->willReturn(new ArrayCollection([$option]));
        
        // 创建数据对象
        $data = $this->createMock(Data::class);
        $data->method('getId')->willReturn('1');
        $data->method('getField')->willReturn($field);
        $data->method('getInputArray')->willReturn(['选项1']);
        
        // 设置数据仓库返回测试数据
        $this->dataRepository->expects($this->once())
            ->method('findBy')
            ->with(['record' => $record])
            ->willReturn([$data]);
        
        // 设置事件分发器行为，模拟事件处理器添加新标签
        $this->eventDispatcher->expects($this->once())
            ->method('dispatch')
            ->willReturnCallback(function ($event) {
                $event->setAnswerTags([...$event->getAnswerTags(), 'added_by_event']);
                return $event;
            });
        
        // 调用并验证结果
        $result = $this->tagCalculator->findByRecord($record);
        $this->assertEquals(['tag1', 'added_by_event'], $result);
    }
} 