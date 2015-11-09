<?php
require_once("phplib/GraphiteGraph.php");

class GraphiteGraphTest extends PHPUnit_Framework_TestCase
{
    public function testInstantiation()
    {
        $graph = new GraphiteGraph("https://graphite.example.com");
        $this->assertInstanceOf('GraphiteGraph', $graph);
    }
}
