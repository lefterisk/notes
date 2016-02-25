<?php
namespace Application\Model;

abstract class AbstractModel
{
    /**
     * Exchange and array list of key => values for object variables.
     *
     * @param array $data
     */
    public function exchangeArray(array $data)
    {
        foreach ($data AS $key => $datum) {
            if (property_exists($this, $key)) {
                $this->{$key} = is_numeric($datum) ? (int) $datum : $datum;
            }
        }
    }

    /**
     * Exchange an object Model with this one.
     *
     * @param Object $data
     */
    public function exchangeObject($data)
    {
        foreach ($data AS $key => $datum) {
            if (property_exists($this, $key)) {
                $this->{$key} = $datum;
            }
        }
    }

    /**
     * Wrapper for exchangeArray - used by some magic methods.
     *
     * @param array $data
     */
    public function updateFromArray(array $data)
    {
        $this->exchangeArray($data);
    }

    /**
     * Get an array copy of the object vars.
     *
     * @return array
     */
    public function getArrayCopy()
    {
        return get_object_vars($this);
    }
}