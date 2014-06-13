<?php

namespace Design311\WebsiteBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;


use Design311\WebsiteBundle\Entity\Address;

class BaseController extends Controller
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

    protected function getPermalink($title, $entity){
        $permalink = $title;
        $permalink = preg_replace("/[^a-z0-9\s\-]/i", "", $permalink); // Remove special characters
        $permalink = preg_replace("/\s\s+/", " ", $permalink); // Replace multiple spaces with one space
        $permalink = trim($permalink); // Remove trailing spaces
        $permalink = preg_replace("/\s/", "-", $permalink); // Replace all spaces with hyphens
        $permalink = preg_replace("/\-\-+/", "-", $permalink); // Replace multiple hyphens with one hyphen
        $permalink = preg_replace("/^\-|\-$/", "", $permalink); // Remove leading and trailing hyphens
        $permalink = strtolower($permalink);

        if ($permalink == 'social-dinners-nederland' || $permalink == 'social-dinners-belgie') {
            $permalink .= '-dinner';
        }

        $i = 1;
        while(true){
            $result = $this->getDoctrine()->getRepository('Design311WebsiteBundle:'.$entity)->findOneByPermalink($permalink);
            if ($result == null) {
                return $permalink;
                break; // not needed, but better be sure. Infinite loops are no fun
            }
            $permalink = $permalink . '-'.$i++;
        }
    }
    
}