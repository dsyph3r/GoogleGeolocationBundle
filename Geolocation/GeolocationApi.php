<?php

namespace Google\GeolocationBundle\Geolocation;

use Google\GeolocationBundle\Entity\Location;
use Google\GeolocationBundle\Entity\ApiLog;
use Buzz\Browser;

/**
 * GeolocationApi
 *
 * Provides a simple wrapper around Google Geocoding API.
 * Results can be cached locally to reduce requests made to service, and
 * daily limiting of API requests can be set. The Doctrine 2 EntityManager
 * must be set to use the caching and limiting functionlity
 *
 * Only supports address geolocation at present, reverse geolocation not supported.
 *
 * @link http://code.google.com/apis/maps/documentation/geocoding/
 */
class GeolocationApi
{

    /**
     * Specifies cache availability
     *
     * @var bool
     */
    private $cacheAvailable;

    /**
     * Doctrine Entity Manager. Required for caching and API request limiting
     *
     * @var \Doctrine\ORM\EntityManager
     */
    protected $em;

    /**
     * Max daily request to Geocoding API. Only available if
     * EntityManager is provided via setEntityManager()
     *
     * @var int
     */
    protected $dailyLimit;
    
    /**
     * Network layer
     *
     * @var Buzz\Browser
     */
    protected $browser;

    /**
     * Lifetime of Geocoded results in cache in hours. Only available if
     * EntityManager is provided via setEntityManager()
     *
     * @var int
     */
    protected $cacheLifetime;

    public function __construct(Browser $browser = null)
    {
        $this->browser = $browser ?: new Browser();
        
        $this->em               = null;
        $this->dailyLimit       = 0;            // No restriction
        $this->cacheLifetime    = 0;

        $this->cacheAvailable = false;
    }

    /**
     * Set the Entity Manager. This enables the availability of the cache
     * and API limiting
     *
     * @param \Doctrine\ORM\EntityManager   $em     EntityManager
     */
    public function setEntityManager(\Doctrine\ORM\EntityManager $em = null)
    {
        $this->em = $em;
        // Cache become available now
        $this->setCacheEnabled();
    }

    /**
     * Set daily limit for API. The Entity Manager must be set first with
     * setEntityManager()
     *
     * @param   int     $dailyLimit         The daily limit
     */
    public function setDailyLimit($dailyLimit)
    {
        $this->dailyLimit = $dailyLimit;
    }

    /**
     * Set the cache lifetime.The Entity Manager must be set first with
     * setEntityManager()
     *
     * @param   int     $cacheLifetime      Duration Geocoded result will be cached for
     */
    public function setCacheLifetime($cacheLifetime)
    {
        $this->cacheLifetime = $cacheLifetime;
    }

    /**
     * Set Network layer
     *
     * @param Browser   $browser        Browser
     */
    public function setBrowser(Browser $browser)
    {
        $this->browser = $browser;
    }
    
    /**
     * Enable the cache
     */
    public function setCacheEnabled()
    {
        if (true === is_null($this->em))
        {
            throw new \Exception("Cannot enable cache. EntityManager must be set via setEntityManager()");
        }
        
        $this->cacheAvailable = true;
    }
    
    /**
     * Disable the cache
     */
    public function setCacheDisabled()
    {
        $this->cacheAvailable = false;
    }
    
    public function locateAddress($search)
    {
        $location = null;
        if ($this->cacheAvailable)
        {
            // Check the cache first
            $location = $this->em
                             ->getRepository('GoogleGeolocationBundle:Location')
                             ->getCachedAddress($search);
        }

        if (true === is_null($location))
        {
            // No cache, Use Google Geolocation API
            $location = new Location();
            $location->setSearch($search);

            $location = $this->geolocate($location);
        }
        else
        {
            // Check the status, if last request status was OVER_QUERY_LIMIT
            // and now is a different day to last attempt, we need to query API
            // again
            if ('OVER_QUERY_LIMIT' === $location->getStatus() &&
                $location->isRequestAgainAllowed())
            {
                $location = $this->geolocate($location);
            }
            else
            {
                // We have a hit
                $location->incrementHits();
            }
        }

        // Only persist if cache is available
        if ($this->cacheAvailable)
        {
            $this->em->persist($location);
            $this->em->flush();
        }

        return $location;
    }

    /**
     * Clean the cache of expired entries
     *
     * @return int              The number of cleaned entries
     */
    public function cleanCache()
    {
        if (false === $this->cacheAvailable)
        {
            throw new \Exception("Unable to clean cache. There is no cache available");
        }

        $expiresAt = date("Y-m-d H:i:s", mktime(date("H")-$this->cacheLifetime));
        // Clean cache
        return $this->em
                    ->getRepository('GoogleGeolocationBundle:Location')
                    ->cleanCache($expiresAt);
    }

    /**
     * Geolocate and populate Location entity with result
     *
     * @param   Google\GeolocationBundle\Entity\Location    $location
     * @return  Google\GeolocationBundle\Entity\Location
     */
    protected function geolocate(\Google\GeolocationBundle\Entity\Location $location)
    {
        // Check limiting
        if ($this->apiAttemptsAllowed())
        {
            $response   = $this->request($location->getSearch());
            $data       = json_decode($response->getContent(), true);

            if (true === $this->cacheAvailable)
            {
                // Log the result
                $this->logResponse($data);
            }

            $status     = $data['status'];
            $result     = $data['results'];
        }
        else
        {
            $status     = 'REQUEST_NOT_ALLOWED';
            $result     = array();
        }

        $location->setResult(json_encode($result));
        $location->setStatus($status);

        // Check if matches were found for $search
        if ('OK' === $location->getStatus())
            $location->setMatches(count($result));
        else
            $location->setMatches(0);

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
        return $this->browser->get('http://maps.googleapis.com/maps/api/geocode/json?' .
            http_build_query(
                array('address' => $search, 'sensor' => 'false')
            )
        );
    }

    /**
     * Check if API attempts can be made based on:
     *   1. We haven't exceed the daily limit set at google_geolocation.geolocation_api.daily_limit
     *   2. The last request to API for today didn't return with status OVER_QUERY_LIMIT
     *
     * @retrun bool         Returns TRUE is requests can be made to API
     */
    protected function apiAttemptsAllowed()
    {
        // We can only limit if there is a cache available
        if (false === $this->cacheAvailable)
        {
            return true;
        }

        // Check last request status
        $apiLog = $this->em
                       ->getRepository('GoogleGeolocationBundle:ApiLog')
                       ->getLogForDate();

        if (true === is_null($apiLog))
        {
            // No history for today, we are OK to proceed
            return true;
        }

        // Check daily limit and last status
        if ($apiLog->getRequests() <= $this->dailyLimit &&
            'OVER_QUERY_LIMIT' !== $apiLog->getLastStatus())
        {
            return true;
        }

        return false;
    }

    /**
     * Log the responses
     *
     * @param   array   $response                           Raw response from API
     * @return  Google\GeolocationBundle\Entity\ApiLog      The updated log entity
     */
    protected function logResponse($response)
    {
        // Get existing log for today
        $apiLog = $this->em
                       ->getRepository('GoogleGeolocationBundle:ApiLog')
                       ->getLogForDate();

        if (true === is_null($apiLog))
        {
            $apiLog = new ApiLog();
        }

        $apiLog->setLastStatus($response['status']);
        $apiLog->incrementRequests();

        $this->em->persist($apiLog);
        $this->em->flush();

        return $apiLog;
    }
}
