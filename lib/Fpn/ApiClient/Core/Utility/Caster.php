<?php

namespace Fpn\ApiClient\Core\Utility;

class Caster
{
    public static function cast($from, &$to)
    {
        if (!is_object($from)) {
            $from = (object)$from;
        }

        $sourceReflection = new \ReflectionObject($from);
        $destReflection   = new \ReflectionObject($to);
        $sourceProperties = $sourceReflection->getProperties();

        foreach ($sourceProperties as $sourceProperty) {
            $sourceProperty->setAccessible(true);

            $name  = $sourceProperty->getName();
            $value = $sourceProperty->getValue($from);

            // lowerCamelCaseAttribute
            $name = lcfirst(implode('', array_map('ucfirst', explode('_', $name))));

            if ($destReflection->hasProperty($name)) {
                $destProperty = $destReflection->getProperty($name);
                $destProperty->setAccessible(true);
                $destProperty->setValue($to, $value);
            }
        }
    }

    public static function arrayToStdObject($array)
    {
        $object = new \stdClass;

        foreach ($array as $key => $value) {
            $object->{$key} = $value;
        }

        return $object;
    }
}
