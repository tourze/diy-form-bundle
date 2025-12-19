<?php

declare(strict_types=1);

namespace DiyFormBundle\Event;

use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Contracts\EventDispatcher\Event;

final class BeforeAnswerSingleDiyFormEvent extends Event
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

    protected mixed $input;

    public function getInput(): mixed
    {
        return $this->input;
    }

    public function setInput(mixed $input): void
    {
        $this->input = $input;
    }
}
