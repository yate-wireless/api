<?php

/*
 * Yate core products API wrapper library
 * (c) Alexey Pavlyuts <alexey@pavlyuts.ru>
 */

namespace YateApiTest;

use Yate\Api\ConfigNodeTrait;
use Yate\Api\Exception\YateConfigException;

/**
 * Test for ConfigNodesTrait
 *
 */
class ConfigNodesTraitTest extends \PHPUnit\Framework\TestCase
{

    public function testSet()
    {
        $t = $this->getMockForTrait(ConfigNodeTrait::class);
        $this->assertEquals($t, $t->withNode(['test'], 'http://test.org/api.php', 'bigSecret'));
        $this->assertEquals('http://test.org/api.php', $t->getNodeUri('test'));
        $this->assertEquals('bigSecret', $t->getNodeSecret('test'));
    }

    public function testUriNotFound()
    {
        $t = $this->getMockForTrait(ConfigNodeTrait::class);
        $this->expectException(YateConfigException::class);
        $this->expectExceptionMessage("Uri not found for node 'test'");
        $t->getNodeUri('test');
    }

    public function testSeretNotFound()
    {
        $t = $this->getMockForTrait(ConfigNodeTrait::class);
        $this->expectException(YateConfigException::class);
        $this->expectExceptionMessage("Secret not found for node 'test'");
        $t->getNodeSecret('test');
    }
}
