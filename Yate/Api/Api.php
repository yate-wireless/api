<?php

/*
 * Yate core products API wrapper library
 * (c) Alexey Pavlyuts <alexey@pavlyuts.ru>
 */

namespace Yate\Api;

use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Client\ClientExceptionInterface;
use Yate\Api\Exception\YateConnectException;
use Yate\Api\Exception\YateApiException;

/**
 * Yate core API wrapper
 *
 */
class Api
{

    protected const NODE = 'node';
    protected const REQUEST = 'request';
    protected const PARAMS = 'params';
    protected const CODE = 'code';
    protected const MESSAGE = 'message';
    protected const JSON_FLAGS = 0;

    protected ConfigInterface $config;

    /**
     * Setups API with ConfigInterface object
     *
     * @param ConfigInterface $config
     */
    public function __construct(ConfigInterface $config)
    {
        $this->config = $config;
    }

    /**
     * Generates Request object for given node, API endpoint (request) and params
     *
     * @param string $node Yate node name like 'ucn', 'hss', 'smsc', 'dra', e.t.c.
     * @param string $request Yate API 'request' string
     * @param array $params Array of params to pass
     * @return RequestInterface
     */
    public function prepareRequest(string $node, string $request, array $params = []): RequestInterface
    {
        return $this->config->createRequest($this->config->getNodeUri($node))
                        ->withHeader('Content-Type', 'application/json')
                        ->withHeader('X-Authentication', $this->config->getNodeSecret($node))
                        ->withBody(
                                $this->config->createStream(
                                        json_encode(
                                                [
                                                    self::NODE => $node,
                                                    self::REQUEST => $request,
                                                    self::PARAMS => $params,
                                                ],
                                                self::JSON_FLAGS
                                        )
                                )
        );
    }

    /**
     * Parse PSR-7 Response object from Yate API
     *
     * Chcks for any problems with answer and throws proper exceptions.
     * If the answer is a good one, retun it as {@see ApiResponse} instance
     *
     * @param ResponseInterface $response Response from API as PSR-7 object
     * @return ApiResponse Successfull response as ApiResponse object
     * @throws YateConnectException In a case of HTTP errors
     * @throws YateApiException In a case of API errors
     */
    public static function parseResult(ResponseInterface $response): ApiResponse
    {
        if ($response->getStatusCode() != 200) {
            throw new YateConnectException($response->getReasonPhrase(), $response->getStatusCode());
        }
        try {
            $result = json_decode($response->getBody(), true, 512, JSON_THROW_ON_ERROR);
        } catch (\JsonException $ex) {
            throw new YateApiException("JSON decode error: " . $ex->getMessage());
        }
        if (!isset($result[self::CODE])) {
            throw new YateApiException("Bad JSON content: 'code' not found");
        }
        if (0 != $result[self::CODE]) {
            throw new YateApiException($result[self::MESSAGE] ?? '', $result[self::CODE]);
        }
        unset($result[self::CODE]);
        return new ApiResponse($result);
    }

    /**
     * Performs API call
     *
     * @param string $node Yate node name like 'ucn', 'hss', 'smsc', 'dra', e.t.c.
     * @param string $request Yate API 'request' string
     * @param array $params Array of params to pass
     * @return ApiResponse Successfull response as ApiResponse object
     * @throws YateConnectException In a case of HTTP/connection errors
     * @throws YateApiException In a case of API errors
     */
    public function call(string $node, string $request, array $params = []): ApiResponse
    {
        try {
            return self::parseResult(
                            $this->config->getClient()->sendRequest(
                                    $this->prepareRequest($node, $request, $params)
                            )
            );
        } catch (ClientExceptionInterface $ex) {
            throw new YateConnectException($ex->getMessage(), $ex->getCode());
        }
    }
}
