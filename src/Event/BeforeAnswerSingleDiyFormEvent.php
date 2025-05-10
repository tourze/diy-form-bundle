<?php

namespace DiyFormBundle\Event;

use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Contracts\EventDispatcher\Event;

class BeforeAnswerSingleDiyFormEvent extends Event
{
    use FieldAware;

    private UserInterface $user;

    public function getUser(): UserInterface
    {
        return $this->user;
    }

    public function setUser(UserInterface $user): void
    {
        $this->user = $user;
    }

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
