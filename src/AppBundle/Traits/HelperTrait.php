<?php

namespace AppBundle\Traits;


trait HelperTrait
{
    /**
     * @param $class
     * @param string $name
     * @param mixed $value
     * @return object
     * A method helper which sets the method with a value on any given class
     */
    public function setOne($class, $name, $value)
    {
        $methodName = "set" . ucfirst(strtolower($name));

        if(method_exists($class, $methodName)){
            $class->$methodName($value);
        }

        return $class;
    }

    /**
     * @param $class
     * @param array $array
     * @return object
     * A method helper which sets multiple methods with a value on any give class
     */
    public function setMany($class, array $array){
        foreach($array as $key=>$value){
            $this->setOne($class, $key, $value);
        }

        return $class;
    }

    /**
     * @param $class
     * @param $name
     * @return mixed|bool
     * Helper which gets the value of a given method name
     */
    public function getOne($class, $name)
    {
        $methodName = "get" . ucfirst(strtolower($name));

        if(method_exists($class, $methodName)){
            return $class->$methodName();
        }

        return false;
    }

    /**
     * @param $array
     * @return array
     * A function which sorts the first dimension of the given multi-dimension array, sorting is automatically
     * in ascending mode
     */
    public function sortFirstDimensionArray(array $array) : array
    {
        $sortedArray = [];

        $sorted_keys = array_keys($array);
        sort($sorted_keys,SORT_NUMERIC);

        $count = 0;
        foreach($sorted_keys as $key){
            $sortedArray[$count] = $array[$key];
            $count++;
        }

        return $sortedArray;
    }
}