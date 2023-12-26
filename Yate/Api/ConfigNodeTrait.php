<?php

/*
 * Yate core products API wrapper library
 * (c) Alexey Pavlyuts <alexey@pavlyuts.ru>
 */

namespace Yate\Api;

use Yate\Api\Exception\YateConfigException;

/**
 * Trait to handle Yate Node configuratons
 *
 */
trait ConfigNodeTrait
{

    protected array $uris = [];
    protected array $secrets = [];

    /**
     * Setup Yate API node records
     *
     * Usual, multiple yate 'nodes' may reside on one host and share one API
     * entry point.
     *
     * Then, we need each api entry point to be registered in the config with a lost of
     * 'nodes' and it's access secret.
     *
     * Mind that each 'node' may only be registered once, mean if you include
     * a 'node', say, 'hss' in two cosecutive calls of the methid, only last
     * record will survive.
     *
     * @param string[] $nodes Node's list of component installed, like
     * ['hss','smsc','ucn']
     * @param string $uri Node's api entry point, full URI like `'http://10.3.2.5/api.php'`
     * @param string $secret Node's secret to authorise access
     * @return self For method chaining
     */
    public function withNode(array $nodes, string $uri, string $secret): self
    {
        foreach ($nodes as $node) {
            $this->uris[$node] = $uri;
            $this->secrets[$node] = $secret;
        }
        return $this;
    }

    /**
     * Provides API URI for given node
     *
     * Should return API URI for e given node as string. If there no URI for a
     * node configured, YateConfigException must be thrown.
     *
     * @param string $node
     * @return string
     * @throws YateConfigException
     */
    public function getNodeUri(string $node): string
    {
        if (!isset($this->uris[$node])) {
            throw new YateConfigException("Uri not found for node '$node'");
        }
        return $this->uris[$node];
    }

    /**
     * Provides access secret for given node
     *
     * Should return access secret for a given node as string. If there no secret for a
     * node configured, YateConfigException must be thrown.
     *
     * @param string $node
     * @return string
     * @throws YateConfigException
     */
    public function getNodeSecret(string $node): string
    {
        if (!isset($this->secrets[$node])) {
            throw new YateConfigException("Secret not found for node '$node'");
        }
        return $this->secrets[$node];
    }
}
