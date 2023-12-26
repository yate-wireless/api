<?php

/*
 * Yate core products API wrapper library
 * (c) Alexey Pavlyuts <alexey@pavlyuts.ru>
 */

namespace Yate\Api;

use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\StreamInterface;
use Psr\Http\Client\ClientInterface;
use Yate\Api\Exception\YateException;

/**
 * Interface for configuration object
 *
 * Configuration object stores core API configutarion, including access keys and
 * all external bindings.
 * Also it works as a factory for Request and Stream to collect all dependencies
 * in one class.
 */
interface ConfigInterface
{

    /**
     * Provides API URI for given node
     *
     * Should return API URI for e given node as string. If there no URI for a
     * node configured, YateException must be thrown.
     *
     * @param string $node
     * @return string
     * @throws YateException
     */
    public function getNodeUri(string $node): string;

    /**
     * Provides access secret for given node
     *
     * Should return access secret for a given node as string. If there no secret for a
     * node configured, YateException must be thrown.
     *
     * @param string $node
     * @return string
     * @throws YateException
     */
    public function getNodeSecret(string $node): string;

    /**
     * Creates POST Request object with given URI
     *
     * @param string $uri
     * @return RequestInterface
     */
    public function createRequest(string $uri): RequestInterface;

    /**
     * Creates Stream with given content
     *
     * @param string $content
     * @return StreamInterface
     */
    public function createStream(string $content): StreamInterface;

    /**
     * Provides PSR-18 HTTP Client object
     *
     * Returns HTTP client instance which API will use to call the core
     *
     * @return ClientInterface
     */
    public function getClient(): ClientInterface;
}
