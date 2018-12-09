<?php

namespace AppBundle\Model;


class SortingValuesModel
{
    private $bestMatch = 0.0;
    private $newest = 0.0;
    private $ratingAverage = 0.0;
    private $distance = 0.0;
    private $popularity = 0.0;
    private $averageProductPrice = 0.0;
    private $deliveryCosts = 0.0;
    private $minCost = 0.0;
    private $topRestaurants = 0.0;

    /**
     * @return float
     */
    public function getBestMatch(): float
    {
        return $this->bestMatch;
    }

    /**
     * @param float $bestMatch
     */
    public function setBestMatch(float $bestMatch)
    {
        $this->bestMatch = $bestMatch;
    }

    /**
     * @return float
     */
    public function getNewest(): float
    {
        return $this->newest;
    }

    /**
     * @param float $newest
     */
    public function setNewest(float $newest)
    {
        $this->newest = $newest;
    }

    /**
     * @return float
     */
    public function getRatingAverage(): float
    {
        return $this->ratingAverage;
    }

    /**
     * @param float $ratingAverage
     */
    public function setRatingAverage(float $ratingAverage)
    {
        $this->ratingAverage = $ratingAverage;
    }

    /**
     * @return float
     */
    public function getDistance(): float
    {
        return $this->distance;
    }

    /**
     * @param float $distance
     */
    public function setDistance(float $distance)
    {
        $this->distance = $distance;
    }

    /**
     * @return float
     */
    public function getPopularity(): float
    {
        return $this->popularity;
    }

    /**
     * @param float $popularity
     */
    public function setPopularity(float $popularity)
    {
        $this->popularity = $popularity;
    }

    /**
     * @return float
     */
    public function getAverageProductPrice(): float
    {
        return $this->averageProductPrice;
    }

    /**
     * @param float $averageProductPrice
     */
    public function setAverageProductPrice(float $averageProductPrice)
    {
        $this->averageProductPrice = $averageProductPrice;
    }

    /**
     * @return float
     */
    public function getDeliveryCosts(): float
    {
        return $this->deliveryCosts;
    }

    /**
     * @param float $deliveryCosts
     */
    public function setDeliveryCosts(float $deliveryCosts)
    {
        $this->deliveryCosts = $deliveryCosts;
    }

    /**
     * @return float
     */
    public function getMinCost(): float
    {
        return $this->minCost;
    }

    /**
     * @param float $minCost
     */
    public function setMinCost(float $minCost)
    {
        $this->minCost = $minCost;
    }

    /**
     * @return float
     */
    public function getTopRestaurants()
    {
        return $this->topRestaurants;
    }

    public function calculateTopRestaurants()
    {
        $this->topRestaurants =  (($this->distance * $this->popularity) + $this->ratingAverage);
    }

    /**
     * @return array
     * Gets all the variables that are instantiated (all the named sorting values)
     */
    public function getAllVariables()
    {
        return array_keys(get_object_vars($this));
    }
}