<?php

namespace Google\GoogleGeolocationBundle\Document;

use Doctrine\ODM\MongoDB\DocumentRepository;

class ApiLogRepository extends DocumentRepository
{
	public function getLogForDate(\DateTime $date = null)
	{
		if (true === is_null($date))
		{
			$date = new \DateTime();
		}

		$qb = $this->createQueryBuilder('GoogleGeolocationBundle:ApiLog')
		->field('created')->equals($date)
		;

		try {
			return $qb->getQuery()
			->getSingleResult();
		} catch (Doctrine\ODM\MongoDB\MongoDBException $e) {
			return null;
		}
	}

	public function cleanCache()
	{
		$from = new \DateTime('today');

		return $this->createQueryBuilder('GoogleGeolocationBundle:ApiLog')
		->remove()
		->field('created')->lt($from)
		->getQuery()
		->execute();
	}

}