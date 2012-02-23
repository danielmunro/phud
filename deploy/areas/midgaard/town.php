<?php
use \Mechanics\Room,
	\Items\Item,
	\Mechanics\Actor,
	\Mechanics\Affect,
	\Mechanics\Attributes,
	\Items\Equipment,
	\Living\Trainer,
	\Living\Shopkeeper,
	\Items\Food,
	\Items\Drink,
	\Items\Container,
	\Items\Armor;

new Room([
	'id' => 13,
	'title' => 'West Gate of Midgaard',
	'description' => 'A stone gate to the west marks the end of Midgaard. Cobblestone paths lead in all directions.',
	'area' => 'midgaard',
	'east' => 6,
	'west' => 23
]);

new Room([
	'id' => 14,
	'title' => 'East Gate of Midgaard',
	'description' => 'A stone gate to the east marks the end of Midgaard. Cobblestone paths lead in all directions.',
	'area' => 'midgaard',
	'west' => 9
]);

new Room([
	'id' => 18,
	'title' => 'Common Square',
	'description' => '',
	'area' => 'midgaard',
	'north' => 3,
	'west' => 19,
	'east' => 21
]);

$p = [
	'title' => 'A Cobblestone Street',
	'description' => 'A cobblestone path travels in a east-west direction with shops lining both sides.',
	'area' => 'midgaard'
];

new Room(array_merge(['id' => 19, 'east' => 18, 'west' => 20, 'north' => 30, 'south' => 31], $p));
new Room(array_merge(['id' => 20, 'east' => 19], $p));
new Room(array_merge(['id' => 21, 'east' => 22, 'west' => 18, 'south' => 28, 'north' => 29], $p));
new Room(array_merge(['id' => 22, 'west' => 21], $p));

new Room([
	'id' => 28,
	'title' => 'Entrance to the Thieves Guild',
	'description' => '',
	'area' => 'midgaard',
	'north' => 21
]);

new Room([
	'id' => 29,
	'title' => 'Tavern',
	'area' => 'midgaard',
	'south' => 21,
	'actors' => [
		new Shopkeeper([
			'items' => [
				new Drink([
					'short' => 'a firebreather',
					'long' => 'A firebreather is here.',
					'nouns' => 'firebreather',
					'contents' => 'cocktail',
					'uses' => 1
				])
			]
		])
	]
]);

new Room([
	'id' => 30,
	'title' => 'Map shop',
	'area' => 'midgaard',
	'south' => 19
]);

new Room([
	'id' => 31,
	'title' => 'Inn',
	'area' => 'midgaard',
	'north' => 19
]);

new Room([
	'id' => 23,
	'title' => 'Outside the West Gate of Midgaard',
	'description' => 'You are in a vast, open field, which gives way to an ominously dark forest in the west. To the east is a large gate to Midgaard.',
	'area' => 'outside midgaard',
	'east' => 13,
	'west' => 24
]);

new Room([
	'id' => 24,
	'title' => 'Path leading towards the woods',
	'description' => 'A meandering path leads from fields to the east into a dark forest to the west.',
	'area' => 'dark forest',
	'east' => 23,
	'west' => 'goblinforest0'
]);


Room::setStartRoom(1);

?>
