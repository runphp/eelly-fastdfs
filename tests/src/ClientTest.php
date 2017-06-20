<?php

declare(strict_types=1);
/*
 * PHP version 7.1
 *
 * @copyright Copyright (c) 2012-2017 EELLY Inc. (https://www.eelly.com)
 * @link      https://api.eelly.com
 * @license   衣联网版权所有
 */

namespace Eelly\FastDFS;

use PHPUnit\Framework\TestCase;

/**
 * @author hehui<hehui@eelly.net>
 */
class ClientTest extends TestCase
{
    /**
     * @var string
     */
    private $testFile;

    /**
     * @var Client
     */
    private $client;

    public function setUp(): void
    {
        $this->testFile = __DIR__.'/resources/test.jpg';
        $this->client = new Client([
            'host' => '172.18.107.96',
            'port' => 22122,
            'group' => [
                'G01',
                'G02',
            ],
        ]);
    }

    public function testUploadFile(): void
    {
        $result = $this->client->uploadFile($this->testFile);
        $this->assertStringStartsWith('G', $result);
    }

    public function testDeleteFile(): void
    {
        $filename = $this->client->uploadFile($this->testFile);
        $result = $this->client->deleteFile($filename);
        $this->assertTrue($result);
    }
}
