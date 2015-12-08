<?php

use Yagd\Host;

class TestHost extends Host
{
}

class HostTest extends PHPUnit_Framework_TestCase
{
    public function setUp() {
        $this->host = new TestHost("foo", 2, ['/var'], true, ['re0']);
        $this->host->setGraphiteConfiguration("http://grph.xmpl.com");
    }

    public function testInstantiation()
    {
        $host = new TestHost("foo");
        $this->assertInstanceOf('Yagd\Host', $host);
    }

    public function testWrongMethodCalledViaMagic()
    {
        $this->setExpectedException(
            'Exception', "Method called via '_call' (foo) is not a render method"
        );
        $this->host->foo();
    }

    public function testMissingBuilderMethod()
    {
        $this->setExpectedException(
            'Exception', "HTML builder method 'buildFooHtml' not implemented."
        );
        $this->host->renderFoo();
    }

}
