<?php

namespace Google\GeolocationBundle\Geolocation;

use Google\GeolocationBundle\Entity\Location;
use Network\Curl\Curl;

/**
 * Geolocation
 *
 * Provides a simple wrapper around Google Geolocation API.
 * Results are cached locally to reduce requests made to service.
 * Only supports address geolocation at present, reverse geolocation not supported.
 *
 * @link http://code.google.com/apis/maps/documentation/geocoding/
 */
class Geolocation
{
    protected $entityManager;
    
    public function __construct(\Doctrine\ORM\EntityManager $entityManager = null)
    {
        $this->entityManager = $entityManager;
    }
    
    public function geolocate($search)
    {
        // Check the cache first
        $location = $this->entityManager
                        ->getRepository('GoogleGeolocationBundle:Location')
                        ->getCachedAddress($search);
                         
        if (true === is_null($location))
        {
            // No cache, Use Google Geolocation API
            $response   = $this->request($search);
            $data       = json_decode($response['data'], true);
            
            $location = new Location();
            $location->setSearch($search);
            $location->setResult(json_encode($data['results']));
            
            // TODO: We need to handle OVER_QUERY_LIMIT status aswell here
            if ('OK' === $data['status'])
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
        
        return $location;
    }
    
    /**
     * Perform Geolocation request with Google Geolocation API.
     * Searches by address string
     *
     * @return  array           cURL response from web service
     */
    protected function request($search)
    {
        $curl = new Curl();
        $params = array('address' => $search, 'sensor' => 'false');
        return $curl->get('http://maps.googleapis.com/maps/api/geocode/json', $params);
    }
}