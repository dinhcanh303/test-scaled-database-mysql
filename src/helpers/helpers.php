<?php

if (! function_exists('create_name_database')) {
    function create_name_database($name,$type = 'a')
    {
        return 'db'.sprintf("%05d",$name).$type;
    }
}

