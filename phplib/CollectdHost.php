<?php

namespace Yagd;

class CollectdHost {

    protected $additional_metrics;

    function __construct(
        $hostname,
        $cpus = 0,
        $fss = [],
        $apache = false,
        $interfaces = []
    ) {
                             $this->hostname = $hostname;
                             $this->san_name = str_replace('.', '_', $hostname);
                             $this->cpus = $cpus;
                             $this->fss = $fss;
                             $this->apache = $apache;
                             $this->interfaces = $interfaces;
                             $this->additional_metrics = [];
    }

    /**
     * Getter method for additional metrics
     *
     * Returns additional metrics
     */
    function get_additional_metrics()
    {
        return $this->additional_metrics;
    }

    /**
     * Setter method for additional metrics
     *
     * Parameter
     *  $metrics - metrics to set to
     */
    function set_additional_metrics($metrics)
    {
        $this->additional_metrics = $metrics;
    }

    /**
     * Append additional metrics
     *
     * Parameter
     *  $metric - metric to append
     */
    function append_additional_metric($metric)
    {
        $this->additional_metrics[] = $metric;
    }

    /**
     * Helper function to fully render a CollectdHost with all properties
     */
    function render()
    {
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

    /**
     * Set graphite configuration for CollectdHost
     *
     * Parameter
     *  $host - hostname of the graphite host with protocol
     *  $legend - value to use for the Graphite hideLegend
     */
    function set_graphite_configuration($host, $legend = null)
    {
        $this->graphite_host = $host;
        $this->graphite_legend = $legend;
    }

    /**
     * Helper function to get a graph object with the current objects graphite
     * settings
     *
     * Returns a GraphiteGraph instance
     */
    function get_graph()
    {
        return new GraphiteGraph(
            $this->graphite_host,
            $_GET["from"],
            null,
            $this->graphite_legend
        );
    }

    /**
     * Build dashboard HTML for CPU graphs
     *
     * Returns HTML as string
     */
    function build_cpus_html()
    {
        $ret = "";
        if ($this->cpus === 0) {
            return $ret;
        }
        $graph = $this->get_graph();
        $graph->stacked(true);
        $ret .= '<h2> CPU Info </h2>';
        $ret .= '<div class="row">';
        for ($i = 0; $i < $this->cpus; $i++) {

            $metric = "collectd." . $this->san_name . ".cpu-$i.cpu-*";
            $ret .= '<div class="col-md-4">';
            $ret .= $graph->buildGraphImgTag($metric);
            $ret .= '</div>';

        }
        $ret .= "</div>";

        return $ret;
    }

    /**
     * Helper method to render CPU dashboard code
     */
    function render_cpus()
    {
        print $this->build_cpus_html();
    }

    /**
     * Build dashboard HTML for memory graphs
     *
     * Returns HTML as string
     */
    function build_memory_html()
    {
        $ret = '';
        $graph = $this->get_graph();
        $graph->stacked(true);
        $metric = "collectd." . $this->san_name . ".memory.memory-*";
        $ret .= '<h2> Memory Info </h2>';
        $ret .= '<div class="row">';
        $ret .= '<div class="col-md-4">';
        $ret .= $graph->buildGraphImgTag($metric);
        $ret .= '</div>';
        $ret .= '</div>';

        return $ret;
    }

    /**
     * Helper method to render Memory dashboard code
     */
    function render_memory()
    {
        print $this->build_memory_html();
    }

    /**
     * Function to build HTML from the additional metrics set on the current
     * object. The expectected format for this is:
     * [ "header title" => ["graph title" => "metric"]]
     *
     * Returns HTML as string
     */
    function build_additional_metrics_html()
    {
        $ret = "";
        foreach ($this->additional_metrics as $name=>$metrics) {
            $ret .= "<h2> {$name} </h2>";
            $ret .= '<div class="row">';
            foreach ($metrics as $title=>$metric) {
                $graph = $this->get_graph();
                $graph->setTitle($title);
                $ret .= '<div class="col-md-4">';
                $ret .= $graph->buildGraphImgTag($metric);
                $ret .= '</div>';
            }
                $ret .= "</div>";
        }

        return $ret;
    }
    /**
     * Render additional metrics. This builds upon
     * build_additional_metrics_html() and just passes arguments to it and
     * prints the return value. All the logic happens in there.
     *
     */
    function render_additional_metrics()
    {
        print $this->build_additional_metrics_html();
    }

    /**
     * Build dashboard HTML for uptime graphs
     *
     * Parameters
     *  $as_days - boolean to determine whether to show the graphs or uptime
     *  in days
     *  $raw_data - mock uptime raw graphite data for testing
     *
     * Returns HTML as string
     */
    function build_uptime_html($as_days = false, $raw_data = null)
    {
        $graph = $this->get_graph();
        $metric = "collectd." . $this->san_name . ".uptime.uptime";

        $ret = '';
        if ($as_days === true) {
            $val = $graph->getLastValue($metric, $raw_data);
            $days = intval($val / 86400);
            $ret = "{$days} days";
        } else {
            $ret .= '<h2> uptime </h2>';
            $ret .= '<div class="row">';
            $ret .= '<div class="col-md-4">';
            $ret .= $graph->buildGraphImgTag($metric);
            $ret .= '</div>';
            $ret .= '</div>';
        }

        return $ret;
    }


    /**
     * Helper method to render uptime dashboard code
     */
    function render_uptime($as_days = false)
    {
        print $this->build_uptime_html($as_days);
    }

    /**
     * Build dashboard HTML for filesystem graphs
     *
     * Returns HTML as string
     */
    function build_filesystems_html()
    {
        $ret = '';
        $graph = $this->get_graph();
        $ret .= '<h2> Filesystems </h2>';
        $ret .= '<div class="row">';
        foreach ($this->fss as $fs) {
            $graph->setTitle($fs);
            $graph->stacked(true);
            $metric = "aliasSub(collectd.{$this->san_name}.df-${fs}.*,'collectd.{$this->san_name}.df-${fs}.*df_','')";
            $ret .= '<div class="col-md-4">';
            $ret .= $graph->buildGraphImgTag($metric);
            $ret .= '</div>';

        }
        $ret .= "</div>";

        return $ret;
    }

    /**
     * Helper method to render filesystem dashboard code
     */
    function render_filesystems()
    {
        print $this->build_filesystems_html();
    }

    /**
     * Build dashboard HTML for apache graphs
     *
     * Returns HTML as string
     */
    function build_apache_html()
    {
        $ret = '';
        $properties = array(
            'apache_bytes',
            'apache_connections',
            'apache_idle_workers',
            'apache_requests',
        );
        $graph = $this->get_graph();
        $ret .= '<h2> Apache Info </h2>';
        $ret .= '<div class="row">';
        foreach ($properties as $property) {
            $metric = "collectd." . $this->san_name . ".apache-apache80.$property";
            $ret .= '<div class="col-md-4">';
            $ret .= $graph->buildGraphImgTag($metric);
            $ret .= '</div>';
        }
        $ret .= "</div>";
        $ret .= '<div class="row">';
        $graph->stacked(true);
        $metric = "collectd." . $this->san_name . ".apache-apache80.apache_scoreboard-*";
        $ret .= '<div class="col-md-4">';
        $ret .= $graph->buildGraphImgTag($metric);
        $ret .= '</div>';
        $ret .= '</div>';

        return $ret;
    }

    /**
     * Helper method to render apache dashboard code
     */
    function render_apache()
    {
        print $this->build_apache_html();
    }

    /**
     * Build dashboard HTML for apache graphs
     *
     * Returns HTML as string
     */
    function build_interfaces_html()
    {
        $ret = '';
        $metric_types = [ "packets", "octets", "errors" ];
        $ret .= '<h2> Network </h2>';
        $ret .= '<div class="row">';
        foreach ($this->interfaces as $int) {
            foreach ($metric_types as $type) {
                $graph = $this->get_graph();
                $graph->setTitle("{$int} {$type}/s");
                $metric  = "aliasSub(collectd.{$this->san_name}.";
                $metric .= "interface-${int}.if_{$type}.*,";
                $metric .= "'collectd.{$this->san_name}.interface-${int}.if_{$type}.','')";
                $ret .= '<div class="col-md-4">';
                $ret .= $graph->buildGraphImgTag($metric);
                $ret .= '</div>';
            }

        }
        $ret .= "</div>";

        return $ret;
    }

    /**
     * Helper method to render interfaces dashboard code
     */
    function render_interfaces()
    {
        print $this->build_interfaces_html();
    }

}
