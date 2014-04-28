<?php

class GraphiteGraph {

    function __construct($graphitehost) {
        $this->graphitehost = $graphitehost;
        $this->baseurl = $graphitehost . "/render?target={{THETARGET}}";
    }


    function render($target) {
        $url = str_replace("{{THETARGET}}", $target, $this->baseurl);
        print('<img src="' . $url . '"></img>');
    }

}
