<?php

namespace Google\GeolocationBundle\Geolocation;

use Google\GeolocationBundle\Entity\Location;
use Network\Curl\Curl;

class Geolocation
{
    protected $entityManager;
    
    public function __construct(\Doctrine\ORM\EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;
    }
    
    public function geolocate($search)
    {
        // Check the cache first
        $location = $this->entityManager
                         ->getRepository('GoogleGeolocationBundle:Location')
                         ->getCachedLocation($search);
                         
        if (true === is_null($location))
        {
            // No cache, Use Google Geolocation API
            $curl = new Curl();
            $response = $curl->get('http://maps.googleapis.com/maps/api/geocode/json?address=' . urlencode($search) . '&sensor=false');
            
            $status     = $response['status'];
            $headers    = $response['headers'];
            $data       = json_decode($response['data'], true);
        
            $geolocationStatus = $data['status'];
            var_dump($response);
            
            $location = new Location();
            $location->setSearch($search);
            $location->setResult(json_encode($data['results']));
            
            // TODO: We need to handle OVER_QUERY_LIMIT status aswell here
            if ('OK' === $geolocationStatus)
                $location->setMatches(true);
            else
                $location->setMatches(false);
            
            $this->entityManager->persist($location);
        }
        else
        {
            // We have a hit
            $location->incrementHits();
            
            $this->entityManager->persist($location);
        }
        $this->entityManager->flush();
        
        $results = array();
        if ($location->getMatches())
        {
            // Retrieve the result.
            $results = json_decode($location->getResult());
        }
        
        return $results;
    }
}