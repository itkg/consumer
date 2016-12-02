<?php

namespace Itkg\Consumer\Cache;

/**
 * Class WarmupQueue
 */
final class WarmupQueue
{
    const KEY_NAME = 'warmup_queue';

    const STATUS_REFRESH = 'REFRESH';

    const STATUS_LOCKED = 'LOCKED';
}
