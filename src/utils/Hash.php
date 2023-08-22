<?php

namespace Utils;

class Hash {
    public static function encode($shardId,$typeId,$localId){
        return ($shardId << 46) | ($typeId << 36) | ($localId << 0);
    }
    public static function decode(string|int $code, string|bool $options = 'shard'){
        $results = [];
        $options === true ? $options = ['shard','type','local'] : $options = [$options];
        foreach ($options as $option) {
            if(!static::matchOptions($option)) return null;
            [$type,$valueOptions] = static::matchOptions($option);
            $results[$type] = ($code >> $valueOptions[0]) & $valueOptions[1] ;
        }
        return $results;
    }
    private static function matchOptions($option){
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
                return false;
        }
        return [$option,$valueOptions];
    }
}