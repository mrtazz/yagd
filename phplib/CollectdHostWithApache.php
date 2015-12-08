<?php

namespace Yagd;

class CollectdHostWithApache extends CollectdHost {

    protected $apache = true;

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

}
