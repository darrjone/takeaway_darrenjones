## Introduction

My choice of framework for this project is to work with [**Symfony 3.4**](https://symfony.com/doc/3.4/setup.html). I have chosen this framework as I am more familiar with and it also comes with quite a lot of useful libraries that can help me out with the task in hand. For the front-end part of the project I have decided to work with [**Bootstrap**](https://getbootstrap.com/docs/4.0/getting-started/introduction/), [**SASS**](https://sass-lang.com/), [**jQuery**](https://jquery.com/) and [**gulp**](https://gulpjs.com/). I am using gulp to compile [**SASS**](https://sass-lang.com/) into CSS and concatenate any JavaScript and [**SASS**](https://sass-lang.com/) libraries into one file. As for testing I chose to work with [**PHPUnit**](https://github.com/sebastianbergmann/phpunit) as a testing framework (This had to be added as an extra package).

## Setup

To fully run this application you would need [**Composer**](https://github.com/composer/composer) and [**PHP**](http://php.net/manual/en/migration70.new-features.php) installed on your system. Once these two are installed CD into the directory of the project and run **composer install****. **As soon as composer finished installing the packages related to the project run** php bin/console server:run ****.** This will run the project into a virtual server and will provide you the link to go to as follows: [**http://127.0.0.1:8000**](http://127.0.0.1:8000)_(port might be different due to other applications using the same port)._

## Implementation

By Reading the document provided I came up with some few ideas that needed for the back-end and front-end. I first started off working on the back-end system to implement the necessary functions and objects that relate to the application. Following this I then begin to work on the front-end components. First off getting the JavaScript functions working to view and search restaurants and last thing was to make it pretty using SASS. The application itself relies a lot on the back-end (PHP) and less on the front-end part (JavaScript).

### Back-end

1. Get ready on creating a fresh new Symfony project
2. Creating two Class objects that relate to the Restaurant
  1. Classes
    1. Restaurant Model
      1. Added a unique identifier to be used as reference for favouriting the restaurant
    2. Sorting Values Model
      1. Also added the bonus bit to this Class model
    3. Adding functions to inject an array into the class object _(Created a trait for this as I also need it for the service part of the project)_
3. Create a service which handles:
  1. Getting the data from the JSON file and storing it in the session for later manipulation
  2. Add the sorting mechanism
  3. Add a search mechanism
  4. Add the set favourite mechanism

### Front-end

1. Added the gulp file via npm
  1. Added the SASS compiler to compile my SASS files as well as vendor SASS files to CSS which points to the /web directory
  2.  Added a JavaScript compiler to compile other vendors together into one file such as jQuery and Boostrap including its dependencies
  3. Created a watch task to watch the SASS files while being changed.
2. Started creating the necessary twig templates with the menu and the restaurant grid layout. I&#39;ve created the restaurant grid layout as a reference to be cloned later via JavaScript
3. Started working on the JavaScript components
  1. Build the restaurant grid layout builder
  2. Added sorting call to action function which if succeeded runs through the restaurant grid layout builder
  3. Added search call to action function which if succeeded runs also through the restaurant grid layout builder
  4. Added favourite call to action function which changes the state of the favourite heart icon by adding a class name active
4. Making things pretty
  1. Added some classes and colours that relate to lieferando.com
  2. Made a few adjustments on the CSS when going through different views (Desktop, Tablet and Mobile). I used Chrome&#39;s Developer Tools to mimic different device views

## Testing

To run the test just CD into the project director and run this command: **./vendor/bin/simple-phpunit**

Make sure you composer ran properly in the early mentioned setup process.

For testing I chose to work with PHPUnit. I have tested mainly the Controller and the Service. For the Controller I went on testing the sorting action and search action functions. I left out the favourite action when it comes to testing passed results as PHPUnit by design cannot store sessions so the unique identifier was changing each time this action was called. For this I had to test the Service class directly by pre-populating the restaurants with random data which allowed me to test properly setting up a favourite on a particular restaurant.

## Improvements

Giving more time on the application I would have made things a bit different especially when it comes to loading the JSON file. I would have created a command that reads from the JSON file and stores it in Symfony&#39;s cache folder. The command would run in the background (cronjob) from the server and replaces the file in every given hour or so. The cached file would contain the Restaurant objects and other related objects (SortingValue Object). As for the session I would only use it to store the favoured restaurant with the given unique identifier. Later matching the cached file and the favourited restaurants from the session to view the entire result. As for the front-end part of the application I would have made both JavaScript and CSS files minified to speed up the request and also add a loading animation just in case the response is a bit slow the user would know that something is coming up.

## Bonus

I have added the top restaurants formula, but the formula doesn&#39;t seem to add up. Somehow restaurants that are further away seem to go first rather than at the end. The average rating doesn&#39;t seem to make any contribution to this formula at all.
