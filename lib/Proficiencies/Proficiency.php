<?php
namespace Phud\Proficiencies;

abstract class Proficiency
{
	protected static $name = '';
	protected static $attributes = [];
	protected static $base_improvement_chance = 0.01;
	protected static $proficiencies = [];
	protected $score = 15;

	abstract public function getImprovementListeners();

	public function checkImprove()
	{
	}

	public static function register($class)
	{
		self::$proficiencies[$class] = 'Phud\\Proficiencies\\'.$class;
	}

	public static function getProficiencies()
	{
		return self::$proficiencies;
	}

	public static function getName()
	{
		return self::$name;
	}
}
?>
