<?php

namespace Itkg\Consumer\Service;


final class Events
{
    const PRE_CALL             = 'consumer.service.pre_call';
    const BIND_REQUEST         = 'consumer.service.bind_request';
    const POST_CALL            = 'consumer.service.post_call';
    const SUCCESS_CALL         = 'consumer.service.success_call';
    const FAIL_CALL            = 'consumer.service.fail_call';
    const BIND_RESPONSE        = 'consumer.service.bind_response';
    const FROM_CACHE           = 'consumer.service.from_cache';
    const PRE_AUTHENTICATE     = 'consumer.service.pre_authenticate';
    const FAIL_AUTHENTICATE    = 'consumer.service.fail_authenticate';
    const SUCCESS_AUTHENTICATE = 'consumer.service.success_authenticate';
}