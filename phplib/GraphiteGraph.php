<?php

class GraphiteGraph {

    private $stacked = false;

    function __construct($graphitehost, $from = null, $title = null,
                         $hide_legend = null, $width = null) {
        $this->from = $from ?: "-4h";
        $this->width = $width ?: "400";
        $this->graphitehost = $graphitehost;
        $this->baseurl = $graphitehost . "/render?width={$this->width}&from={$this->from}&target={{THETARGET}}";
        $this->title = $title;
        $this->set_legend($hide_legend);
    }

    /**
     * set the title of the graph when rendering
     *
     * Parameter
     *  $title - title of the graph
     */
    function set_title($title="") {
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
    function set_legend($val) {
        if (is_null($val)) {
            $this->legend = "";
        } elseif ($val === true) {
            $this->legend = "&hideLegend=true";
        } else {
            $this->legend = "&hideLegend=false";
        }
    }

    /**
     * Render a graphite metric in an <img> HTML tag in place
     *
     * Parameter
     *  $target - Graphite metric to render
     */
    function render($target) {
        print($this->build_graph_img_tag($target));
    }

    /**
     * Build a graphite metric in an <img> HTML tag and return it
     *
     * Parameter
     *  $target - Graphite metric to render
     *
     * Returns the graph img tag as a string
     */
    function build_graph_img_tag($target) {
        $url = str_replace("{{THETARGET}}", $target, $this->baseurl);
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
     *  $target - Graphite metric to get
     *
     * Returns the number
     */
    function get_last_value($target) {
        $url = str_replace("{{THETARGET}}", $target, $this->baseurl);
        $val = explode(",", file_get_contents("{$url}&format=raw"));
        $val = array_filter($val, function ($v) { return trim($v) !== "None"; });
        return end($val);
    }

}
