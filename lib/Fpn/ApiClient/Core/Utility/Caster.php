<?php

namespace Fpn\ApiClient\Core\Utility;

class Caster
{
    public static function cast($from, &$to)
    {
        $sourceReflection = new \ReflectionObject($from);
        $destReflection   = new \ReflectionObject($to);
        $sourceProperties = $sourceReflection->getProperties();

        foreach ($sourceProperties as $sourceProperty) {
            $sourceProperty->setAccessible(true);

            $name  = $sourceProperty->getName();
            $value = $sourceProperty->getValue($from);

            if ($destReflection->hasProperty($name)) {
                $destProperty = $destReflection->getProperty($name);
                $destProperty->setAccessible(true);
                $destProperty->setValue($to, $value);
            }
        }
    }
}
