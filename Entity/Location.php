<?php

namespace Google\GeolocationBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="Google\GeolocationBundle\Repository\LocationRepository")
 * @ORM\Table(name="google_geolocation_location")
 * @ORM\HasLifecycleCallbacks()
 */
class Location
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\Column(type="string")
     */
    protected $search;

    /**
     * @ORM\Column(type="smallint")
     */
    protected $matches;

    /**
     * @ORM\Column(type="string", length=20)
     */
    protected $status;

    /**
     * @ORM\Column(type="text")
     */
    protected $result;

    /**
     * @ORM\Column(type="integer")
     */
    protected $hits;

    /**
     * @ORM\Column(type="datetime")
     */
    protected $created;

    /**
     * @ORM\Column(type="datetime")
     */
    protected $updated;

    public function __construct()
    {
        $this->setMatches(false);
        $this->setHits(0);
        $this->setCreated(new \DateTime());
        $this->setUpdated(new \DateTime());
    }

    /**
     * @ORM\preUpdate
     */
    public function setUpdatedValue()
    {
       $this->setUpdated(new \DateTime());
    }

    public function incrementHits()
    {
        $this->setHits($this->getHits() + 1);
    }

    /**
     * Get the address components for a Geocoded result. Geocoding service
     * may return more than 1 match for a search. The $match param can
     * be used to specify the result to return
     *
     * @param   int     $match      Result match to return
     * @return  array               Key/Value address components
     */
    public function getAddress($match = 1)
    {
        $matches = json_decode($this->getResult(), true);
        
        $components = array();
        if (isset($matches[$match]))
        {
            foreach ($matches[$match]['address_components'] as $component)
            {
                $type = $component['types'][0];
                $components[$type] = $component['long_name'];
            }
        }
        
        return $components;
    }
    
    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set search
     *
     * @param string $search
     */
    public function setSearch($search)
    {
        $this->search = $search;
    }

    /**
     * Get search
     *
     * @return string
     */
    public function getSearch()
    {
        return $this->search;
    }

    /**
     * Set matches
     *
     * @param boolean $matches
     */
    public function setMatches($matches)
    {
        $this->matches = $matches;
    }

    /**
     * Get matches
     *
     * @return boolean
     */
    public function getMatches()
    {
        return $this->matches;
    }

    /**
     * Set result
     *
     * @param string $result
     */
    public function setResult($result)
    {
        $this->result = $result;
    }

    /**
     * Get result
     *
     * @return string
     */
    public function getResult($raw = true)
    {
        return $this->result;
    }

    /**
     * Get result as array
     *
     * @return string
     */
    public function getResultArray()
    {
        $results = array();
        if ($location->getMatches())
        {
            // Retrieve the result.
            $results = json_decode($location->getResult());
        }
        return $result;
    }

    /**
     * Set hits
     *
     * @param integer $hits
     */
    public function setHits($hits)
    {
        $this->hits = $hits;
    }

    /**
     * Get hits
     *
     * @return integer
     */
    public function getHits()
    {
        return $this->hits;
    }

    /**
     * Set created
     *
     * @param datetime $created
     */
    public function setCreated($created)
    {
        $this->created = $created;
    }

    /**
     * Get created
     *
     * @return datetime
     */
    public function getCreated()
    {
        return $this->created;
    }

    /**
     * Set updated
     *
     * @param datetime $updated
     */
    public function setUpdated($updated)
    {
        $this->updated = $updated;
    }

    /**
     * Get updated
     *
     * @return datetime
     */
    public function getUpdated()
    {
        return $this->updated;
    }

    /**
     * Set status
     *
     * @param string $status
     */
    public function setStatus($status)
    {
        $this->status = $status;
    }

    /**
     * Get status
     *
     * @return string
     */
    public function getStatus()
    {
        return $this->status;
    }
}
