<?php
namespace Mechanics;
use \Exception;

trait EasyInit
{
	protected function initializeProperties($properties, $exceptions = [])
	{
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
		if(property_exists($this, 'nouns') && !$this->nouns) {
			$this->assignNouns();
		}
	}
}
?>
