<?php

namespace Itkg\Consume\Service;


final class Events
{
    const PRE_CALL = 'consume.service.pre_call';
    const BIND_REQUEST = 'consume.service.bind_request';
    const POST_CALL = 'consume.service.post_call';
    const SUCCESS_CALL = 'consume.service.success_call';
    const FAIL_CALL = 'consume.service.fail_call';
    const BIND_RESPONSE = 'consume.service.bind_response';
}