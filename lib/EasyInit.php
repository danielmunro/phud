<?php
namespace Phud;
use \Exception;

trait EasyInit
{
	protected $initializing_properties = [];

	protected function initializeProperties($properties, $exceptions = [])
	{
		$this->initializing_properties = $properties;
		foreach($properties as $property => $value) {
			if(isset($exceptions[$property])) {
				$exceptions[$property]($this, $property, $value);
			} else if(property_exists($this, $property)) {
				$t1 = gettype($value);
				$t2 = gettype($this->$property);
				$this->checkForFaultyValue($property, $t1, $t2, $value);
				$this->$property = $value;
			} else if($property === 'apply') {
				$this->apply($value);
			} else {
				throw new Exception('Property of '.$this.' ('.$property.') does not exist');
			}
		}
	}

	protected function checkForFaultyValue($property, $t1, $t2, &$value)
	{
		// Exact type match
		if($t1 === $t2) {
			return;
		}

		// Int => string, ok
		if($t1 === 'integer' && $t2 === 'string') {
			return;
		}

		// Obj => null, ok
		if($t1 === 'object' && $t2 === 'NULL') {
			return;
		}

		if($t1 === 'string' && $t2 === 'integer') {
			if(intval($t1) == $t1) {
				$value = intval($value);
				return;
			}
		}

		if($t1 === 'integer' && $t2 === 'double') {
			$value = floatval($value);
			return;
		}
		Debug::log('Property ('.$property.') of '.$this.' has a type mismatch, expecting ('.$t2.'), got ('.$t1.')');
	}

	public function getInitializingProperties()
	{
		return $this->initializing_properties;
	}
}
?>
