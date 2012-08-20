<?php
namespace Phud\Commands;
use Phud\Actors\Actor,
	Phud\Actors\User as aUser,
	Phud\Actors\Shopkeeper as aShopkeeper,
	\InvalidArgumentException;

class CList extends User
{
	protected $alias = 'list';
	protected $dispositions = [
		Actor::DISPOSITION_STANDING,
		Actor::DISPOSITION_SITTING
	];

	public function perform(aUser $user, aShopkeeper $shopkeeper)
	{
		$user->notify($shopkeeper->getListItemMessage()."\r\n".$shopkeeper->displayContents(true));
	}

	protected function getArgumentsFromHints(Actor $actor, $args)
	{
		return [(new Arguments\Shopkeeper())->parse($actor, sizeof($args) === 3 ? $args[2] : null)];
	}
}
