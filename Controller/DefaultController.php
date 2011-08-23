<?php

namespace Google\GeolocationBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Google\GeolocationBundle\Geolocation\Geolocation;

class DefaultController extends Controller
{
    
    public function indexAction()
    {
        $em = $this->getDoctrine()
                   ->getEntityManager();

        $geo = new Geolocation($em);
        $geo->geolocate("wales, uk");
        $geo->geolocate("abhfdhgsa, gdsag");
        
        return $this->render('GoogleGeolocationBundle:Default:index.html.twig');
    }
}
