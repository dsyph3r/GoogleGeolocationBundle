<?php

namespace Google\GeolocationBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="Google\GeolocationBundle\Repository\ApiLog")
 * @ORM\Table(name="google_geolocation_api_log")
 * @ORM\HasLifecycleCallbacks()
 */
class ApiLog
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\Column(type="string", length=20)
     */
    protected $lastStatus;

    /**
     * @ORM\Column(type="integer")
     */
    protected $requests;

    /**
     * @ORM\Column(type="date", unique=true)
     */
    protected $created;

    /**
     * @ORM\Column(type="datetime")
     */
    protected $updated;

    public function __construct()
    {
        $this->setRequests(0);
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

    public function incrementRequests()
    {
        $this->setRequests($this->getRequests() + 1);
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
     * Set lastStatus
     *
     * @param string $lastStatus
     */
    public function setLastStatus($lastStatus)
    {
        $this->lastStatus = $lastStatus;
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
