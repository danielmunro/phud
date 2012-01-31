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
					if(gettype($value) !== gettype($this->$property)) {
						Debug::addDebugLine('Property ('.$property.') of '.$this.' has a type mismatch');
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
