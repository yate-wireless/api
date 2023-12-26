<?php

/*
 * Yate core products API wrapper library
 * (c) Alexey Pavlyuts <alexey@pavlyuts.ru>
 */

namespace Yate\Api;

use Yate\Api\Exception\YateException;

/**
 * Interface for configuration object
 *
 * Configuration object stores core API configutarion, including access keys and
 * all external bindings.
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
}
