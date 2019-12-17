<?php

namespace App;

use Exception;

abstract class AbstractEntity
{

    /**
     * Holds class properties
     *
     * @var array
     */
    protected $data;


    public function __construct($data = [])
    {
        $this->setData($data);
    }

    public function getData()
    {
        $out = [];
        foreach ($this->data as $k=>$v) {
            $getter = $this->getGetter($k);
            if (method_exists($this, $getter)) {
                $out[$k] = $this->$getter($v);
            }
        }

        return $out;
    }
    public function setData($data=[])
    {
        foreach ($data as $k=>$v) {
            $setter = $this->getSetter($k);
            if (method_exists($this, $setter)) {
                $this->$setter($v);
            }
        }
    }
    public function getGetter($str)
    {
        $str = strtolower($str);
        $str = str_replace('_', ' ', $str);
        $str = ucwords($str);
        $str = str_replace(' ', '', $str);

        return "get$str";
    }
    public function getSetter($str)
    {
        $str = strtolower($str);
        $str = str_replace('_', ' ', $str);
        $str = ucwords($str);
        $str = str_replace(' ', '', $str);

        return "set$str";
    }

    public function __isset($name)
    {
        $method = $this->getGetter($name);
        if (method_exists($this, $method)) {
            return (bool) $this->$method();
        }
    }
    public function __unset($name)
    {
        unset($this->data[$name]);
    }
    public function __set($name, $value)
    {
        $method = $this->getSetter($name);
        if (method_exists($this, $method)) {
            return $this->$method($value);
        }

        throw new Exception("Trying to set undefined property $name");
    }
    public function __get($name)
    {
        $method = $this->getGetter($name);
        if (method_exists($this, $method)) {
            return $this->$method();
        }

        throw new Exception("Trying to get undefined property $name");
    }
}
