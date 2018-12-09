<?php

namespace AppBundle\Controller;

use AppBundle\Exception\InvalidSortingNameException;
use AppBundle\Model\RestaurantModel;
use AppBundle\Service\RestaurantService;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;

class DefaultController extends Controller
{
    /**
     * @return RestaurantService
     */
    public function getService()
    {
        /**
         * @var RestaurantService $restaurantService
         */
        $restaurantService = $this->container->get("restaurant.service");
        return $restaurantService;
    }

    /**
     * @return Serializer
     */
    public function getSerializer()
    {
        $encoders = array(new JsonEncoder());
        $normalizers = array(new ObjectNormalizer());

        return new Serializer($normalizers, $encoders);
    }

    /**
     * @Route("/", name="homepage")
     */
    public function indexAction(Request $request)
    {
        $sortingValueDefault = "topRestaurants";
        if($request->getSession()->has($this->getService()::SESSION_KEY_ORDER)){
            $sortingValueDefault = $request->getSession()->get($this->getService()::SESSION_KEY_ORDER);
        }
        $sortingValues = $this->getService()->getSortValues();

        // replace this example code with whatever you need
        return $this->render('pages/index.html.twig', [
            "sortingValues" => $sortingValues,
            "sortingValueDefault" => $sortingValueDefault
        ]);
    }

    /**
     * @param $sort
     * @return JsonResponse|Response
     * @Route("/sort/{sort}", name="sort_ajax")
     */
    public function sortAjaxAction($sort)
    {
        try {
            $order = $this->getService()::ORDER_DESCENDING;

            /**
             * Sorting out the order based on the sorting value
             */
            switch (strtolower($sort)) {
                case "bestmatch":
                case "newest":
                case "ratingaverage":
                case "popularity":
                case "toprestaurants":
                    $order = $this->getService()::ORDER_DESCENDING;
                    break;
                case "distance":
                case "averageproductprice":
                case "deliverycosts":
                case "mincost":
                    $order = $this->getService()::ORDER_ASCENDING;
                    break;
            }

            $restaurants = $this->getService()->getAllByGrouped($sort, $order);

            $jsonContent = $this->getSerializer()->serialize($restaurants, 'json');

            return new Response($jsonContent, 200);
        }catch(\Exception $e){
            return new JsonResponse("Something went wrong!", 500);
        }
    }

    /**
     * @param Request $request
     * @param $uid
     * @return JsonResponse
     * @Route("favourite/{uid}", name="favourite_ajax")
     */
    public function favouriteAjaxAction(Request $request, $uid)
    {
        try {
            $params = $request->request->all();

            $favourite = 0;
            if (array_key_exists("favourite", $params)) {
                $favourite = $params["favourite"];
            }

            $favourite = boolval($favourite);

            $this->getService()->setFavouriteByKey($uid, boolval($favourite));

            return new JsonResponse(json_encode($params), 200);
        }catch(\Exception $e){
            return new JsonResponse("Something went wrong!", 500);
        }
    }

    /**
     * @param Request $request
     * @return JsonResponse|Response
     * @Route("search", name="search_ajax")
     */
    public function searchAjaxAction(Request $request)
    {
        try{
            $params = $request->request->all();

            $restaurants = [];
            if(array_key_exists("search", $params))
            {
                $restaurants = $this->getService()->getManyByNameGrouped($params["search"]);
            }

            $jsonContent = $this->getSerializer()->serialize($restaurants, 'json');

            return new Response($jsonContent, 200);
        }catch(\Exception $e){
            return new JsonResponse("Something went wrong!", 500);
        }
    }
}
