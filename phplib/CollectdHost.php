<?php

namespace Yagd;

class CollectdHost {

    protected $additionalMetrics;

    function __construct(
        $hostname,
        $cpus = 0,
        $fss = [],
        $apache = false,
        $interfaces = []
    ) {
                             $this->hostname = $hostname;
                             $this->sanName = str_replace('.', '_', $hostname);
                             $this->cpus = $cpus;
                             $this->fss = $fss;
                             $this->apache = $apache;
                             $this->interfaces = $interfaces;
                             $this->additionalMetrics = [];
    }

    /**
     * Getter method for additional metrics
     *
     * Returns additional metrics
     */
    function getAdditionalMetrics()
    {
        return $this->additionalMetrics;
    }

    /**
     * Setter method for additional metrics
     *
     * Parameter
     *  $metrics - metrics to set to
     */
    function setAdditionalMetrics($metrics)
    {
        $this->additionalMetrics = $metrics;
    }

    /**
     * Append additional metrics
     *
     * Parameter
     *  $metric - metric to append
     */
    function appendAdditionalMetric($metric)
    {
        $this->additionalMetrics[] = $metric;
    }

    /**
     * Helper function to fully render a CollectdHost with all properties
     */
    function render()
    {
        $this->renderCPUs();
        $this->renderMemory();
        $this->renderInterfaces();
        if (count($this->fss) > 0) {
            $this->renderFilesystems();
        }
        if ($this->apache) {
            $this->renderApache();
        }
        $this->renderUptime();
        $this->renderAdditionalMetrics();
    }

    /**
     * Set graphite configuration for CollectdHost
     *
     * Parameter
     *  $host - hostname of the graphite host with protocol
     *  $legend - value to use for the Graphite hideLegend
     */
    function setGraphiteConfiguration($host, $legend = null)
    {
        $this->graphiteHost = $host;
        $this->graphiteLegend = $legend;
    }

    /**
     * Helper function to get a graph object with the current objects graphite
     * settings
     *
     * Returns a GraphiteGraph instance
     */
    function getGraph()
    {
        return new GraphiteGraph(
            $this->graphiteHost,
            null,
            null,
            $this->graphiteLegend
        );
    }

    /**
     * Build dashboard HTML for CPU graphs
     *
     * Returns HTML as string
     */
    function buildCPUsHtml()
    {
        $ret = "";
        if ($this->cpus === 0) {
            return $ret;
        }
        $graph = $this->getGraph();
        $graph->stacked(true);
        $ret .= '<h2> CPU Info </h2>';
        $ret .= '<div class="row">';
        for ($i = 0; $i < $this->cpus; $i++) {

            $metric = "collectd." . $this->sanName . ".cpu-$i.cpu-*";
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
    function renderCPUs()
    {
        print $this->buildCPUsHtml();
    }

    /**
     * Build dashboard HTML for memory graphs
     *
     * Returns HTML as string
     */
    function buildMemoryHtml()
    {
        $ret = '';
        $graph = $this->getGraph();
        $graph->stacked(true);
        $metric = "collectd." . $this->sanName . ".memory.memory-*";
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
    function renderMemory()
    {
        print $this->buildMemoryHtml();
    }

    /**
     * Function to build HTML from the additional metrics set on the current
     * object. The expectected format for this is:
     * [ "header title" => ["graph title" => "metric"]]
     *
     * Returns HTML as string
     */
    function buildAdditionalMetricsHtml()
    {
        $ret = "";
        foreach ($this->getAdditionalMetrics() as $name=>$metrics) {
            $ret .= "<h2> {$name} </h2>";
            $ret .= '<div class="row">';
            foreach ($metrics as $title=>$metric) {
                $graph = $this->getGraph();
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
     * buildAdditionalMetricsHtml() and just passes arguments to it and
     * prints the return value. All the logic happens in there.
     *
     */
    function renderAdditionalMetrics()
    {
        print $this->buildAdditionalMetricsHtml();
    }

    /**
     * Build dashboard HTML for uptime graphs
     *
     * Parameters
     *  $asDays - boolean to determine whether to show the graphs or uptime
     *  in days
     *  $rawData - mock uptime raw graphite data for testing
     *
     * Returns HTML as string
     */
    function buildUptimeHtml($asDays = false, $rawData = null)
    {
        $graph = $this->getGraph();
        $metric = "collectd." . $this->sanName . ".uptime.uptime";

        $ret = '';
        if ($asDays === true) {
            $val = $graph->getLastValue($metric, $rawData);
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
    function renderUptime($asDays = false)
    {
        print $this->buildUptimeHtml($asDays);
    }

    /**
     * Build dashboard HTML for filesystem graphs
     *
     * Returns HTML as string
     */
    function buildFilesystemsHtml()
    {
        $ret = '';
        $graph = $this->getGraph();
        $ret .= '<h2> Filesystems </h2>';
        $ret .= '<div class="row">';
        foreach ($this->fss as $fs) {
            $graph->setTitle($fs);
            $graph->stacked(true);
            $metric = "aliasSub(collectd.{$this->sanName}.df-${fs}.*,'collectd.{$this->sanName}.df-${fs}.*df_','')";
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
    function renderFilesystems()
    {
        print $this->buildFilesystemsHtml();
    }

    /**
     * Build dashboard HTML for apache graphs
     *
     * Returns HTML as string
     */
    function buildApacheHtml()
    {
        $ret = '';
        $properties = array(
            'apache_bytes',
            'apache_connections',
            'apache_idle_workers',
            'apache_requests',
        );
        $graph = $this->getGraph();
        $ret .= '<h2> Apache Info </h2>';
        $ret .= '<div class="row">';
        foreach ($properties as $property) {
            $metric = "collectd." . $this->sanName . ".apache-apache80.$property";
            $ret .= '<div class="col-md-4">';
            $ret .= $graph->buildGraphImgTag($metric);
            $ret .= '</div>';
        }
        $ret .= "</div>";
        $ret .= '<div class="row">';
        $graph->stacked(true);
        $metric = "collectd." . $this->sanName . ".apache-apache80.apache_scoreboard-*";
        $ret .= '<div class="col-md-4">';
        $ret .= $graph->buildGraphImgTag($metric);
        $ret .= '</div>';
        $ret .= '</div>';

        return $ret;
    }

    /**
     * Helper method to render apache dashboard code
     */
    function renderApache()
    {
        print $this->buildApacheHtml();
    }

    /**
     * Build dashboard HTML for apache graphs
     *
     * Returns HTML as string
     */
    function buildInterfacesHtml()
    {
        $ret = '';
        $metricTypes = [ "packets", "octets", "errors" ];
        $ret .= '<h2> Network </h2>';
        $ret .= '<div class="row">';
        foreach ($this->interfaces as $int) {
            foreach ($metricTypes as $type) {
                $graph = $this->getGraph();
                $graph->setTitle("{$int} {$type}/s");
                $metric  = "aliasSub(collectd.{$this->sanName}.";
                $metric .= "interface-${int}.if_{$type}.*,";
                $metric .= "'collectd.{$this->sanName}.interface-${int}.if_{$type}.','')";
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
    function renderInterfaces()
    {
        print $this->buildInterfacesHtml();
    }

}
