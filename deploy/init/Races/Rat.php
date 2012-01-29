<?php
namespace Races;
use \Mechanics\Alias;
use \Mechanics\Attributes;
class Rat extends \Mechanics\Race
{
	protected $alias = 'rat';

	protected function __construct()
	{
		$this->attributes = new Attributes();
		
		parent::__construct();
	}

	public function getSubscribers()
	{
		return [
		];
	}

	public function getAbilities()
	{
		return [
		];
	}
}
?>
