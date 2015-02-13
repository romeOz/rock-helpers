<?php

namespace rock\helpers;


use rock\base\ObjectInterface;

class Instance
{
    /**
     * @param object|string|array|static $reference an object or a reference to the desired object.
     * @param string|null $className name of class
     * @param bool $throwException
     * @return ObjectInterface
     * @throws InstanceException
     */
    public static function ensure($reference, $className = null, $throwException = true)
    {
        if (is_object($reference)) {
            return $reference;
        }
        if (isset($reference) && class_exists('\rock\di\Container')) {
            return \rock\di\Container::load($reference);
        } else {
            $config = [];
            if (is_array($reference)) {
                $config = $reference;
                if (!isset($className)) {
                    $className = $config['class'];
                }
                unset($config['class']);
            }

            if (!class_exists($className)) {
                if ($throwException) {
                    throw new InstanceException(InstanceException::UNKNOWN_CLASS, ['class' =>$className]);
                }
                return null;
            }
            return new $className($config);
        }
    }
}