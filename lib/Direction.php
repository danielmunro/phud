<?php
namespace Phud;

class Direction
{
	const NORTH = 'north';
	const SOUTH = 'south';
	const EAST = 'east';
	const WEST = 'west';
	const UP = 'up';
	const DOWN = 'down';

	public static function getDirections()
	{
		return [
			self::NORTH,
			self::SOUTH,
			self::EAST,
			self::WEST,
			self::UP,
			self::DOWN
		];
	}

	public static function getFullAlias($dir)
	{
		foreach(self::getDirections() as $direction) {
			if(strpos($direction, $dir) === 0) {
				return $direction;
			}
		}
	}

	public static function getReverse($direction)
	{
		$directions = self::getDirections();
		$i = array_search($direction, $directions);
		if($i % 2 === 0) {
			return $directions[$i+1];
		} else {
			return $directions[$i-1];
		}
	}
}
?>
