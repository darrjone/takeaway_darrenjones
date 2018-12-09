(function($) {
    'use strict';

    /**
     * global variables
     */
    let restaurantGridContainer = $("#restaurant-grid-container"),
        menu = $(".sorting-values-menu"),
        menuItem = menu.find(".dropdown-item"),
        activeMenuItem = menu.find(".dropdown-item.active");

    /**
     * iterates the object to compile restaurant grid
     * @param restaurants
     */
    function iterateRestaurants(restaurants)
    {
        clearRestaurantGrid();

        $.each(restaurants, function(type, status){
            for(let i = 0; i < status.length; i++){
                $.each(status[i], function(key, restaurant){
                    buildRestaurantGrid(restaurant);
                });
            }
        });

    }

    /**
     * clears the grid
     */
    function clearRestaurantGrid(){
        restaurantGridContainer.empty();
    }

    /**
     * Builds the grid for each restaurant
     * @param restaurant
     */
    function buildRestaurantGrid(restaurant)
    {
        let restaurantGrid = $(".restaurant-grid.reference-grid").clone(),
            favourite = restaurantGrid.find(".favourite"),
            sortingValues = restaurant.sortingValues;

        restaurantGrid.removeClass("reference-grid");
        restaurantGrid.addClass("restaurant-status-"+restaurant.status.replace(" ", "-"));

        favourite.attr("data-uid", restaurant.uid);

        if(restaurant.favourite){
            favourite.addClass("active");
        }

        restaurantGrid.find(".title").html(restaurant.name);
        restaurantGrid.find(".status").html(restaurant.status);

        $.each(sortingValues, function(key, value){
            restaurantGrid.find("."+key).html(value);
        });


        restaurantGridContainer.append(restaurantGrid);

    }

    /**
     * Loads individual restaurants in a grid
     * @param element
     * @param sort
     */
    function loadRestaurants(element, sort){
        menuItem.removeClass("active");
        let item = element;
        $.ajax({
            method: "GET",
            url : "/sort/"+sort,
            dataType: "json",
            success: function success(response) {
                item.addClass("active");
                iterateRestaurants(response);
            },
            error: function error(_error) {
                console.log(_error);
            }
        })
    }

    /**
     * calls the load restaurants function based on the clickable menu sort item value
     */
    menuItem.click(function(e){
        e.preventDefault();
        let item = $(this);
        loadRestaurants(item, item.data("sort"));
    });

    /**
     * pre-loads the the restaurants based on the active menu sort item value
     */
    $(document).ready(function(){
        loadRestaurants(activeMenuItem, activeMenuItem.data("sort"));
    })

    /**
     * Clicking on favourite
     */
    $(document).on("click", ".favourite-and-status .favourite", function(e){
       e.preventDefault();
       let uid = $(this).attr("data-uid"),
           favourite = $(this),
           isActive = favourite.hasClass("active");


        $.ajax({
            method: "POST",
            url : "/favourite/"+uid,
            data: {
                favourite: (!isActive)? 1 : 0
            },
            dataType: "json",
            success: function success() {
                if(isActive){
                    favourite.removeClass("active");
                }else{
                    favourite.addClass("active");
                }
            },
            error: function error(_error) {
                console.log(_error);
            }
        })
    });


    $(document).on("keyup", "#restaurant-search", function(e){
        let input = $(this);
        $.ajax({
            type: "POST",
            url:  "/search",
            data: { "search" : input.val()},
            dataType: "json",
            success: function(restaurants){
                iterateRestaurants(restaurants);
            }
        });
    });


})(jQuery);