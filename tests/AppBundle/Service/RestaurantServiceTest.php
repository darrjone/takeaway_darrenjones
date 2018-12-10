<?php

namespace Tests\AppBundle\Service;

use AppBundle\Service\RestaurantService;
use AppBundle\Model\RestaurantModel;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class RestaurantServiceTest extends WebTestCase
{
    /**
     * @var RestaurantService $restaurantService
     */
    private $restaurantService;
    private $restaurants;

    /**
     * Setting up the test case
     * @throws \Exception
     */
    protected function setUp(){
        $client = static::createClient();
        $container = $client->getContainer();
        $restaurantService = $container->get("restaurant.service");
        $this->restaurantService = $restaurantService;

        $restaurants = [];
        $statuses = ["open", "order ahead", "closed"];
        for($i = 0; $i < 5; $i++){
            $restaurants[] = new RestaurantModel([
                "name" => "test{$i}",
                "status" => $statuses[array_rand($statuses, 1)],
                "sortingValues" => [
                    "bestMatch" => random_int(0, 100),
                    "newest" => random_int(0, 100),
                    "ratingAverage" => random_int(0, 100),
                    "distance" => random_int(0, 100),
                    "popularity" => random_int(0, 100),
                    "averageProductPrice" => random_int(0, 100),
                    "deliveryCosts" => random_int(0, 100),
                    "minCost" => random_int(0, 100),
                    "topRestaurants" => random_int(0, 100),
                ],
                "favourite" => false

            ]);
        }
        $this->restaurantService->setAll($restaurants);
        $this->restaurants = $this->restaurantService->getAll();
    }

    /**
     * Doing a thorough test in the set Favourite by key
     * @throws \AppBundle\Exception\InvalidFavouriteUidException
     */
    public function testSetFavouriteByUid()
    {
        /**
         * @var RestaurantModel $restaurant
         */
        $restaurant = $this->restaurants[0];

        //should return the value that was given (true)
        $result = $this->restaurantService->setFavouriteByUid(
            $restaurant->getUid(), true
        );
        $this->assertEquals(true, $result);

        //should contain a key name favourite once you add a favoured restaurant
        $result = $this->restaurantService->getAllGrouped();
        $this->assertEquals(true, array_key_exists("favourites", $result));


        //should return the value give (false)
        $result = $this->restaurantService->setFavouriteByUid(
            $restaurant->getUid(), false
        );
        $this->assertEquals(false, $result);

    }


}