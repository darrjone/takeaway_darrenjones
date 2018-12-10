<?php

namespace Tests\AppBundle\Controller;

use AppBundle\Model\RestaurantModel;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use AppBundle\Model\SortingValuesModel;

class DefaultControllerTest extends WebTestCase
{
    private $sortingValues;

    /**
     * Setting up the test case
     */
    protected function setUp(){
        $sortValues = new SortingValuesModel();
        $sortValues = $sortValues->getAllVariables();
        $this->sortingValues = $sortValues;
    }
    /**
     * Helper Functions
     * @param $restaurants
     * @param $sortValue
     */
    private function restaurantAssertGreaterThenIterator($restaurants, $sortValue){
        $previousValue = 0;
        foreach($restaurants as $restaurant){
            /**
             * @var RestaurantModel $restaurant
             */
            $currentValue = $restaurant["sortingValues"][$sortValue];

            if($previousValue === 0){
                $previousValue = $currentValue;
            }

            $this->assertGreaterThanOrEqual($currentValue, $previousValue);
            $previousValue = $currentValue;
        }
    }

    /**
     * Helper Functions
     * @param $restaurants
     * @param $sortValue
     */
    private function restaurantAssertLessThenIterator($restaurants, $sortValue){
        $previousValue = 0;
        foreach($restaurants as $restaurant){
            /**
             * @var RestaurantModel $restaurant
             */
            $currentValue = $restaurant["sortingValues"][$sortValue];

            if($previousValue === 0){
                $previousValue = $currentValue;
            }

            $this->assertLessThanOrEqual($currentValue, $previousValue);
            $previousValue = $currentValue;
        }
    }

    public function testSortAjax()
    {
        //should always return 200 (each sort value should exists and return a list of restaurants)
        foreach($this->sortingValues as $sortValue)
        {
            $client = static::createClient();
            $client->request('GET', "/sort/{$sortValue}");
            $this->assertEquals(200, $client->getResponse()->getStatusCode());
            $this->assertNotEmpty($client->getResponse()->getContent());
            $result = json_decode($client->getResponse()->getContent(), true);

            /**
             * Sorting out the order based on the sorting value
             */
            switch (strtolower($sortValue)) {
                //should be in descending order
                case "bestmatch":
                case "newest":
                case "ratingaverage":
                case "popularity":
                case "toprestaurants":
                    //may not contain a regular depending if all of them are set to favourite
                    if(array_key_exists("regular", $result))
                    {
                        $previousKey = 0;
                        foreach($result["regular"] as $key=>$restaurants)
                        {

                            //checks if the restaurants are in order (open, open ahead, closed)
                            $this->assertGreaterThanOrEqual($previousKey, $key);
                            $previousKey = $key;

                            switch($key){
                                case 0: //open
                                    $this->restaurantAssertGreaterThenIterator($restaurants, $sortValue);
                                    break;
                                case 1: //open ahead
                                    $this->restaurantAssertGreaterThenIterator($restaurants, $sortValue);
                                    break;
                                case 2: //closed
                                    $this->restaurantAssertGreaterThenIterator($restaurants, $sortValue);
                                    break;
                            }
                        }
                    }
                    //may not contain a favourite depending if none are favoured
                    if(array_key_exists("favourite", $result))
                    {
                        $previousKey = 0;
                        foreach($result["favourite"] as $key=>$restaurants)
                        {

                            //checks if the restaurants are in order (open, open ahead, closed)
                            $this->assertGreaterThanOrEqual($previousKey, $key);
                            $previousKey = $key;

                            switch($key){
                                case 0: //open
                                    $this->restaurantAssertGreaterThenIterator($restaurants, $sortValue);
                                    break;
                                case 1: //open ahead
                                    $this->restaurantAssertGreaterThenIterator($restaurants, $sortValue);
                                    break;
                                case 2: //closed
                                    $this->restaurantAssertGreaterThenIterator($restaurants, $sortValue);
                                    break;
                            }
                        }
                    }

                    break;
                    //should be in ascending order
                case "distance":
                case "averageproductprice":
                case "deliverycosts":
                case "mincost":
                    //may not contain a regular depending if all of them are set to favourite
                    if(array_key_exists("regular", $result))
                    {
                        $previousKey = 0;
                        foreach($result["regular"] as $key=>$restaurants)
                        {

                            //checks if the restaurants are in order (open, open ahead, closed)
                            $this->assertGreaterThanOrEqual($previousKey, $key);
                            $previousKey = $key;

                            switch($key){
                                case 0: //open
                                    $this->restaurantAssertLessThenIterator($restaurants, $sortValue);
                                    break;
                                case 1: //open ahead
                                    $this->restaurantAssertLessThenIterator($restaurants, $sortValue);
                                    break;
                                case 2: //closed
                                    $this->restaurantAssertLessThenIterator($restaurants, $sortValue);
                                    break;
                            }
                        }
                    }
                    //may not contain a favourite depending if none are favoured
                    if(array_key_exists("favourite", $result))
                    {
                        $previousKey = 0;
                        foreach($result["favourite"] as $key=>$restaurants)
                        {

                            //checks if the restaurants are in order (open, open ahead, closed)
                            $this->assertGreaterThanOrEqual($previousKey, $key);
                            $previousKey = $key;

                            switch($key){
                                case 0: //open
                                    $this->restaurantAssertLessThenIterator($restaurants, $sortValue);
                                    break;
                                case 1: //open ahead
                                    $this->restaurantAssertLessThenIterator($restaurants, $sortValue);
                                    break;
                                case 2: //closed
                                    $this->restaurantAssertLessThenIterator($restaurants, $sortValue);
                                    break;
                            }
                        }
                    }
                    break;
            }
        }

        //fail
        $client = static::createClient();
        $client->request("GET", "/sort/test");
        $this->assertEquals(500, $client->getResponse()->getStatusCode());
    }


    public function testFavouriteAjax()
    {
        //fail
        $client = static::createClient();
        $client->request("POST", "/favourite/test", [
            "favourite" => false
        ]);
        $this->assertEquals(404, $client->getResponse()->getStatusCode());
    }

    /**
     * See app/Resources/data/sample.json for restaurant reference
     */
    public function testSearchAjax()
    {
        //1. result should be more than zero
        $client = static::createClient();
        $client->request("POST", "/search", [
            "search" => "Sushi"
        ]);
        $result = json_decode($client->getResponse()->getContent(), true);
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertGreaterThan(0, count($result));

        //there should be four restaurants that have the word Sushi in them
        //it might be that we don't have regular but only favourites
        if(array_key_exists("regular", $result)){
            $restaurantAmount = 0;

            foreach($result["regular"] as $status=>$restaurants){
                $restaurantAmount = $restaurantAmount + count($restaurants);
            }

            $this->assertEquals(4, $restaurantAmount);
        }

        //it might be that we don't have favourites but only regulars
        if(array_key_exists("favourite", $result))
        {
            $restaurantAmount = 0;

            foreach($result["favourite"] as $status=>$restaurants){
                $restaurantAmount = $restaurantAmount + count($restaurants);
            }

            $this->assertEquals(4, $restaurantAmount);
        }


        //2. result should be zero
        $client = static::createClient();
        $client->request("POST", "/search", [
            "search" => "Darren Jones"
        ]);
        $result = json_decode($client->getResponse()->getContent(), true);

        $this->assertEquals(200, $client->getResponse()->getStatusCode());

        //it might be that we don't have regular but only favourites
        if(array_key_exists("regular", $result)){
            $this->assertEquals(0, count($result["regular"]));
        }

        //it might be that we don't have favourites but only regulars
        if(array_key_exists("favourite", $result)){
            $this->assertEquals(0, count($result["favourite"]));
        }
    }
}
