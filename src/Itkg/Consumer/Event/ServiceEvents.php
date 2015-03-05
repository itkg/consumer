<?php

namespace Itkg\Consumer\Event;

/**
 * Class ServiceEvents
 *
 * @package Itkg\Consumer\Event
 */
final class ServiceEvents
{
    const REQUEST        = 'consumer.service.request';

    const RESPONSE       = 'consumer.service.response';

    const EXCEPTION      = 'consumer.service.exception';

    const PRE_CONFIGURE  = 'consumer.service.pre_configure';

    const POST_CONFIGURE = 'consumer.service.post_configure';
}
