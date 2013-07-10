<?php

namespace Google\GoogleGeolocationBundle\Model;


class BaseLocation
{
	public function incrementHits()
	{
		$this->setHits($this->getHits() + 1);
	}

	/**
	 * Get the address components for a Geocoded result. Geocoding service
	 * may return more than 1 match for a search. The $match param can
	 * be used to specify the result to return
	 *
	 * @param   int     $match      Result match to return (indexes start at 0)
	 * @return  array               Key/Value address components
	 */
	public function getAddressComponents($match = 0)
	{
		$matches = json_decode($this->getResult(), true);

		if (false === isset($matches[$match]))
		{
			return false;
		}

		$components = array();
		foreach ($matches[$match]['address_components'] as $component)
		{
			$type = $component['types'][0];
			$components[$type] = $component['long_name'];
		}

		return $components;
	}

	/**
	 * Get the latlng components for a Geocoded result. Geocoding service
	 * may return more than 1 match for a search. The $match param can
	 * be used to specify the result to return
	 *
	 * @param   int     $match      Result match to return (indexes start at 0)
	 * @return  array               Key/Value latlng component
	 */
	public function getLatLng($match = 0)
	{
		$matches = json_decode($this->getResult(), true);

		if (false === isset($matches[$match]))
		{
			return false;
		}

		$components = array();
		$components['lat'] = $matches[$match]['geometry']['location']['lat'];
		$components['lng'] = $matches[$match]['geometry']['location']['lng'];

		return $components;

	}

	/**
	 * Get result as array
	 *
	 * @return string
	 */
	public function getResultArray()
	{
		$results = array();
		if ($this->getMatches())
		{
			// Retrieve the result.
			$results = json_decode($this->getResult());
		}
		return $results;
	}
}