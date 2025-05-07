<?php

namespace DiyFormBundle\Event;

use AppBundle\Event\HaveUserAware;
use Symfony\Contracts\EventDispatcher\Event;

class BeforeAnswerSingleDiyFormEvent extends Event
{
    use FieldAware;
    use HaveUserAware;

    protected $input;

    /**
     * @return null
     */
    public function getInput()
    {
        return $this->input;
    }

    /**
     * @param null $input
     */
    public function setInput($input): void
    {
        $this->input = $input;
    }
}
