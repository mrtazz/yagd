<?php
require_once("phplib/GraphiteGraph.php");

use Yagd\GraphiteGraph;

class GraphiteGraphTest extends PHPUnit_Framework_TestCase
{
    public function testInstantiation()
    {
        $graph = new GraphiteGraph("https://graphite.example.com");
        $this->assertInstanceOf('Yagd\GraphiteGraph', $graph);
    }

    public function testDefaultGraphImgTag()
    {
        $graph = new GraphiteGraph("https://graphite.example.com");
        $tag = $graph->build_graph_img_tag("foo");
        $expct =  '<img src="https://graphite.example.com/render?';
        $expct .= 'width=400&from=-4h&target=foo"></img>';
        $this->assertEquals($expct, $tag);
    }
}
