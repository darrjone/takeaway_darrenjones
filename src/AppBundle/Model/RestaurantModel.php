<?php

namespace AppBundle\Model;


use AppBundle\Traits\HelperTrait;

class RestaurantModel
{
    private $uid;
    private $name;
    private $status;
    private $sortingValues = [];
    private $favourite = false;

    use HelperTrait;

    /**
     * RestaurantModel constructor.
     * @param array $array
     */
    public function __construct(array $array)
    {
        if(is_array($array)){
            $this->build($array);
        }
    }

    /**
     * @param array $array
     * Builds the object from a given array including child objects
     */
    public function build(array $array)
    {
        foreach($array as $key=>$value){
            switch($key){
                case "sortingValues":
                    $this->sortingValues = $this->setMany(new SortingValuesModel(), $value);
                    break;
                default:
                    $this->setOne($this, $key, $value);
                    break;
            }
        }

        $this->setUid(uniqid("RM", true));
    }

    /**
     * @return mixed
     */
    public function getUid()
    {
        return $this->uid;
    }

    /**
     * @param mixed $uid
     */
    public function setUid($uid)
    {
        $this->uid = $uid;
    }

    /**
     * @return mixed
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param mixed $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * @return mixed
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * @param mixed $status
     */
    public function setStatus($status)
    {
        $this->status = $status;
    }

    /**
     * @return array
     */
    public function getSortingValues(): SortingValuesModel
    {
        return $this->sortingValues;
    }

    /**
     * @param SortingValuesModel $sortingValuesModel
     */
    public function setSortingValues(SortingValuesModel $sortingValuesModel)
    {
        $this->sortingValues = $sortingValuesModel;
    }

    /**
     * @return bool
     */
    public function isFavourite(): bool
    {
        return $this->favourite;
    }

    /**
     * @param bool $favourite
     */
    public function setFavourite(bool $favourite)
    {
        $this->favourite = $favourite;
    }


}