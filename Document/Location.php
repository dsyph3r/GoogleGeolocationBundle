<?php

namespace Google\GoogleGeolocationBundle\Document;

use Webinfopro\Bundle\GoogleGeolocationBundle\Model\BaseLocation;

use Doctrine\ODM\MongoDB\Mapping\Annotations as MongoDB;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * @MongoDB\Document(
 * 					collection="google_geolocation_location",
 * 					repositoryClass="Webinfopro\Bundle\GoogleGeolocationBundle\Document\LocationRepository")
 */
class Location extends BaseLocation
{
	/**
	 * @MongoDB\Id
	 */
    protected $id;

    /**
	 * @MongoDB\String
     */
    protected $search;

    /**
	 * @MongoDB\Int
     */
    protected $matches;

    /**
	 * @MongoDB\String
     */
    protected $status;

    /**
	 * @MongoDB\String
     */
    protected $result;

    /**
	 * @MongoDB\Int
     */
    protected $hits;

	/**
	 * @var datetime $created
	 *
	 * @MongoDb\Timestamp
	 * @Gedmo\Timestampable(on="create")
	 */
    protected $created;

	/**
	 * @var datetime $updated
	 *
	 * @MongoDb\Timestamp
	 * @Gedmo\Timestampable()
	 */
    protected $updated;

    public function __construct()
    {
        $this->setMatches(false);
        $this->setHits(0);
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
        return $this;
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
        return $this;
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
        return $this;
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
     * Set hits
     *
     * @param integer $hits
     */
    public function setHits($hits)
    {
        $this->hits = $hits;
        return $this;
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
        return $this;
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
        return $this;
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
        return $this;
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
