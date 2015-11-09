# yagd

[![Build Status](https://travis-ci.org/mrtazz/yagd.svg?branch=master)](https://travis-ci.org/mrtazz/yagd)

## Overview
Yet Another Graphite Dashboard - because why not? It's heavily inspired by
[the Etsy dashboard framework](https://github.com/etsy/dashboard) but only
provides a very small subset of features. If you have a lot of hosts or need
advanced features I'd recommend checking that out.

## Look
![yagd look](http://s3itch.unwiredcouch.com/dashboards-20140524-130506.jpg)

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
            ],
            "additional_metrics" => [
                "disk temp" => [
                "disk temperature" => "collectd.foo_example_com.disktemp-ada*.current",
                ]
            ],
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

    $server = new CollectdHost($host, $data["cpus"], $fss, ($data["apache"] == true),
                               $data["interfaces"]);
    $server->set_graphite_host($CONFIG["graphitehost"]);
    echo "<h2> {$host} </h2>";
    $server->render();
}

include_once("../phplib/footer.php");

```

### Inject a select box into the navbar
For the host page for example you might wanna have an easy way to only show
one host. For that you can inject a select box into the header navbar like
this:

```
<?php

$selectbox = "";
$selectbox .= "<form method='get' action='hosts.php' style='margin-top: 15px'class='pull-right'>";
$selectbox .= "   <select name='hostname' onchange='this.form.submit()'>";
    foreach ($CONFIG["hosts"] as $host => $data) {
        $selected = ($_GET["hostname"] == $host) ? "selected" : "";
        $selectbox .= "<option value='{$host}' {$selected}>{$host}</option>";
}
$selectbox .= "</select>";
$selectbox .= "</form>";

include_once("../phplib/header.php");

if (empty($_GET["hostname"])) {
    $hosts = $CONFIG["hosts"];
} else {
    $hosts = [ $_GET["hostname"] => $CONFIG["hosts"][$_GET["hostname"]] ];
}
```

This will show the content of `$selectbox` in the header and only show the
actually selected host (if one was selected) on the page.
