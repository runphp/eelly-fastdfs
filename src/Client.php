<?php

declare(strict_types=1);
/*
 * This file is part of eelly package.
 *
 * (c) eelly.com
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Eelly\FastDFS;

use Psr\Http\Message\UploadedFileInterface;

/**
 * fastdfs client.
 *
 * ```
 * new Client([
 *   'host' => '172.18.107.96',
 *   'port' => 22122,
 *   'group' => [
 *       'G01',
 *       'G02',
 *   ],
 * ]);
 * ```
 *
 */
class Client
{
    /**
     * @var Tracker
     */
    private $tracker;

    /**
     * @var array
     */
    private $storageInfo;

    /**
     * @var Storage
     */
    private $storage;

    public function __construct(array $config)
    {
        shuffle($config['group']);
        $this->tracker = new Tracker($config['host'], $config['port']);
        $this->storageInfo = $this->tracker->applyStorage(current($config['group']));
        $this->storage = new Storage($this->storageInfo['storage_addr'], $this->storageInfo['storage_port']);
    }

    /**
     * 上传文件.
     *
     * @param UploadedFileInterface $file
     * @param string                $ext
     *
     * @return string
     */
    public function writeUploadedFile(UploadedFileInterface $file, string $ext = '')
    {
        return $this->uploadFile($file->getStream()->getMetadata('uri'), $ext);
    }

    /**
     * 上传文件.
     *
     *
     * @param stirng $filename 文件路径
     * @param string $ext      扩展名
     *
     * @return string 文件路径
     */
    public function uploadFile($filename, $ext = '')
    {
        $result = $this->getStorage()->uploadFile($this->getStorageInfo()['storage_index'], $filename, $ext);

        return $result['group'].'/'.$result['path'];
    }

    /**
     * 删除文件.
     *
     *
     * @param string $filename 文件路径
     *
     * @return bool
     */
    public function deleteFile($filename)
    {
        list($groupName, $filePath) = explode('/', $filename, 2);
        $result = $this->getStorage()->deleteFile($groupName, $filePath);

        return $result;
    }

    private function getStorageInfo()
    {
        return $this->storageInfo;
    }

    private function getStorage()
    {
        return $this->storage;
    }
}
