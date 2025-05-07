<?php

namespace DiyFormBundle\Event;

use DiyFormBundle\Entity\Record;

trait RecordAware
{
    private Record $record;

    public function getRecord(): Record
    {
        return $this->record;
    }

    public function setRecord(Record $record): void
    {
        $this->record = $record;
    }
}
