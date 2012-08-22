<?php
namespace Phud\Commands\Arguments;
use Phud\Actors\Shopkeeper as aShopkeeper;

class Shopkeeper extends Argument
{
	protected $search_in = null;

	public function __construct($search_in)
	{
		$this->search_in = $search_in;
	}

	public function parse($arg = null)
	{
		if($arg === null) {
			foreach($this->search_in->getRoom()->getActors() as $_actor) {
				if($_actor instanceof aShopkeeper) {
					return $_actor;
				}
			}
		} else {
			$target = $this->search_in->getRoom()->getActorByInput($arg);
			if($target instanceof aShopkeeper) {
				return $target;
			}
		}
		$this->fail("No one is there.");
	}
}
