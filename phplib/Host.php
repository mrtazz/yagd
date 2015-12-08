<?php

namespace Yagd;

abstract class Host
{

    protected $additionalMetrics;

    public function __construct(
        $hostname,
        $cpus = 0,
        $fss = [],
        $interfaces = []
    ) {
        $this->hostname = $hostname;
        $this->sanName = str_replace('.', '_', $hostname);
        $this->cpus = $cpus;
        $this->fss = $fss;
        $this->interfaces = $interfaces;
        $this->additionalMetrics = [];
    }

    /**
     * Getter method for additional metrics
     *
     * Returns additional metrics
     */
    public function getAdditionalMetrics()
    {
        return $this->additionalMetrics;
    }

    /**
     * Setter method for additional metrics
     *
     * Parameter
     *  $metrics - metrics to set to
     */
    public function setAdditionalMetrics($metrics)
    {
        $this->additionalMetrics = $metrics;
    }

    /**
     * Append additional metrics
     *
     * Parameter
     *  $metric - metric to append
     */
    public function appendAdditionalMetric($metric)
    {
        $this->additionalMetrics[] = $metric;
    }

    /**
     * Magic method to allow generation of render* methods that are just
     * calling the corresponding build*Html() methods. This takes the tedious
     * work away of having to write a render method for everything that just
     * prints whatever the builder method returns.
     */
    public function __call($name, $arguments)
    {
        if (substr($name, 0, 6) !== "render") {
            $msg = "Method called via '_call' ({$name}) is not a render method";
            throw new \Exception($msg);
        }

        $builderMethod = "build" . substr($name, 6) . "Html";

        if (!method_exists($this, $builderMethod)) {
            $msg = "HTML builder method '{$builderMethod}' not implemented.";
            throw new \Exception($msg);
        }
        print $this->$builderMethod($arguments);
    }

    /**
     * Helper function to fully render a CollectdHost with all properties
     */
    public function render()
    {
        $this->renderCPUs();
        $this->renderMemory();
        $this->renderInterfaces();
        if (count($this->fss) > 0) {
            $this->renderFilesystems();
        }
        if ($this->apache) {
            $this->renderApache();
        }
        $this->renderUptime();
        $this->renderAdditionalMetrics();
    }

    /**
     * Set graphite configuration for CollectdHost
     *
     * Parameter
     *  $host - hostname of the graphite host with protocol
     *  $legend - value to use for the Graphite hideLegend
     */
    public function setGraphiteConfiguration($host, $legend = null)
    {
        $this->graphiteHost = $host;
        $this->graphiteLegend = $legend;
    }

    /**
     * Helper function to get a graph object with the current objects graphite
     * settings
     *
     * Returns a GraphiteGraph instance
     */
    public function getGraph()
    {
        return new GraphiteGraph(
            $this->graphiteHost,
            null,
            null,
            $this->graphiteLegend
        );
    }
}
