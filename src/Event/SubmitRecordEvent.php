<?php

namespace DiyFormBundle\Event;

use AppBundle\Event\HaveUserAware;
use Symfony\Contracts\EventDispatcher\Event;

class SubmitRecordEvent extends Event
{
    use RecordAware;
    use HaveUserAware;
}
