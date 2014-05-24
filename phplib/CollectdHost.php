<?php
require_once("GraphiteGraph.php");

class CollectdHost {

    function __construct($hostname, $cpus = 0, $fss = [], $apache = false) {
        $this->hostname = $hostname;
        $this->san_name = str_replace('.', '_', $hostname);
        $this->cpus = $cpus;
        $this->fss = $fss;
        $this->apache = $apache;
    }

    function render() {
        $this->render_cpus();
        $this->render_memory();
        if (count($this->fss) > 0) {
            $this->render_filesystems();
        }
        if ($this->apache) {
            $this->render_apache();
        }
        $this->render_uptime();
    }

    function set_graphite_host($host) {
        $this->graphite_host = $host;
    }

    function render_cpus() {
        $graph = new GraphiteGraph($this->graphite_host, $_GET["from"]);
        echo '<h2> CPU Info </h2>';
        echo '<div class="row">';
        for ($i = 0; $i < $this->cpus; $i++) {

            $metric = "collectd." . $this->san_name . ".cpu-$i.cpu-*";
            echo('<div class="col-md-4">');
            $graph->render($metric);
            echo('</div>');

        }
        echo "</div>";
    }

    function render_memory() {
        $graph = new GraphiteGraph($this->graphite_host, $_GET["from"]);
        echo '<h2> Memory Info </h2>';
        echo '<div class="row">';
        $metric = "stacked(collectd." . $this->san_name . ".memory.memory-*)";
        echo('<div class="col-md-4">');
        $graph->render($metric);
        echo('</div>');
        echo "</div>";
    }

    function render_uptime() {
        $graph = new GraphiteGraph($this->graphite_host, $_GET["from"]);
        echo '<h2> uptime </h2>';
        echo '<div class="row">';
        $metric = "collectd." . $this->san_name . ".uptime.uptime";
        echo('<div class="col-md-4">');
        $graph->render($metric);
        echo('</div>');
        echo "</div>";
    }

    function render_filesystems() {
        $graph = new GraphiteGraph($this->graphite_host, $_GET["from"]);
        echo '<h2> Filesystems </h2>';
        echo '<div class="row">';
        foreach ($this->fss as $fs) {
            $graph->set_title($fs);
            $graph->stacked(true);
            $metric = "aliasSub(collectd.{$this->san_name}.df-${fs}.*,'collectd.{$this->san_name}.df-${fs}.*df_','')";
            echo('<div class="col-md-4">');
            $graph->render($metric);
            echo('</div>');

        }
        echo "</div>";
    }

    function render_apache() {
        $properties = array(
            'apache_bytes',
            'apache_connections',
            'apache_idle_workers',
            'apache_requests',
        );
        $graph = new GraphiteGraph($this->graphite_host, $_GET["from"]);
        echo '<h2> Apache Info </h2>';
        echo '<div class="row">';
        foreach ($properties as $property) {
            $metric = "collectd." . $this->san_name . ".apache-apache80.$property";
            echo('<div class="col-md-4">');
            $graph->render($metric);
            echo('</div>');
        }
        echo "</div>";
        echo '<div class="row">';
        $metric = "stacked(collectd." . $this->san_name . ".apache-apache80.apache_scoreboard-*)";
        echo('<div class="col-md-4">');
        $graph->render($metric);
        echo('</div>');
        echo "</div>";
    }

}
