<?php

declare(strict_types=1);

namespace DiyFormBundle\Service;

use DiyFormBundle\Entity\Data;
use DiyFormBundle\Entity\Field;
use DiyFormBundle\Entity\Option;
use DiyFormBundle\Entity\Record;
use DiyFormBundle\Event\AnswerTagCalcEvent;
use DiyFormBundle\Repository\DataRepository;
use Monolog\Attribute\WithMonologChannel;
use Psr\Log\LoggerInterface;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

/**
 * 标签计算.
 */
#[WithMonologChannel(channel: 'diy_form')]
readonly class TagCalculator
{
    public function __construct(
        private LoggerInterface $logger,
        private EventDispatcherInterface $eventDispatcher,
        private DataRepository $dataRepository,
    ) {
    }

    /**
     * @return array<string>
     */
    public function findByRecord(Record $record): array
    {
        $answerTags = $this->calculateTagsFromData($record);
        $answerTags = array_values(array_unique($answerTags));

        return $this->dispatchTagCalculationEvent($record, $answerTags);
    }

    /**
     * @return array<string>
     */
    private function calculateTagsFromData(Record $record): array
    {
        $answerTags = [];
        $dataList = $this->dataRepository->findBy(['record' => $record]);

        foreach ($dataList as $data) {
            if ($data instanceof Data) {
                $answerTags = $this->processDataInput($data, $answerTags);
            }
        }

        return $answerTags;
    }

    /**
     * @param array<string> $answerTags
     *
     * @return array<string>
     */
    private function processDataInput(Data $data, array $answerTags): array
    {
        $input = $data->getInputArray();
        $this->logger->debug("[{$data->getId()}]计算输入最终拆分出来的input值", [
            'input' => $input,
            'data' => $data,
        ]);

        $field = $data->getField();
        if (null === $field) {
            return $answerTags;
        }

        return $this->processFieldOptions($field, $input, $answerTags);
    }

    /**
     * @param list<string>  $input
     * @param array<string> $answerTags
     *
     * @return array<string>
     */
    private function processFieldOptions(Field $field, array $input, array $answerTags): array
    {
        foreach ($field->getOptions() as $option) {
            $tags = $option->getTagList();
            if (0 === count($tags)) {
                continue;
            }

            $answerTags = $this->checkOptionMatch($option, $input, $tags, $answerTags);
        }

        return $answerTags;
    }

    /**
     * @param list<string>  $input
     * @param list<string>  $tags
     * @param array<string> $answerTags
     *
     * @return array<string>
     */
    private function checkOptionMatch(Option $option, array $input, array $tags, array $answerTags): array
    {
        foreach ($input as $item) {
            if ($option->getText() === $item) {
                $answerTags = [
                    ...$answerTags,
                    ...$tags,
                ];
            }
        }

        return $answerTags;
    }

    /**
     * @param array<string> $answerTags
     *
     * @return array<string>
     */
    private function dispatchTagCalculationEvent(Record $record, array $answerTags): array
    {
        $event = new AnswerTagCalcEvent();
        $event->setRecord($record);
        $event->setAnswerTags($answerTags);
        $this->eventDispatcher->dispatch($event);

        return $event->getAnswerTags();
    }
}
