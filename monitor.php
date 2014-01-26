<?php
/**
 * @fileoverview 
 * @author daxingplay<daxingplay@gmail.com>
 * @time: 1/26/14 11:22
 * @description
 */

require_once "lib/solus.class.php";

if(!file_exists('./config.php')){
    echo 'cannot find config file. please copy config.sample.php as config.php.';
    exit();
}

$config = require_once './config.php';

foreach($config['servers'] as $server_config){
    $server = new Solus($server_config['api'], $server_config['key'], $server_config['hash']);
    $status = $server->status();
    $ip = $status['ipaddress'];
    $hostname = $status['hostname'];

    if($status['statusmsg'] === 'online'){
        echo $hostname.' is running.';
    }else{
        echo $hostname.' is down. trying to boot...';
        $result = $server->boot();
        if($result){
            echo $hostname.' is running.';
        }else{
            echo $hostname.' boot failed.';
        }
    }
    echo "\n";
//    echo $server->getServerStatus();
}