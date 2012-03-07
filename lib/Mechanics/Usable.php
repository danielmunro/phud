<?php
namespace Mechanics;

trait Usable
{
	public function getUsableByInput($collection, $input)
	{
		return $this->getUsables($collection, $input, true);
	}

	public function getManyUsablesByInput($collection, $input)
	{
		return $this->getUsables($collection, $input, false);
	}

	protected function getUsables($collection, $input, $scalar)
	{
		$found = [];
		foreach($collection as $c) {
			foreach($this->getUsableProperty($c) as $u) {
				if(stripos($u, $input) === 0) {
					if($scalar) {
						return $c;
					} else {
						$found[] = $c;
					}
				}
			}
		}
		if($scalar && !$found) {
			return false;
		}
		return $found;
	}

	protected function getUsableProperty($usable)
	{
		return property_exists($usable, 'nouns') ? explode(' ', $usable->getNouns()) : [$usable->getAlias()];
	}
}
?>
