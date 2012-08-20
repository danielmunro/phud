<?php
namespace Phud\Commands\Arguments;
use Phud\Actors\Actor as aActor,
	Phud\Actors\Shopkeeper as aShopkeeper;

class Shopkeeper extends Argument
{
	protected function parseArg(aActor $actor, $arg = null)
	{
		if($arg === null) {
			foreach($actor->getRoom()->getActors() as $_actor) {
				if($_actor instanceof aShopkeeper) {
					return $_actor;
				}
			}
		} else {
			$target = $actor->getRoom()->getActorByInput($arg);
			if($target instanceof aShopkeeper) {
				return $target;
			}
		}
		$this->fail($actor, "No one is there.");
	}
}
