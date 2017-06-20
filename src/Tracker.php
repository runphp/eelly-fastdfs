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

/**
 * Tracker.
 *
 * @author    hehui<hehui@eelly.net>
 */
class Tracker extends Base
{
    /**
     * 根据GroupName申请Storage地址
     *
     * @command 104
     *
     * @param string $group_name 组名称
     *
     * @return array/boolean
     */
    public function applyStorage($group_name)
    {
        $req_header = self::buildHeader(104, Base::GROUP_NAME_MAX_LEN);
        $req_body = self::padding($group_name, Base::GROUP_NAME_MAX_LEN);

        $this->send($req_header.$req_body);

        $res_header = $this->read(Base::HEADER_LENGTH);
        $res_info = self::parseHeader($res_header);

        if ($res_info['status'] !== 0) {
            throw new FastDFSException(
                'something wrong with get storage by group name',
                $res_info['status']);

            return false;
        }

        $res_body = (bool) $res_info['length']
            ? $this->read($res_info['length'])
            : '';

        $group_name = trim(substr($res_body, 0, Base::GROUP_NAME_MAX_LEN));
        $storage_addr = trim(substr($res_body, Base::GROUP_NAME_MAX_LEN,
            Base::IP_ADDRESS_SIZE - 1));

        list(, , $storage_port) = unpack('N2', substr($res_body,
            Base::GROUP_NAME_MAX_LEN + Base::IP_ADDRESS_SIZE - 1,
            Base::PROTO_PKG_LEN_SIZE));

        $storage_index = ord(substr($res_body, -1));

        return [
            'group_name' => $group_name,
            'storage_addr' => $storage_addr,
            'storage_port' => $storage_port,
            'storage_index' => $storage_index,
        ];
    }
}
