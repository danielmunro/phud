<?php
namespace Mechanics;

trait Usable
{
	public function getUsableByInput($collection, $input)
	{
		foreach($collection as $c) {
			foreach($this->getUsables($c) as $u) {
				if(stripos($u, $input) === 0) {
					return $c;
				}
			}
		}
		return false;
	}

	protected function getUsables($usable)
	{
		return property_exists($usable, 'nouns') ? explode(' ', $usable->getNouns()) : [$usable->getAlias()];
	}
}
?>
