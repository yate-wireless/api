<?php

/*
 * Yate core products API wrapper library
 * (c) Alexey Pavlyuts <alexey@pavlyuts.ru>
 */

namespace YateApiTest;

use Yate\Api\Api;
use Yate\Api\Exception\YateConnectException;
use Yate\Api\Exception\YateApiException;
use Yate\Api\ConfigInterface;
use GuzzleHttp\Psr7;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestFactoryInterface;
use Psr\Http\Message\StreamFactoryInterface;

/**
 * Tests for Api
 *
 */
class ApiTest extends \PHPUnit\Framework\TestCase
{

    const NODE = 'test';
    const URI = 'http://10.1.2.3/api.php';
    const SECRET = 'bigSecret';
    const REQUEST = 'test_yate';
    const PARAMS = ['param1' => 'value1', 'param2' => 2];

    public function testPrepareRequest()
    {
        $bodyArray = [
            'node' => self::NODE,
            'request' => self::REQUEST,
            'params' => self::PARAMS
        ];
        $bodyJson = json_encode($bodyArray);

        $config = $this->getMockForAbstractClass(ConfigInterface::class);
        $config->expects($this->once())
                ->method('getNodeUri')
                ->with(self::NODE)
                ->willReturn(self::URI);
        $config->expects($this->once())
                ->method('getNodeSecret')
                ->with(self::NODE)
                ->willReturn(self::SECRET);

        $reqiestFactory = $this->getMockForAbstractClass(RequestFactoryInterface::class);
        $reqiestFactory->expects($this->once())
                ->method('createRequest')
                ->with('POST', self::URI)
                ->willReturn(new Psr7\Request('POST', self::URI));

        $streamFactory = $this->getMockForAbstractClass(StreamFactoryInterface::class);
        $streamFactory->expects($this->once())
                ->method('createStream')
                ->with($bodyJson)
                ->willReturn(Psr7\Utils::streamFor($bodyJson));

        $client = $this->getMockForAbstractClass(ClientInterface::class);

        $api = new Api($config, $reqiestFactory, $streamFactory, $client);

        $q = $api->prepareRequest(self::NODE, self::REQUEST, self::PARAMS);
        $this->assertEquals('POST', $q->getMethod());
        $this->assertEquals(self::URI, (string) $q->getUri());
        $this->assertEquals(self::SECRET, $q->getHeader('X-Authentication')[0]);
        $this->assertEquals('application/json', $q->getHeader('Content-Type')[0]);
        $this->assertEquals($bodyJson, (string) $q->getBody());
    }

    public function testParseResultNormal()
    {
        $result = ['key1' => 'val1', 'key2' => 'val2'];
        $resultJson = json_encode(array_merge($result, ['code' => 0]));
        $response = new Psr7\Response(200, ['Content-Type' => 'application/json'], $resultJson);
        $apiResponse = Api::parseResult($response);
        $this->assertEquals($result, $apiResponse->asArray());
    }

    public function testParseResultHttpError()
    {
        $this->expectException(YateConnectException::class);
        $this->expectExceptionMessage("Something goes wrong");
        Api::parseResult(
                new Psr7\Response(501, [], null, 1.1, "Something goes wrong")
        );
    }

    public function testParseResultBadJson()
    {
        $this->expectException(YateApiException::class);
        $this->expectExceptionMessageMatches("/^JSON decode error: .*$/");
        Api::parseResult(
                new Psr7\Response(200, [], 'notJson')
        );
    }

    public function testParseResultNoCode()
    {
        $this->expectException(YateApiException::class);
        $this->expectExceptionMessage("Bad JSON content: 'code' not found");
        Api::parseResult(
                new Psr7\Response(200, [], '{"key1":"val1"}')
        );
    }

    public function testParseResultErrorCode()
    {
        $this->expectException(YateApiException::class);
        $this->expectExceptionMessage("API error message");
        Api::parseResult(
                new Psr7\Response(200, [], '{"code": 502,"message": "API error message"}')
        );
    }

    protected function preapareTestCall($client, $request)
    {
        $api = $this->getMockBuilder(Api::class)
                ->setConstructorArgs(
                        [
                            $this->getMockForAbstractClass(ConfigInterface::class),
                            $this->getMockForAbstractClass(RequestFactoryInterface::class),
                            $this->getMockForAbstractClass(StreamFactoryInterface::class),
                            $client
                        ]
                )
                ->onlyMethods(['prepareRequest'])
                ->getMock();
        $api->expects($this->once())
                ->method('prepareRequest')
                ->willReturn($request);
        return $api;
    }

    public function testCall()
    {
        $request = new Psr7\Request('POST', self::URI);
        $response = new Psr7\Response(200, [], '{"code":0, "result":"ok"}');

        $client = $this->getMockForAbstractClass(\Psr\Http\Client\ClientInterface::class);
        $client->expects($this->once())
                ->method('sendRequest')
                ->with($request)
                ->willReturn($response);

        $api = $this->preapareTestCall($client, $request);

        $answer = $api->call(self::NODE, self::URI, []);
        $this->assertEquals('ok', $answer->result);
    }

    public function testCallException()
    {
        $request = new Psr7\Request('POST', self::URI);
        $response = new Psr7\Response(200, [], '{"code":0, "result":"ok"}');

        $client = $this->getMockForAbstractClass(\Psr\Http\Client\ClientInterface::class);
        $client->expects($this->once())
                ->method('sendRequest')
                ->will($this->throwException(
                                new TestClientException("Client error message", 99)
                        )
        );

        $api = $this->preapareTestCall($client, $request);

        $this->expectException(YateConnectException::class);
        $this->expectExceptionMessage("Client error message");
        $this->expectExceptionCode(99);
        $api->call(self::NODE, self::URI, []);
    }
}
