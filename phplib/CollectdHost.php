<?php

namespace Yagd;

class CollectdHost extends Host {

    protected $additionalMetrics;
    protected $apache = false;

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
     * Build dashboard HTML for uptime graphs
     *
     * Returns HTML as string
     */
    function buildUptimeHtml()
    {
        $graph = $this->getGraph();
        $metric = "collectd." . $this->sanName . ".uptime.uptime";

        $ret = '';
        $ret .= '<h2> uptime </h2>';
        $ret .= '<div class="row">';
        $ret .= '<div class="col-md-4">';
        $ret .= $graph->buildGraphImgTag($metric);
        $ret .= '</div>';
        $ret .= '</div>';

        return $ret;
    }

    /**
     * Build dashboard HTML for uptime graphs as days
     *
     * Parameters
     *  $rawData - mock uptime raw graphite data for testing
     *
     * Returns HTML as string
     */
    function buildUptimeAsDaysHtml($rawData = null)
    {
        $graph = $this->getGraph();
        $metric = "collectd." . $this->sanName . ".uptime.uptime";

        $val = $graph->getLastValue($metric, $rawData);
        $days = intval($val / 86400);
        $ret = "{$days} days";
        return $ret;
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

}
