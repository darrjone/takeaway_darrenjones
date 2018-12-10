<?php
namespace AppBundle\Service;

use AppBundle\Exception\InvalidFavouriteUidException;
use AppBundle\Exception\InvalidSortingNameException;
use AppBundle\Model\SortingValuesModel;
use AppBundle\Traits\HelperTrait;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Session\Session;
use AppBundle\Model\RestaurantModel;

class RestaurantService
{
    private $session;
    private $filePath;
    private $restaurants;

    const ORDER_ASCENDING = "<";
    const ORDER_DESCENDING = ">";
    const SESSION_KEY = "restaurants";
    const SESSION_KEY_ORDER = "restaurants.order";

    use HelperTrait;

    public function __construct(Session $session, $filePath)
    {
        $this->session = $session;
        $this->filePath = $filePath;
    }

    public function setAll($restaurants)
    {
        $this->restaurants = $restaurants;
    }

    /**
     * @return array
     * Gets the restaurants from the file and maps each restaurant into an object. These objects then are stored
     * in a session so that they can be modified later (example: favourites).
     */
    public function getAll()
    {
        if(!empty($this->restaurants)){
            return $this->restaurants;
        }

        if(!$this->session->has(self::SESSION_KEY)) {
            $sample = json_decode(file_get_contents($this->filePath), true);

            $restaurants = [];

            if (array_key_exists("restaurants", $sample)) {
                foreach ($sample["restaurants"] as $restaurant) {
                    $restaurantModel = new RestaurantModel($restaurant);
                    $restaurantModel->getSortingValues()->calculateTopRestaurants();
                    $restaurants[] = $restaurantModel;
                }
            }

            $this->session->set(self::SESSION_KEY, $restaurants);
        }

        return $this->session->get(self::SESSION_KEY);
    }

    /**
     * @return array
     * Groups all the restaurants by regular and favourites. Each group is sorted by (open, order ahead and closed), will
     * load the current sorted restaurants
     * function.
     */
    public function getAllGrouped()
    {
        return $this->groupAll($this->getAll());
    }

    /**
     * @param string $name
     * @param string $order
     * @return array
     * Orders the restaurant by the attribute name and order (ASC, DESC), replaces the current stored session with the
     * sorted one at hand. Creates a new session key with the order by name (useful to keep everything at hand on refresh)
     */
    public function getAllBy($name = "popularity", $order=self::ORDER_ASCENDING)
    {

        $restaurants = $this->getAll();

        usort($restaurants, function($a, $b) use ($name, $order){

            /**
             * @var $a RestaurantModel
             * @var $b RestaurantModel
             */
            $compareA = $this->getOne($a->getSortingValues(), $name);
            $compareB = $this->getOne($b->getSortingValues(), $name);

            if($compareA !== false && $compareB !== false){
                switch($order){
                    case self::ORDER_ASCENDING:
                        if($compareA == $compareB){
                            return 0;
                        }
                        return ($compareA < $compareB) ? -1 : 1;
                        break;
                    case self::ORDER_DESCENDING:
                        if($compareA == $compareB){
                            return 0;
                        }
                        return ($compareA > $compareB) ? -1 : 1;
                        break;
                }
            }else{
                throw new InvalidSortingNameException("Sorting by {$name} doesn't seem to exist");
            }

        });

        $this->session->set(self::SESSION_KEY, $restaurants); //update the session
        $this->session->set(self::SESSION_KEY_ORDER, $name); //updates the session order by {name}

        return $this->session->get(self::SESSION_KEY);
    }

    /**
     * @param string $name
     * @param string $order
     * @return array
     * Groups all the restaurants by regular and favourites. Each group is sorted by (open, order ahead and closed), each
     * restaurant is then sorted by any sorting values in (ASC, DESC). The sorting values are pre-defined via the getAllBY
     * function.
     */
    public function getAllByGrouped($name="popularity", $order=self::ORDER_ASCENDING)
    {
        return $this->groupAll($this->getAllBy($name, $order));
    }

    /**
     * @param $name
     * @return array
     */
    public function getManyByName($name)
    {
        $restaurants = [];

        if(empty($name)) {
            return $this->getAll();
        }

        foreach ($this->getAll() as $restaurant) {
            /**
             * @var RestaurantModel $restaurant
             */

            if (strpos(strtolower($restaurant->getName()), strtolower($name)) !== false) {
                $restaurants[] = $restaurant;
            }
        }

        return $restaurants;
    }

    /**
     * @param $name
     * @return array
     */
    public function getManyByNameGrouped($name){
        return $this->groupAll($this->getManyByName($name));
    }

    /**
     * @param array $restaurants
     * @param $uid
     * @param bool $bool
     * @return bool
     * @throws InvalidFavouriteUidException
     */
    public function setFavouriteByUid($uid, $bool = false)
    {
        foreach($this->getAll() as $restaurant){
            /**
             * @var RestaurantModel $restaurant
             */
            if($restaurant->getUid() == $uid){
                $restaurant->setFavourite($bool);
                return $bool;
            }
        }
        throw new InvalidFavouriteUidException("The restaurant with {$uid} doesn't seem to exist");
    }

    /**
     * @param array $restaurants
     * @return array
     * Groups all the restaurants by regular and favourites. Each group is sorted by (open, order ahead and closed)
     */
    public function groupAll(array $restaurants)
    {
        $grouping = ["open" => 0, "order ahead" => 1, "closed" => 2];

        $results = [];

        $favourites = [];
        $regular = [];

        foreach($restaurants as $key=>$restaurant){
            /**
             * @var RestaurantModel $restaurant
             */

            if($restaurant->isFavourite()){
                $favourites[$grouping["{$restaurant->getStatus()}"]][$key] = $restaurant;
            }else{
                $regular[$grouping["{$restaurant->getStatus()}"]][$key] = $restaurant;
            }
        }


        if(count($favourites) > 0) {
            $results["favourites"] = $this->sortFirstDimensionArray($favourites);
        }

        $results["regular"] = $this->sortFirstDimensionArray($regular);
        return $results;
    }
    /**
     * @return array
     */
    public function getSortValues(){
        $values = new SortingValuesModel();
        return $values->getAllVariables();
    }


}