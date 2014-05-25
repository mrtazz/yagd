<?php

$CONFIG = [
    'title' => "cool dashboards",
    'navitems' => [
        'Hosts' => '/hosts.php',
        'Graphite' => '/graphite.php'
    ],
    'graphitehost' => "https://graphite.example.com",
    'hosts' => [
        "foo.example.com" => [
            "cpus" => 2,
            "apache" => true,
            "interfaces" => ['eth0'],
            "filesystems" => [ 'root', 'var' ]
            ]
        ]
    ];
