<?php
namespace Mechanics;
use \Exception;

trait EasyInit
{
	protected function initializeProperties($properties, $exceptions = [])
	{
		foreach($properties as $property => $value) {
			if(property_exists($this, $property)) {
				if(isset($exceptions[$property])) {
					$exceptions[$property]($this, $property, $value);
				} else {
					$t1 = gettype($value);
					$t2 = gettype($this->$property);
					if($t1 !== $t2) {
						Debug::addDebugLine('Property ('.$property.') of '.$this.' has a type mismatch, expecting ('.$t2.'), got ('.$t1.')');
					}
					$this->$property = $value;
				}
			} else {
				throw new Exception('Property of '.$this.' ('.$property.') does not exist');
			}
		}
	}
}
?>
