<?php
namespace Races;
use Phud\Race,
	Phud\Attributes;

class Critter extends Race
{
	protected $alias = 'critter';
	protected $creation_points = 10;
	protected $movement_cost = 1;
	protected $move_verb = 'leaves';
	protected $unarmed_verb = 'punch';

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
