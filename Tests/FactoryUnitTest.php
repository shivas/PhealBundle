<?php

namespace shivas\PhealBundle\Tests;

use shivas\PhealBundle\Service\PhealFactory;
use shivas\PhealBundle\Exception\PhealFactoryException;

class FactoryUnitTest extends \PHPUnit_Framework_TestCase
{
    private $minimum_config;
    private $simple_config;

    public function setUp()
    {
        $this->minimum_config = array('reconfigure' => false);
        $this->simple_config = array(
            'api_base' => 'http://api.eveonline.com',
            'api_customkeys' => false,
            'additional_request_parameters' => array('headername' => 'value'),
            'http_method' => 'file',
            'http_interface_ip' => '0.0.0.0',
            'http_user_agent' => 'user_agent',
            'http_post' => true,
            'http_timeout' => 20,
            'http_ssl_verifypeer' => false,
            'http_keepalive' => true
        );
    }

    public function testPhealObjectConstructed()
    {
        $factory = new PhealFactory($this->minimum_config);
        $pheal = $factory->getInstance();
        $this->assertTrue($pheal instanceof \Pheal);
    }

    public function testCacheExtensionPoint()
    {
        $cache_config = array('cache' => array('class' => '\PhealFileCache'));
        $config = array_merge($this->minimum_config, $cache_config);
        $factory = new PhealFactory($config);
        $pheal = $factory->getInstance();
        $this->assertTrue($pheal instanceof \Pheal);
        $this->assertTrue(\PhealConfig::getInstance()->cache instanceof \PhealFileCache);
    }

    public function testLogExtensionPoint()
    {
        $log_config = array('log' => array('class' => '\PhealFileLog'));
        $config = array_merge($this->minimum_config, $log_config);
        $factory = new PhealFactory($config);
        $pheal = $factory->getInstance();
        $this->assertTrue($pheal instanceof \Pheal);
        $this->assertTrue(\PhealConfig::getInstance()->log instanceof \PhealFileLog);
    }

    public function testArchiveExtensionPoint()
    {
        $archive_config = array('archive' => array('class' => '\PhealFileArchive'));
        $config = array_merge($this->minimum_config, $archive_config);
        $factory = new PhealFactory($config);
        $pheal = $factory->getInstance();
        $this->assertTrue($pheal instanceof \Pheal);
        $this->assertTrue(\PhealConfig::getInstance()->archive instanceof \PhealFileArchive);
    }

    public function testAccessExtensionPoint()
    {
        $access_config = array('access' => array('class' => '\PhealCheckAccess'));
        $config = array_merge($this->minimum_config, $access_config);
        $factory = new PhealFactory($config);
        $pheal = $factory->getInstance();
        $this->assertTrue($pheal instanceof \Pheal);
        $this->assertTrue(\PhealConfig::getInstance()->access instanceof \PhealCheckAccess);
        $this->assertInstanceOf('\PhealCheckAccess', \PhealConfig::getInstance()->access);
    }

    public function testSettingAllOppositeSimpleSettingsToDefault()
    {
        $config = array_merge($this->minimum_config, $this->simple_config);
        $factory = new PhealFactory($config);
        $factory->getInstance();

        $this->assertEquals('http://api.eveonline.com', \PhealConfig::getInstance()->api_base);
        $this->assertFalse(\PhealConfig::getInstance()->api_customkeys);
        $this->assertArrayHasKey('headername', \PhealConfig::getInstance()->additional_request_parameters);
        $this->assertEquals('file', \PhealConfig::getInstance()->http_method);
        $this->assertEquals('0.0.0.0', \PhealConfig::getInstance()->http_interface_ip);
        $this->assertEquals('user_agent', \PhealConfig::getInstance()->http_user_agent);
        $this->assertTrue(\PhealConfig::getInstance()->http_post);
        $this->assertEquals(20, \PhealConfig::getInstance()->http_timeout);
        $this->assertFalse(\PhealConfig::getInstance()->http_ssl_verifypeer);
        $this->assertTrue(\PhealConfig::getInstance()->http_keepalive);
    }

    public function testReconfigurationEnabledAndDisabled()
    {
        $config = $this->simple_config;
        $config['reconfigure'] = true;

        $factory = new PhealFactory($config);
        $pheal = $factory->getInstance();

        // make sure atleast timeout is now set from config
        $this->assertEquals(20, \PhealConfig::getInstance()->http_timeout);
        // lets change it imitating changed value inside logic
        \PhealConfig::getInstance()->http_timeout = 10;

        // after new object is created, default timeout should be 20 again
        $pheal = $factory->getInstance();
        $this->assertEquals(20, \PhealConfig::getInstance()->http_timeout);

        // now test the same, but with reconfiguration set to false

        $config['reconfigure'] = false;
        $factory2 = new PhealFactory($config);
        $pheal = $factory2->getInstance();

        $this->assertEquals(20, \PhealConfig::getInstance()->http_timeout);
        \PhealConfig::getInstance()->http_timeout = 10;

        $pheal = $factory2->getInstance();

        // as reconfiguration is now disabled, value should be 10 as was set after last construction
        $this->assertEquals(10, \PhealConfig::getInstance()->http_timeout);
    }
}