<?php

namespace Google\GoogleGeolocationBundle\Document;

use Doctrine\ODM\MongoDB\Mapping\Annotations as MongoDB;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * @MongoDB\Document(
 * 					collection="google_geolocation_api_log",
 * 					repositoryClass="Webinfopro\Bundle\GoogleGeolocationBundle\Document\ApiLogRepository")
 */
class ApiLog
{
	/**
	 * @MongoDB\Id
	 */
	protected $id;

    /**
	 * @MongoDB\String
     */
    protected $lastStatus;

    /**
	 * @MongoDB\Int
     */
    protected $requests;

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
        $this->setRequests(0);
    }

    public function incrementRequests()
    {
        $this->setRequests($this->getRequests() + 1);
    }

    public function getId()
    {
        return $this->id;
    }

    /**
     * Set lastStatus
     *
     * @param string $lastStatus
     */
    public function setLastStatus($lastStatus)
    {
        $this->lastStatus = $lastStatus;
        return $this;
    }

    /**
     * Get lastStatus
     *
     * @return string
     */
    public function getLastStatus()
    {
        return $this->lastStatus;
    }

    /**
     * Set requests
     *
     * @param integer $requests
     */
    public function setRequests($requests)
    {
        $this->requests = $requests;
        return $this;
    }

    /**
     * Get requests
     *
     * @return integer
     */
    public function getRequests()
    {
        return $this->requests;
    }

    /**
     * Set created
     *
     * @param date $created
     */
    public function setCreated($created)
    {
        $this->created = $created;
        return $this;
    }

    /**
     * Get created
     *
     * @return date
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
}
