<?php
namespace Commands;
use \Mechanics\Server,
	\Mechanics\Alias,
	\Mechanics\Actor,
	\Mechanics\Race,
	\Mechanics\Command\DM,
	\Living\Shopkeeper as lShopkeeper,
	\Living\User as lUser;

class Shopkeeper extends DM
{

	protected function __construct()
	{
		self::addAlias('shopkeeper', $this);
	}

	public function perform(lUser $user, $args = array())
	{
		if(!$this->hasArgCount($user, $args, 2))
			return;
	
		$command_fn = $this->getCommand($args[2]);
		$shopkeeper = $user->getRoom()->getActorByInput($args[1]);
		$value = implode(' ', array_slice($args, 3));
		
		if($command_fn && $shopkeeper instanceof lShopkeeper)
			return $this->$command_fn($user, $shopkeeper, $value, $args);
		
		if(!($shopkeeper instanceof lShopkeeper))
			return Server::out($user, "You can't find them.");
		
		if(!$command_fn)
			return Server::out($user, "What are you trying to do.");
	}
	
	private function doRace(lUser $user, lShopkeeper $shopkeeper, $race, $args)
	{
		$race = Alias::lookup($race);
		if(!($race instanceof Race))
			return Server::out($user, "That is not a valid race.");
		$shopkeeper->setRace($race);
		$shopkeeper->getRoom()->announce($shopkeeper, $shopkeeper->getAlias(true)." spontaneously shapeshifts into a ".$race->getAlias().".");
	}
	
	private function doLong(lUser $user, lShopkeeper $shopkeeper, $long, $args)
	{
		$shopkeeper->setLong($long);
		Server::out($user, $shopkeeper->getAlias(true)."'s description now reads: ".$shopkeeper->getLong());
	}
	
	private function doLevel(lUser $user, lShopkeeper $shopkeeper, $levels, $args)
	{
		if(!is_numeric($levels))
			return Server::out($user, "Number of levels granted must be a number.");
		$shopkeeper->setLevel($levels);
		return Server::out($user, "You grant ".$shopkeeper->getAlias()." ".$levels." level".($levels==1?'':'s'));
	}
	
	private function doInformation(lUser $user, lShopkeeper $shopkeeper, $null, $args)
	{
		$sexes = [Actor::SEX_MALE=>'male',Actor::SEX_FEMALE=>'female',Actor::SEX_NEUTRAL=>'it'];
		Server::out($user,
				"info page on shopkeeper:\n".
				"alias:                    ".$shopkeeper->getAlias()."\n".
				"race:                     ".$shopkeeper->getRace()['lookup']."\n".
				"level:                    ".$shopkeeper->getLevel()."\n".
				"nouns:                    ".$shopkeeper->getNouns()."\n".
				"max worth:                ".$shopkeeper->getGold().'g '.$shopkeeper->getSilver().'s '.$shopkeeper->getCopper()."c\n".
				"movement pulses:          ".$shopkeeper->getMovementPulses()."\n".
				"unique:                   ".($shopkeeper->isUnique()?'yes':'no')."\n".
				"sex:                      ".$shopkeeper->getDisplaySex($sexes)."\n".
				"start room:               ".$shopkeeper->getStartRoom()->getTitle()." (#".$shopkeeper->getStartRoom()->getId().")\n".
				"area:                     ".$shopkeeper->getArea()."\n".
				"long:\n".
				($shopkeeper->getLong() ? $shopkeeper->getLong() : "Nothing."));
	}
	
	private function doGold(lUser $user, lShopkeeper $shopkeeper, $gold, $args)
	{
		$this->doWorth($user, $shopkeeper, $gold, $args, 'gold');
	}
	
	private function doSilver(lUser $user, lShopkeeper $shopkeeper, $gold, $args)
	{
		$this->doWorth($user, $shopkeeper, $gold, $args, 'silver');
	}
	
	private function doCopper(lUser $user, lShopkeeper $shopkeeper, $gold, $args)
	{
		$this->doWorth($user, $shopkeeper, $copper, $args, 'copper');
	}
	
	private function doWorth(lUser $user, $shopkeeper, $amount, $args, $type)
	{
		if(!is_numeric($amount) || $amount < 0 || $amount > 99999)
			return Server::out($user, "Invalid amount of ".$type." to give ".$shopkeeper->getAlias().".");
		
		$fn = 'set'.ucfirst($type).'Repop';
		$shopkeeper->$fn($amount);
		$fn = 'set'.ucfirst($type);
		$shopkeeper->$fn($amount);
		Server::out($user, "You set ".$shopkeeper->getAlias()."'s ".$type." amount to ".$amount.".");
	}
	
	private function doSex(lUser $user, lShopkeeper $shopkeeper, $sex, $args)
	{
		if(strpos('male', $sex) === 0) {
			$shopkeeper->setSex(Actor::SEX_MALE);
			return Server::out($user, $shopkeeper->getAlias(true)." is now a ".strtoupper($shopkeeper->getDisplaySex()).".");
		}
		if(strpos('female', $sex) === 0) {
			$shopkeeper->setSex(Actor::SEX_FEMALE);
			return Server::out($user, $shopkeeper->getAlias(true)." is now a ".strtoupper($shopkeeper->getDisplaySex()).".");
		}
		if(strpos('neutral', $sex) === 0) {
			$shopkeeper->setSex(Actor::SEX_NEUTRAL);
			return Server::out($user, $shopkeeper->getAlias(true)." is now a ".strtoupper($shopkeeper->getDisplaySex()).".");
		}
		Server::out($user, "What?");
	}
	
	private function doMovement(lUser $user, lShopkeeper $shopkeeper, $movement, $args)
	{
		if(!is_numeric($movement))
			return Server::out($user, "What movement speed?");
		$shopkeeper->setMovementPulses($movement);
		Server::out($user, $shopkeeper->getAlias()."'s movement speed set to ".$movement." pulses.");
	}
	
	private function doArea(lUser $user, lShopkeeper $shopkeeper, $area, $args)
	{
		$shopkeeper->setArea($area);
		Server::out($user, $shopkeeper->getAlias(true)."'s area is now set to ".$area.".");
	}
	
	private function doAlias(lUser $user, lShopkeeper $shopkeeper, $alias, $args)
	{
		$old_alias = $shopkeeper->getAlias(true);
		$shopkeeper->setAlias($alias);
		Server::out($user, $old_alias." has been renamed to ".$shopkeeper->getAlias().".");
	}
	
	private function doDelete(lUser $user, lShopkeeper $shopkeeper, $null, $args)
	{
		$shopkeeper->delete();
		$user->getRoom()->announce($shopkeeper, $shopkeeper->getAlias(true)." poofs out of existence.");
	}
	
	private function doNouns(lUser $user, lShopkeeper $shopkeeper, $nouns, $args)
	{
		$shopkeeper->setNouns($nouns);
		Server::out($user, $shopkeeper->getAlias(true)."'s nouns are now: ".$shopkeeper->getNouns());
	}
	
	private function getCommand($arg)
	{
		$commands = array('race', 'delete', 'alias', 'nouns', 'level', 'information', 'long', 'gold', 'silver', 'copper', 'movement', 'sex', 'area');
		$command = array_filter($commands, function($c) use ($arg) 
			{
				return strpos($c, $arg) === 0;
			});
		if(sizeof($command))
			return 'do'.ucfirst(str_replace(' ', '', array_shift($command)));
		return false;
	}
}
?>
