<?php

/*
 * Yate core products API wrapper library
 * (c) Alexey Pavlyuts <alexey@pavlyuts.ru>
 */

namespace YateApiTest;

use Yate\Api\Config;
use Yate\Api\Exception\YateConfigException;

/**
 * Test for ConfigNodesTrait
 *
 */
class ConfigTest extends \PHPUnit\Framework\TestCase
{

    public function testWithNode()
    {
        $config = new Config();
        $this->assertEquals($config, $config->withNode(['test'], 'http://test.org/api.php', 'bigSecret'));
        $this->assertEquals('http://test.org/api.php', $config->getNodeUri('test'));
        $this->assertEquals('bigSecret', $config->getNodeSecret('test'));
    }

    public function testWithNodeList()
    {
        $nodes = [
            ['nodes' => ['node1'], 'uri' => 'http://node1.dom/', 'secret' => 'secret1'],
            [['node2'], 'http://node2.dom/', 'secret2'],
            ['nodes' => ['node3'], 'uri' => 'http://node3.dom/', 'secret' => 'secret3'],
        ];
        $config = new Config();
        $this->assertEquals($config, $config->withNodeList($nodes));
        foreach ([1, 2, 3] as $i) {
            $this->assertEquals("http://node$i.dom/", $config->getNodeUri("node$i"));
            $this->assertEquals("secret$i", $config->getNodeSecret("node$i"));
        }
    }

    public function testUriNotFound()
    {
        $config = new Config();
        $this->expectException(YateConfigException::class);
        $this->expectExceptionMessage("Uri not found for node 'test'");
        $config->getNodeUri('test');
    }

    public function testSeretNotFound()
    {
        $config = new Config();
        $this->expectException(YateConfigException::class);
        $this->expectExceptionMessage("Secret not found for node 'test'");
        $config->getNodeSecret('test');
    }
}
