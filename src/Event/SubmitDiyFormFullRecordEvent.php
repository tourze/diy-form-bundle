<?php

namespace DiyFormBundle\Event;

use Tourze\JsonRPCEndpointBundle\Traits\AppendJsonRpcResultAware;
use Tourze\UserEventBundle\Event\UserInteractionEvent;

/**
 * 提交完整的表单数据时触发
 */
class SubmitDiyFormFullRecordEvent extends UserInteractionEvent
{
    use RecordAware;
    use AppendJsonRpcResultAware;
}
