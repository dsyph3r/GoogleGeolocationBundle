<?php

namespace Google\GoogleGeolocationBundle\Document;

use Doctrine\ODM\MongoDB\DocumentRepository;

class LocationRepository extends DocumentRepository
{
	public function getCachedAddress($search)
	{
		$qb = $this->createQueryBuilder('GoogleGeolocationBundle:Location')
				->field('search')->equals($search);

		try {
			return $qb->getQuery()
			->getSingleResult();
		} catch (Doctrine\ODM\MongoDB\MongoDBException $e) {
			return null;
		}
	}

	public function cleanCache($expiresAt)
	{
		return $this->createQueryBuilder('GoogleGeolocationBundle:Location')
		->remove()
		->field('created')->lt($expiresAt)
		->getQuery()
		->execute();
	}

}