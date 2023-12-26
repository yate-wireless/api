<?php

/*
 * Yate core products API wrapper library
 * (c) Alexey Pavlyuts <alexey@pavlyuts.ru>
 */

namespace YateApiTest;

use Psr\Http\Client\ClientExceptionInterface;

/**
 * Implementation of ClientExceptionInterface for tests
 *
 */
class TestClientException extends \Exception implements ClientExceptionInterface
{

}
