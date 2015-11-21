<?php
require_once("phplib/CollectdHost.php");

use Yagd\CollectdHost;

class CollectdHostTest extends PHPUnit_Framework_TestCase
{
    public function setUp() {
        $_GET["from"] = "-4hours";
        $this->host = new CollectdHost("foo", 2, ['/var'], true, ['re0']);
        $this->host->set_graphite_configuration("http://grph.xmpl.com");
    }

    public function testInstantiation()
    {
        $host = new CollectdHost("foo");
        $this->assertInstanceOf('Yagd\CollectdHost', $host);
    }

    public function testAdditionalMetricsGetSet() {
        $this->host->set_additional_metrics(['foo' => 'bar']);
        $metrics = $this->host->get_additional_metrics();
        $this->assertEquals(['foo' => 'bar'], $metrics);
        $this->host->append_additional_metric(['bla' => 'blubb']);
        $metrics = $this->host->get_additional_metrics();
        $this->assertEquals(['foo' => 'bar', ['bla' => 'blubb']], $metrics);
    }

    public function testAdditionalMetricsHTML() {
        $this->host->set_additional_metrics(['foo' => ['bar' => 'blubb']]);
        $ret = $this->host->build_additional_metrics_html();
        $expct = '<h2> foo </h2><div class="row"><div class="col-md-4"><img src="http://grph.xmpl.com/render?width=400&from=-4hours&target=blubb&title=bar"></img></div></div>';
        $this->assertEquals($expct, $ret);
    }

    public function testCPUsHTML() {
        $ret = $this->host->build_cpus_html();
        $expct = '<h2> CPU Info </h2><div class="row"><div class="col-md-4"><img src="http://grph.xmpl.com/render?width=400&from=-4hours&target=collectd.foo.cpu-0.cpu-*&areaMode=stacked"></img></div><div class="col-md-4"><img src="http://grph.xmpl.com/render?width=400&from=-4hours&target=collectd.foo.cpu-1.cpu-*&areaMode=stacked"></img></div></div>';
        $this->assertEquals($expct, $ret);
        $this->host = new CollectdHost('foo', 0);
        $this->host->set_graphite_configuration("http://grph.xmpl.com");
        $ret = $this->host->build_cpus_html();
        $expct = '';
        $this->assertEquals($expct, $ret);
    }

    public function testMemoryHTML() {
        $ret = $this->host->build_memory_html();
        $expct = '<h2> Memory Info </h2><div class="row"><div class="col-md-4"><img src="http://grph.xmpl.com/render?width=400&from=-4hours&target=collectd.foo.memory.memory-*&areaMode=stacked"></img></div></div>';
        $this->assertEquals($expct, $ret);
    }

    public function testFilesystemHTML() {
        $ret = $this->host->build_filesystems_html();
        $expct = '<h2> Filesystems </h2><div class="row"><div class="col-md-4"><img src="http://grph.xmpl.com/render?width=400&from=-4hours&target=aliasSub(collectd.foo.df-/var.*,\'collectd.foo.df-/var.*df_\',\'\')&title=/var&areaMode=stacked"></img></div></div>';
        $this->assertEquals($expct, $ret);
    }

    public function testApacheHTML() {
        $ret = $this->host->build_apache_html();
        $expct = '<h2> Apache Info </h2><div class="row"><div class="col-md-4"><img src="http://grph.xmpl.com/render?width=400&from=-4hours&target=collectd.foo.apache-apache80.apache_bytes"></img></div><div class="col-md-4"><img src="http://grph.xmpl.com/render?width=400&from=-4hours&target=collectd.foo.apache-apache80.apache_connections"></img></div><div class="col-md-4"><img src="http://grph.xmpl.com/render?width=400&from=-4hours&target=collectd.foo.apache-apache80.apache_idle_workers"></img></div><div class="col-md-4"><img src="http://grph.xmpl.com/render?width=400&from=-4hours&target=collectd.foo.apache-apache80.apache_requests"></img></div></div><div class="row"><div class="col-md-4"><img src="http://grph.xmpl.com/render?width=400&from=-4hours&target=collectd.foo.apache-apache80.apache_scoreboard-*&areaMode=stacked"></img></div></div>';
        $this->assertEquals($expct, $ret);
    }

    public function testInterfacesHTML() {
        $ret = $this->host->build_interfaces_html();
        $expct = '<h2> Network </h2><div class="row"><div class="col-md-4"><img src="http://grph.xmpl.com/render?width=400&from=-4hours&target=aliasSub(collectd.foo.interface-re0.if_packets.*,\'collectd.foo.interface-re0.if_packets.\',\'\')&title=re0 packets/s"></img></div><div class="col-md-4"><img src="http://grph.xmpl.com/render?width=400&from=-4hours&target=aliasSub(collectd.foo.interface-re0.if_octets.*,\'collectd.foo.interface-re0.if_octets.\',\'\')&title=re0 octets/s"></img></div><div class="col-md-4"><img src="http://grph.xmpl.com/render?width=400&from=-4hours&target=aliasSub(collectd.foo.interface-re0.if_errors.*,\'collectd.foo.interface-re0.if_errors.\',\'\')&title=re0 errors/s"></img></div></div>';
        $this->assertEquals($expct, $ret);
    }

    public function testUptimeHTML() {
        $ret = $this->host->build_uptime_html();
        $expct = '<h2> uptime </h2><div class="row"><div class="col-md-4"><img src="http://grph.xmpl.com/render?width=400&from=-4hours&target=collectd.foo.uptime.uptime"></img></div></div>';
        $this->assertEquals($expct, $ret);
        $ret = $this->host->build_uptime_html(true, 86400);
        $expct = '1 days';
        $this->assertEquals($expct, $ret);
    }

    public function testGetGraph() {
        $graph = $this->host->get_graph();
        $this->assertInstanceOf('Yagd\GraphiteGraph', $graph);
    }

    public function testRender() {
        // we only execute the render method here to make sure it runs. The
        // output is already tested via all the specific builder methods
        ob_start();
        $this->host->render();
        ob_end_clean();
    }

}
