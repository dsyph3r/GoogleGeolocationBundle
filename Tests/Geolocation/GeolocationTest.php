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
        $geolocationApiMock = $this->getGeolocationApiMock();

        $geolocationApiMock->expects($this->any())
            ->method('request')
            ->will($this->returnValue($this->getSuccessfulResultRequest()));

        $location = $geolocationApiMock->locateAddress("Wales, UK");
        $this->assertEquals(1, $location->getMatches());

        // Check the cache was hit this time
        $hits = $location->getHits();
        $location = $geolocationApiMock->locateAddress("Wales, UK");
        $this->assertEquals($hits + 1, $location->getHits());
    }

    public function testNoMatchesGeolocation()
    {
        $geolocationApiMock = $this->getGeolocationApiMock();

        $geolocationApiMock->expects($this->any())
            ->method('request')
            ->will($this->returnValue($this->getFailedResultRequest()));

        $location = $geolocationApiMock->locateAddress("adsdas,fdsfsdf,fsdf");
        $this->assertEquals(0, $location->getMatches());
    }

    public function testSuccesfulGeolocationNoCache()
    {
        $geolocationApiMock = $this->getGeolocationApiMock(false);

        $geolocationApiMock->expects($this->once())
            ->method('request')
            ->will($this->returnValue($this->getSuccessfulResultRequest()));

        $location = $geolocationApiMock->locateAddress("Cardiff, Wales");
        $this->assertEquals(1, $location->getMatches());

    }

    protected function getGeolocationApiMock($cacheAvailable = true)
    {
        $mock = $this->getMock('Google\GeolocationBundle\Geolocation\GeolocationApi',
            array('request')
        );

        if (true === $cacheAvailable)
        {
            $mock->setEntityManager($this->em);
            $mock->setDailyLimit(20);
        }

        return $mock;
    }

    protected function getSuccessfulResultRequest()
    {
        return array(
            'curl_info' => array(),
            'headers'   => array(),
            'data'      => json_encode(array(
                'status'    => 'OK',
                'results'   => array(
                    'cardiff, wales, uk'
                )
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
