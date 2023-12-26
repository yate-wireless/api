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

    public function testSet()
    {
        $config = new Config();
        $this->assertEquals($config, $config->withNode(['test'], 'http://test.org/api.php', 'bigSecret'));
        $this->assertEquals('http://test.org/api.php', $config->getNodeUri('test'));
        $this->assertEquals('bigSecret', $config->getNodeSecret('test'));
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
