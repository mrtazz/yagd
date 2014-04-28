<?php

class GraphiteGraph {

    function __construct($graphitehost, $from = null) {
        if (is_null($from)) {
            $from = "-4h";
        }
        $this->graphitehost = $graphitehost;
        $this->baseurl = $graphitehost . "/render?width=400&from=$from&target={{THETARGET}}";
    }


    function render($target) {
        $url = str_replace("{{THETARGET}}", $target, $this->baseurl);
        print('<img src="' . $url . '"></img>');
    }

}
