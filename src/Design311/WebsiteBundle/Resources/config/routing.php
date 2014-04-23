<?php

use Symfony\Component\Routing\RouteCollection;
use Symfony\Component\Routing\Route;

$collection = new RouteCollection();

$collection->add('design311_website_homepage', new Route('/hello/{name}', array(
    '_controller' => 'Design311WebsiteBundle:Default:index',
)));

return $collection;
