<?php
use \Mechanics\Room,
	\Living\Mob;

$area_den = 'spiderden';
$id = 'goblinforestspiderweb';

$spider = [
	'alias' => 'a small green spider',
	'nouns' => 'green spider',
	'long' => 'An unintimidating little green spider scurries across your path.',
	'area' => $area_den
];

new Room([
	'id' => $id.'0',
	'title' => 'Climbing a spider web',
	'description' => 'A monstrous web has engulfed the trees around it. The main arterials are just strong enough to support your weight.',
	'area' => $area_den,
	'down' => 'goblinforest3',
	'actors' => [
		new Mob($spider)
	]
]);
?>
