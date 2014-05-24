# yagd

## Overview
Yet Another Graphite Dashboard - because why not? It's heavily inspired by
[the Etsy dashboard framework](https://github.com/etsy/dashboard) but only
provides a very small subset of features. If you have a lot of hosts or need
advanced features I'd recommend checking that out.

## Requirements
- PHP >= 5.4
- Graphite

## Installation
- clone the repo
- copy `config.example.php` to `config.php` and adapt it

## Usage examples

### Generic dashboards
There is a generic `page.php` file included which will just include all
Graphite graphs you have in an array called `$metrics`:

```
<?php

$metrics = array(
    'carbon.agents.foo_example_com-a.committedPoints',
    'carbon.agents.foo_example_com-a.cpuUsage',
    'carbon.agents.foo_example_com-a.avgUpdateTime',
    'carbon.agents.foo_example_com-a.creates',
    'carbon.agents.foo_example_com-a.errors',
    'carbon.agents.foo_example_com-a.metricsReceived',
    'carbon.agents.foo_example_com-a.pointsPerUpdate',
    'carbon.agents.foo_example_com-a.updateOperations',
);

include_once("../phplib/page.php");
```

### Display CollectD host data
If you are using collectd to gather system level graphs you can draw basic
information onto a dashboard like this:

Configure hosts in your `config.php`

```
$CONFIG['hosts'] = [
        "foo.example.com" => [
            "cpus" => 2,
            "apache" => true,
            "filesystems" => [ 'root', 'var', ]
            ]
        ]
    ];
```

And then drop something like this into e.g. `htdocs/hosts.php`:


```
<?php

include_once("../config.php");
require_once "../phplib/CollectdHost.php";
include_once("../phplib/header.php");

foreach($CONFIG["hosts"] as $host => $data) {

    $fss = empty($data["filesystems"]) ? [] : $data["filesystems"];

    $server = new CollectdHost($host, $data["cpus"], $fss, ($data["apache"] == true));
    $server->set_graphite_host($CONFIG["graphitehost"]);
    echo "<h2> {$host} </h2>";
    $server->render();
}

include_once("../phplib/footer.php");

```

