<?php

namespace rock\helpers;


use rock\base\ObjectInterface;

class Instance
{
    /**
     * @param object|string|array|static $reference an object or a reference to the desired object.
     * @param string|null $defaultClass default name of class
     * @param array $args arguments of constructor.
     * @param bool $throwException
     * @return ObjectInterface
     * @throws InstanceException
     */
    public static function ensure($reference, $defaultClass = null, array $args = [], $throwException = true)
    {
        if (is_object($reference)) {
            return $reference;
        }
        if (isset($reference) && class_exists('\rock\di\Container')) {
            return \rock\di\Container::load($reference, $args, $throwException);
        } else {
            $config = [];
            if (is_array($reference)) {
                $config = $reference;
                if (!isset($defaultClass)) {
                    $defaultClass = $config['class'];
                }
                unset($config['class']);
            } elseif (is_string($reference) && !isset($defaultClass)) {
                $defaultClass = $reference;
            }

            if (!class_exists($defaultClass)) {
                if ($throwException) {
                    throw new InstanceException(InstanceException::UNKNOWN_CLASS, ['class' => Helper::getValue($defaultClass, 'null', true)]);
                }
                return null;
            }
            if (!empty($args)) {
                $reflect = new \ReflectionClass($defaultClass);
                $args = array_merge($args, $config ? [$config] : $config);
                return $reflect->newInstanceArgs($reflect->getConstructor() ? $args : []);
            }
            return new $defaultClass($config);
        }
    }
}