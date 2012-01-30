<?php
use \Mechanics\Room,
	\Living\Mob,
	\Mechanics\Item;

$id = 'goblinforest';
$area = 'goblinforest';

new Room([
	'id' => $id.'0',
	'title' => 'Entrance to the goblin forest',
	'description' => 'Trees begin to envelop you as you travel further into the woods.',
	'area' => 'goblin forest',
	'east' => 24,
	'west' => $id.'4',
	'south' => $id.'1',
	'items' => [
		new Item([
			'short' => 'a wooden sign',
			'long' => "A wooden sign is on the side of the path. It reads,\n".
						"\"Beware travellers, beyond this point is controlled by renegade goblins. Continue at your own risk.\"",
			'nouns' => 'wooden sign',
			'material' => Item::MATERIAL_WOOD,
			'can_own' => false
		])
	]
]);

new Room([
	'id' => $id.'1',
	'title' => 'The goblin forest',
	'description' => 'A small path leads you through a dark forest.',
	'area' => $area,
	'north' => $id.'0',
	'east' => $id.'2'
]);

new Room([
	'id' => $id.'2',
	'title' => 'The goblin forest',
	'description' => 'A small path leads you through a dark forest. Cobwebs line the trees and bushes.',
	'area' => $area,
	'west' => $id.'1',
	'east' => $id.'3'
]);

new Room([
	'id' => $id.'3',
	'title' => 'A large spider web in the goblin forest',
	'description' => 'A small path leads you through a dark forest. Cobwebs line the trees and bushes.',
	'area' => $area,
	'west' => $id.'2',
	'up' => 'goblinforestspiderweb0'

]);

new Room([
	'id' => $id.'4',
	'title' => 'The goblin forest',
	'description' => '',
	'area' => $area,
	'east' => $id.'0',
	'west' => $id.'5'
]);

new Room([
	'id' => $id.'5',
	'title' => 'Deeper in the goblin forest',
	'description' => 'Trees begin to block out light all around you.',
	'area' => $area,
	'east' => $id.'4'
]);

?>
