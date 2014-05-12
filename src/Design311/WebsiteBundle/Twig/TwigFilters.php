<?php

namespace Design311\WebsiteBundle\Twig;

class TwigFilters extends \Twig_Extension
{
    public function getFilters()
    {
        return array(
            new \Twig_SimpleFilter('objectSort', array($this, 'objectSortFilter')),
        );
    }

    public function objectSortFilter($array, $property)
    {
        print_r($array);die;
        return $array;
    }

    public function getName()
    {
        return 'twig_filters';
    }
}