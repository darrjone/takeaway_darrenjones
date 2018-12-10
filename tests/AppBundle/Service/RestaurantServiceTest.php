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

    protected function setUp(){
        $client = static::createClient();
        $container = $client->getContainer();
        $restaurantService = $container->get("restaurant.service");
        $this->restaurantService = $restaurantService;

        $restaurants = [];
        for($i = 0; $i < 5; $i++){
            $restaurants[] = new RestaurantModel([
                "name" => "test{$i}",
                "status" => array_rand(["open", "open ahead", "close"])[0],
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

    public function testSetFavouriteByKey()
    {
        /**
         * @var RestaurantModel $restaurant
         */
        $restaurant = $this->restaurants[0];

        $result = $this->restaurantService->setFavouriteByUid(
            $restaurant->getUid(), false
        );

        $this->assertEquals(true, $result);
    }


}