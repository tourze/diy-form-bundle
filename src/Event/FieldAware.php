<?php

declare(strict_types=1);

namespace DiyFormBundle\Event;

use DiyFormBundle\Entity\Field;

trait FieldAware
{
    private Field $field;

    public function getField(): Field
    {
        return $this->field;
    }

    public function setField(Field $field): void
    {
        $this->field = $field;
    }
}
