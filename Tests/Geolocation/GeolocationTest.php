<?php

namespace Google\GeolocationBundle\Tests\Geolocation;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

/**
 * GeolocationTest
 *
 * Test for Geolocation
 */
class GeolocationTest extends WebTestCase
{
    /**
     * @var \Doctrine\ORM\EntityManager
     */
    private $em;

    public function setUp()
    {
        $kernel = static::createKernel();
        $kernel->boot();
        $this->em = $kernel->getContainer()
                           ->get('doctrine.orm.entity_manager');
    }
    
    public function testSuccesfulGeolocation()
    {
        $geolocationMock = $this->getGeolocationMock();

        $geolocationMock->expects($this->once())
            ->method('request')
            ->will($this->returnValue($this->getSuccessfulResultRequest()));

        $location = $geolocationMock->geolocate("Wales, UK");
        $this->assertEquals(true, $location->getMatches());
        
        // Check the cache was hit this time
        $location = $geolocationMock->geolocate("Wales, UK");
        $this->assertEquals(1, $location->getHits());
    }
    
    public function testNoMatchesGeolocation()
    {
        $geolocationMock = $this->getGeolocationMock();

        $geolocationMock->expects($this->once())
            ->method('request')
            ->will($this->returnValue($this->getFailedResultRequest()));

        $location = $geolocationMock->geolocate("adsdas,fdsfsdf,fsdf");
        $this->assertEquals(false, $location->getMatches());
    }

    protected function getGeolocationMock()
    {
        return $this->getMock('Google\GeolocationBundle\Geolocation\Geolocation', array('request'), array($this->em));
    }

    protected function getSuccessfulResultRequest()
    {
        return array(
            'curl_info' => array(),
            'headers'   => array(),
            'data'      => json_encode(array(
                'status'    => 'OK',
                'results'   => array()
            ))
        );
    }
    
    protected function getFailedResultRequest()
    {
        return array(
            'curl_info' => array(),
            'headers'   => array(),
            'data'      => json_encode(array(
                'status'    => 'ZERO_RESULTS',
                'results'   => array()
            ))
        );
    }
    
    protected function getLimitedResultRequest()
    {
        return array(
            'curl_info' => array(),
            'headers'   => array(),
            'data'      => json_encode(array(
                'status'    => 'OVER_QUERY_LIMIT',
                'results'   => array()
            ))
        );
    }

}
