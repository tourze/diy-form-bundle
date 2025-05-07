<?php

namespace DiyFormBundle\Service;

use DiyFormBundle\Entity\Record;
use DiyFormBundle\Event\AnswerTagCalcEvent;
use DiyFormBundle\Repository\DataRepository;
use Psr\Log\LoggerInterface;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

/**
 * 标签计算
 */
class TagCalculator
{
    public function __construct(
        private readonly LoggerInterface $logger,
        private readonly EventDispatcherInterface $eventDispatcher,
        private readonly DataRepository $dataRepository,
    ) {
    }

    public function findByRecord(Record $record): array
    {
        // 根据当前Session的答题情况，我们计算一下 answerTags
        $answerTags = [];
        foreach ($this->dataRepository->findBy(['record' => $record]) as $data) {
            $input = $data->getInputArray();
            $this->logger->debug("[{$data->getId()}]计算输入最终拆分出来的input值", [
                'input' => $input,
                'data' => $data,
            ]);

            if ($data->getField()) {
                foreach ($data->getField()->getOptions() as $option) {
                    $_tags = $option->getTagList();
                    if (empty($_tags)) {
                        continue;
                    }

                    foreach ($input as $item) {
                        if ($option->getText() === $item) {
                            $answerTags = [
                                ...$answerTags,
                                ...$_tags,
                            ];
                        }
                    }
                }
            }
        }

        $answerTags = array_values(array_unique($answerTags));

        // 这里留给第三方，加一些额外的标签
        $event = new AnswerTagCalcEvent();
        $event->setRecord($record);
        $event->setAnswerTags($answerTags);
        $this->eventDispatcher->dispatch($event);

        return $event->getAnswerTags();
    }
}
