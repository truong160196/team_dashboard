<?php

return [

    'default' => 'production',

    'connections' => [
        'production' => [
            'host'      => '192.168.1.215:22',
            'username'  => 'root',
            'password'  => 'arisvn1369', // no password
            'key'       => '',
            'keytext'   => '',
            'keyphrase' => '',
            'agent'     => '',
            'timeout'   => 10,
        ],
        'staging' => [
            'host'      => '222.222.222.222:22',
            'username'  => 'staging',
            'password'  => 'stagingpass',
            'timeout'   => 10,
        ],
        'test' => [
            'host'      => '3.3.3.3:3',
            'username'  => 'test',
            'password'  => 'testpass',
            'timeout'   => 10,
        ],
    ],

    'groups' => [
        'web' => ['production'],
    ],

];