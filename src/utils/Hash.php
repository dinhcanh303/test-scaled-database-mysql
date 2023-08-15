<?php

namespace Utils;

class Hash {
    public static function encode($shardId,$typeId,$localId){
        return ($shardId << 46) | ($typeId << 36) | ($localId << 0);
    }
    public static function decode($code,$option = 'shard'){
        $valueOptions = [];
        switch ($option) {
            case 'shard':
                $valueOptions = [46,0xFFFF];
                break;
            case 'type':
                $valueOptions = [36,0x3FF];
                break;
            case 'local':
                $valueOptions = [0,0xFFFFFFFFF];
                    break;
            default:
                return;
        }
        return ($code >> $valueOptions[0]) & $valueOptions[1];
    }
}