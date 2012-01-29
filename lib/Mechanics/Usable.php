<?php
namespace Mechanics;

trait Usable
{
	public function getUsableByInput($usables, $input)
	{
		foreach($usables as $u) {
			if(strpos(strtolower($u), $input) === 0) {
				return $u;
			}
		}
	}

	public function getUsableNounByInput($usables, $input)
	{
		if(is_array($input)) {
			$input = $input[0];
		}
		foreach($usables as $u) {
			$nouns = explode(' ', $u->getNouns());
			foreach($nouns as $n) {
				if(strpos($n, $input) === 0) {
					return $u;
				}
			}
		}
		return false;
	}
}
?>
