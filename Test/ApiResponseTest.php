<?php

/*
 * Yate core products API wrapper library
 * (c) Alexey Pavlyuts <alexey@pavlyuts.ru>
 */

namespace YateApiTest;

use Yate\Api\ApiResponse;

/**
 * Tests for ApiResponse
 *
 */
class ApiResponseTest extends \PHPUnit\Framework\TestCase
{

    public function testAll()
    {
        $data = ['key1' => 'val1', 'key2' => 'val2'];
        $r = new ApiResponse($data);
        $this->assertTrue(isset($r['key1']));
        $this->assertTrue(isset($r->key2));
        $this->assertFalse(isset($r['key3']));
        $this->assertFalse(isset($r->key3));

        $this->assertEquals('val1', $r['key1']);
        $this->assertEquals('val2', $r->key2);

        $this->assertNull($r['key3']);
        $this->assertNull($r->key3);

        $r['key1'] = 'val3';
        $r->key2 = 'val4';
        $this->assertEquals('val1', $r['key1']);
        $this->assertEquals('val2', $r->key2);

        unset($r['key1']);
        unset($r->key2);
        $this->assertEquals('val1', $r['key1']);
        $this->assertEquals('val2', $r->key2);

        $this->assertEquals($data, $r->asArray());
    }
}
