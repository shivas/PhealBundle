<?php

namespace shivas\PhealBundle\Service;

use \Pheal;
use \PhealConfig;
use shivas\PhealBundle\Exception\PhealFactoryException;

class PhealFactory
{
    private $config=null;
    private $configured = false;
    private $reconfigure = false;

    public function __construct($config)
    {
        $this->reconfigure = $config['reconfigure'];
        unset($config['reconfigure']);
        $this->config = $config;

    }

    /**
     * Factory method to get new \Pheal object
     *
     * @return \Pheal
     */
    public function getInstance()
    {
        if ($this->reconfigure || !$this->configured)
        {
            $this->configurePhealConfig();
            $this->configured = true;
        }
        $reflection = new \ReflectionClass('\Pheal');
        return $reflection->newInstanceArgs(func_get_args());
    }

    /**
     * Configures PhealConfig singleton class with default settings in Symfony configuration
     *
     * @throws \RuntimeException
     */
    private function configurePhealConfig()
    {
        if(class_exists('\PhealConfig'))
        {
            foreach ($this->config as $key => $value)
            {
                if (in_array($key, array('cache','log','archive','access')))
                {
                    \PhealConfig::getInstance()->$key = $this->resolveConfigClass($value);
                }else {
                    \PhealConfig::getInstance()->$key = $value;
                }
            }
        }else {
            throw new \RuntimeException("There is no Pheal library in autoload, make sure you installed all dependencies");
        }
    }

    /**
     * Constructs object by configuration satisfying custom arguments passing to constructor
     *
     * @param $config string
     * @return object
     * @throws \shivas\PhealBundle\Exception\PhealFactoryException
     */
    public function resolveConfigClass($config)
    {
        $className = $config['class'];
        $arguments = isset($config['arguments']) ? $config['arguments'] : null;

        if(is_null($arguments))
        {
            return new $className();
        }else {
            try {
                $reflection = new \ReflectionClass($className);
                return $reflection->newInstanceArgs($arguments);
            }catch (\ReflectionException $re)
            {
                throw new PhealFactoryException($re->getMessage(), $re->getCode(), $re);
            }
        }
    }

}
