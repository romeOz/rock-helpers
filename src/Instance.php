<?php

namespace rock\helpers;


use rock\base\ObjectInterface;

class Instance
{
    /**
     * @param object|string|array|static $reference an object or a reference to the desired object.
     * @param string $className name of class
     * @param bool $throwException
     * @return ObjectInterface
     * @throws InstanceException
     */
    public static function ensure($reference, $className, $throwException = true)
    {
        if (isset($reference) && class_exists('\rock\di\Container')) {
            return \rock\di\Container::load($reference);
        } else {
            $config = [];
            if (is_array($reference)) {
                $config = $reference;
                unset($config['class']);
            }

            if ($throwException && !class_exists($className)) {
                throw new InstanceException(InstanceException::UNKNOWN_CLASS, ['class' =>$className]);
            }
            return new $className($config);
        }
    }
}