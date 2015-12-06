<?php

namespace Yagd;

class GraphiteGraph {

    private $stacked = false;

    function __construct($graphitehost, $from = null, $title = null,
                         $hideLegend = null, $width = null) {
        $this->from = $from ?: "-4h";
        $this->width = $width ?: "400";
        $this->graphitehost = $graphitehost;
        $this->baseurl = $graphitehost . "/render?width={$this->width}&from={$this->from}&target={{THETARGET}}";
        $this->title = $title;
        $this->setLegend($hideLegend);
    }

    /**
     * set the title of the graph when rendering
     *
     * Parameter
     *  $title - title of the graph
     */
    function setTitle($title="") {
        $this->title = $title;
    }

    /**
     * set whether or not to render graphs stacked
     *
     * Parameter
     *  $val - true or false
     */
    function stacked($val) {
        $this->stacked = $val;
    }

    /**
     * set whether or not to show legend. This sets the value of Graphite's
     * hideLegend param to the specified value if a boolean is given. If null
     * is give, it removes the param, making it fallback to default setting of
     * only showing the legend for 10 metrics or less.
     *
     * Parameter
     *  $val - true, false or null
     */
    function setLegend($val) {
        $this->legend = "&hideLegend=false";
        if (is_null($val)) {
            $this->legend = "";
        } elseif ($val === true) {
            $this->legend = "&hideLegend=true";
        }
    }

    /**
     * Render a graphite metric in an <img> HTML tag in place
     *
     * Parameter
     *  $target - Graphite metric to render
     */
    function render($target) {
        print($this->buildGraphImgTag($target));
    }

    /**
     * Get the full Graphite URL for a metric
     *
     * Parameter
     *  $metric - the metric to fill in for the URL
     *
     * Returns the full URL as a string
     */
    function getGraphiteUrlForMetric($metric) {
        return str_replace("{{THETARGET}}", $metric, $this->baseurl);
    }

    /**
     * Build a graphite metric in an <img> HTML tag and return it
     *
     * Parameter
     *  $target - Graphite metric to render
     *
     * Returns the graph img tag as a string
     */
    function buildGraphImgTag($target) {
        $url = $this->getGraphiteUrlForMetric($target);
        if (!empty($this->title)) {
            $url .= "&title={$this->title}";
        }
        if ($this->stacked === true) {
            $url .= "&areaMode=stacked";
        }
        $url .= $this->legend;
        return '<img src="' . $url . '"></img>';
    }

    /**
     * get only the latest value of a timeseries
     *
     * Parameter
     *  $target   - Graphite metric to get
     *  $rawData - raw data to mock out for testing
     *
     * Returns the last value of the timeseries as a number
     */
    function getLastValue($target, $rawData=null) {
        $url = $this->getGraphiteUrlForMetric($target);
        if (is_null($rawData)) { $rawData = file_get_contents("{$url}&format=raw"); }
        $val = explode(",", $rawData );
        $val = array_filter($val, function ($item) { return trim($item) !== "None"; });
        return end($val);
    }

}
