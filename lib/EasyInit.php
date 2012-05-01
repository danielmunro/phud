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
				if($t1 !== $t2 && ($t1 !== 'integer' && $t2 !== 'string')) {
					Debug::log('Property ('.$property.') of '.$this.' has a type mismatch, expecting ('.$t2.'), got ('.$t1.')');
				}
				$this->$property = $value;
			} else if($property === 'apply') {
				$this->apply($value);
			} else {
				throw new Exception('Property of '.$this.' ('.$property.') does not exist');
			}
		}
	}

	public function getInitializingProperties()
	{
		return $this->initializing_properties;
	}
}
?>
