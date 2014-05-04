<?php

namespace Design311\WebsiteBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Design311\WebsiteBundle\Entity\Address;


class GeocodeController extends Controller
{
    protected function geocode(Address $address)
    {

        $key = $this->container->getParameter('geocoding_api_key');
        $encodedAddress = urlencode($address->__toString());

        $json = json_decode(file_get_contents('https://maps.googleapis.com/maps/api/geocode/json?address='.$encodedAddress.'&sensor=false&key='. $key));

        if ($json->status == 'OK') {
        	return $json->results{0}->geometry->location;
        }
        else{
        	return $json->status;
        }


    }
}
