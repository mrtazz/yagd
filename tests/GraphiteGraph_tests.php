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

    public function testStackedGraphImgTag()
    {
        $graph = new GraphiteGraph("https://graphite.example.com");
        $graph->stacked(true);
        $tag = $graph->build_graph_img_tag("foo");
        $expct =  '<img src="https://graphite.example.com/render?';
        $expct .= 'width=400&from=-4h&target=foo&areaMode=stacked"></img>';
        $this->assertEquals($expct, $tag);
    }

    public function testGraphImgTagWithTitle()
    {
        $graph = new GraphiteGraph("https://graphite.example.com");
        $graph->set_title("foo");
        $tag = $graph->build_graph_img_tag("foo");
        $expct =  '<img src="https://graphite.example.com/render?';
        $expct .= 'width=400&from=-4h&target=foo&title=foo"></img>';
        $this->assertEquals($expct, $tag);
    }

    public function testGraphImgTagWithLegend()
    {
        $graph = new GraphiteGraph("https://graphite.example.com");
        $graph->set_legend(false);
        $tag = $graph->build_graph_img_tag("foo");
        $expct =  '<img src="https://graphite.example.com/render?';
        $expct .= 'width=400&from=-4h&target=foo&hideLegend=false"></img>';
        $this->assertEquals($expct, $tag);
    }

    public function testGraphRender()
    {
        $graph = new GraphiteGraph("https://graphite.example.com");
        ob_start();
        $graph->render("foo");
        ob_end_clean();
    }

    public function testGetFullGraphiteURL() {
        $graph = new GraphiteGraph("https://graphite.example.com");
        $url = $graph->get_graphite_url_for_metric("foo");
        $expct =  'https://graphite.example.com/render?';
        $expct .= 'width=400&from=-4h&target=foo';
        $this->assertEquals($expct, $url);
    }

    public function testGetLastValue() {
        $graph = new GraphiteGraph("https://graphite.example.com");
        $val = $graph->get_last_value("foo", "1,2,3,4,5");
        $this->assertEquals(5, $val);
    }

    public function testGetLastValueIfNone() {
        $graph = new GraphiteGraph("https://graphite.example.com");
        $val = $graph->get_last_value("foo", "1,2,3,4,5,None");
        $this->assertEquals(5, $val);
    }

}
