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
				$this->checkForFaultyValue($property, $t1, $t2);
				$this->$property = $value;
			} else if($property === 'apply') {
				$this->apply($value);
			} else {
				throw new Exception('Property of '.$this.' ('.$property.') does not exist');
			}
		}
	}

	protected function checkForFaultyValue($property, $t1, $t2)
	{
		if($t1 !== $t2 && ($t1 !== 'integer' && $t2 !== 'string') && !($t1 === 'object' && $t2 === 'NULL')) {
			Debug::log('Property ('.$property.') of '.$this.' has a type mismatch, expecting ('.$t2.'), got ('.$t1.')');
		}
	}

	public function getInitializingProperties()
	{
		return $this->initializing_properties;
	}
}
?>
