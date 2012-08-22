<?php
namespace Phud\Commands\Arguments;

class Primary extends Argument
{
	public function parse($arg)
	{
		/**
		$seeking = $actor->getRoom()->getActorByInput($arg);
		if(!$seeking) {
			$seeking = $actor->getRoom()->getItemByInput($arg) || $actor->getItemByInput($arg);
			if(!$seeking) {
				$this->fail("Nothing like that exists on heaven or earth.");
			}
		}
		return $seeking;
		*/
		return 
			$actor->getRoom()->getActorByInput($arg) ||
			$actor->getRoom()->getItemByInput($arg) ||
			$actor->getItemByInput($arg) ||
			$this->fail("Nothing like that exists on heaven or earth.");
	}
}
