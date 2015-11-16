<?php
require_once("GraphiteGraph.php");

class CollectdHost {

    protected $additional_metrics;

    function __construct($hostname, $cpus = 0, $fss = [], $apache = false,
                         $interfaces = []) {
        $this->hostname = $hostname;
        $this->san_name = str_replace('.', '_', $hostname);
        $this->cpus = $cpus;
        $this->fss = $fss;
        $this->apache = $apache;
        $this->interfaces = $interfaces;
        $this->additional_metrics = [];
    }

    function get_additional_metrics() {
        return $this->additional_metrics;
    }

    function set_additional_metrics($metrics) {
        $this->additional_metrics = $metrics;
    }

    function append_additional_metric($metric) {
        $this->additional_metrics[] = $metric;
    }

    function render() {
        $this->render_cpus();
        $this->render_memory();
        $this->render_interfaces();
        if (count($this->fss) > 0) {
            $this->render_filesystems();
        }
        if ($this->apache) {
            $this->render_apache();
        }
        $this->render_uptime();
        $this->render_additional_metrics();
    }

    function set_graphite_configuration($host, $legend = null) {
        $this->graphite_host = $host;
        $this->graphite_legend = $legend;
    }

    function get_graph() {
        return new GraphiteGraph($this->graphite_host, $_GET["from"],
                                 null, $this->graphite_legend);
    }

    function render_cpus() {
        if ($this->cpus === 0) {
            return;
        }
        $graph = $this->get_graph();
        $graph->stacked(true);
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
        $graph = $this->get_graph();
        echo '<h2> Memory Info </h2>';
        echo '<div class="row">';
        $graph->stacked(true);
        $metric = "collectd." . $this->san_name . ".memory.memory-*";
        echo('<div class="col-md-4">');
        $graph->render($metric);
        echo('</div>');
        echo "</div>";
    }

    function render_additional_metrics() {
        foreach($this->additional_metrics as $name=>$metrics) {
            echo "<h2> {$name} </h2>";
                echo '<div class="row">';
            foreach ($metrics as $title=>$metric) {
                $graph = $this->get_graph();
                $graph->set_title($title);
                echo('<div class="col-md-4">');
                $graph->render($metric);
                echo('</div>');
            }
                echo "</div>";
        }
    }

    function render_uptime($as_days=false) {
        $graph = $this->get_graph();
        $metric = "collectd." . $this->san_name . ".uptime.uptime";

        if ($as_days === true) {
            $val = $graph->get_last_value($metric);
            $days = intval($val / 86400);
            print "{$days} days";
        } else {
            echo '<h2> uptime </h2>';
            echo '<div class="row">';
            echo('<div class="col-md-4">');
            $graph->render($metric);
            echo('</div>');
            echo "</div>";
        }
    }

    function render_filesystems() {
        $graph = $this->get_graph();
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
        $graph = $this->get_graph();
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
        $graph->stacked(true);
        $metric = "collectd." . $this->san_name . ".apache-apache80.apache_scoreboard-*";
        echo('<div class="col-md-4">');
        $graph->render($metric);
        echo('</div>');
        echo "</div>";
    }

    function render_interfaces() {
        $metric_types = [ "packets", "octets", "errors" ];
        echo '<h2> Network </h2>';
        echo '<div class="row">';
        foreach ($this->interfaces as $int) {
            foreach ($metric_types as $type) {
                $graph = $this->get_graph();
                $graph->set_title("{$int} {$type}/s");
                $metric = "aliasSub(collectd.{$this->san_name}.interface-${int}.if_{$type}.*,'collectd.{$this->san_name}.interface-${int}.if_{$type}.','')";
                echo('<div class="col-md-4">');
                $graph->render($metric);
                echo('</div>');
            }

        }
        echo "</div>";
    }

}
