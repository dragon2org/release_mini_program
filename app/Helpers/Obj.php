<?php


namespace App\Helpers;


class Obj
{
    /**
     * Get an item from an object using "dot" notation.
     *
     * @param  object   $object
     * @param  string  $property
     * @param  mixed   $default
     * @return mixed
     */
    public static function get($object, $property, $default = null)
    {
        if (is_null($property)) {
            return $object;
        }

        if (property_exists($object, $property)) {
            return $object->{$property};
        }

        foreach (explode('.', $property) as $segment) {
            if (! is_object($object) || !property_exists($object, $segment)) {
                return value($default);
            }

            $object = $object->{$segment};
        }

        return $object;
    }
}