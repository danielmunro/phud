<?php
namespace Phud\Commands\Arguments;
use Phud\Actors\Actor as aActor,
	Phud\Actors\Shopkeeper as aShopkeeper;

class Shopkeeper extends Argument
{
	protected function parseArg(aActor $actor, $arg)
	{
		$target = $actor->getRoom()->getActorByInput($arg);
		if($target instanceof aShopkeeper) {
			return $target;
		}
		$this->status = self::STATUS_INVALID;
		$actor->notify("No one is there.");
	}
}
