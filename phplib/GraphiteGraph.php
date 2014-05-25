<?php

class GraphiteGraph {

    function __construct($graphitehost, $from = null, $title = null) {
        if (is_null($from)) {
            $from = "-4h";
        }
        $this->graphitehost = $graphitehost;
        $this->baseurl = $graphitehost . "/render?width=400&from=$from&target={{THETARGET}}";
        $this->title = $title;
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
     * Render a graphite metric in an <img> HTML tag in place
     *
     * Parameter
     *  $target - Graphite metric to render
     */
    function render($target) {
        $url = str_replace("{{THETARGET}}", $target, $this->baseurl);
        if (!empty($this->title)) {
            $url .= "&title={$this->title}";
        }
        if ($this->stacked === true) {
            $url .= "&areaMode=stacked";
        }
        print('<img src="' . $url . '"></img>');
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
