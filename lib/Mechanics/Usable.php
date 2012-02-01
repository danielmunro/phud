<?php
namespace Mechanics;

trait Usable
{
	public function getUsableByInput($collection, $input)
	{
		foreach($collection as $c) {
			foreach($c->getUsables() as $u) {
				if(stripos($u, $input) === 0) {
					return $c;
				}
			}
		}
		return false;
	}

	public function getUsables()
	{
		return property_exists($this, 'nouns') ? explode(' ', $this->nouns) : [$this->alias];
	}
}
?>
