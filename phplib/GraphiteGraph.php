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

    function set_title($title="") {
        $this->title = $title;
    }


    function render($target) {
        $url = str_replace("{{THETARGET}}", $target, $this->baseurl);
        if (!empty($this->title)) {
            $url .= "&title={$this->title}";
        }
        print('<img src="' . $url . '"></img>');
    }

}
