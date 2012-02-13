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

/**
$p = [
	'value' => 50,
	'weight' => 2,
	'level' => 5,
	'material' => Item::MATERIAL_LEATHER,
	'attributes' => new Attributes([
		'ac_bash' => -5,
		'ac_slash' => -5,
		'ac_pierce' => -5
	])
];
new Room([
	'id' => 11,
	'title' => 'Midgaard Armory',
	'description' => 'A hallway is lined with implements of destruction on one side, and pieces of various types of armor on the other.',
	'area' => 'midgaard',
	'north' => 4,
	'actors' => [
		new Shopkeeper([
			'alias' => 'Halek',
			'nouns' => 'blacksmith halek',
			'race' => 'human',
			'long' => 'A tall and lean blacksmith stands before you.',
			'items' => [
				new Armor(array_merge($p, [
					'short' => 'ragged leather boots',
					'long' => 'Torn and ragged leather boots are here.',
					'nouns' => 'leather boots',
					'position' => Equipment::POSITION_FEET
				])),
				new Armor(array_merge($p, [
					'short' => 'a ragged leather helmet',
					'long' => 'A ragged leather helmet is here.',
					'nouns' => 'leather helmet',
					'position' => Equipment::POSITION_HEAD
				])),
				new Armor(array_merge($p, [
					'short' => 'ragged leather leggings',
					'long' => 'Ragged leather leggings are here.',
					'nouns' => 'leather leggings',
					'position' => Equipment::POSITION_LEGS
				])),
				new Armor(array_merge($p, [
					'short' => 'a ragged leather chest armor',
					'long' => 'Ragged leather chest armor is here.',
					'nouns' => 'leather chest armor',
					'position' => Equipment::POSITION_TORSO
				])),
				new Armor(array_merge($p, [
					'short' => 'a ragged leather belt',
					'long' => 'Ragged leather belt is here.',
					'nouns' => 'leather belt',
					'position' => Equipment::POSITION_WAIST
				]))
			]
		])
	]
]);
*/

new Room([
	'id' => 12,
	'title' => 'Midgaard General Store',
	'description' => 'You are standing in an empty room. On the far end is a small desk with a gnomish man behind it.',
	'area' => 'midgaard',
	'south' => 7,
	'actors' => [
		new Shopkeeper([
			'alias' => 'Mardwyn',
			'nouns' => 'mardwyn clerk',
			'long' => 'A small gnomish man fidgets before you.',
			'race' => 'elf',
			'sex' => Actor::SEX_MALE,
			'items' => [
				new Equipment([
					'short' => 'a wooden torch',
					'long' => 'A wooden torch is glowing lightly.',
					'nouns' => 'wooden torch',
					'position' => Equipment::POSITION_LIGHT,
					'material' => Item::MATERIAL_WOOD,
					'value' => 5,
					'affects' => [
						new Affect(['affect' => 'glow'])
					]
				]),
				new Drink([
					'short' => 'a small canteen',
					'long' => ' a small tin canteen is bound by leather. Looks like it can carry water.',
					'nouns' => 'canteen',
					'material' => Item::MATERIAL_TIN,
					'value' => 10,
					'amount' => 10
				]),
				new Container([
					'short' => 'a leather satchel',
					'long' => 'A leather satchel looks perfect for carrying items.',
					'nouns' => 'leather satchel',
					'material' => Item::MATERIAL_LEATHER,
					'value' => 15
				])
			]
		])
	]
]);

new Room([
	'id' => 15,
	'title' => 'Midgaard Alchemy Shop',
	'description' => '',
	'area' => 'midgaard',
	'south' => 9,
	'actors' => [
		new Shopkeeper([
		])
	]
]);

new Room([
	'id' => 16,
	'title' => 'Midgaard Bank',
	'description' => '',
	'area' => 'midgaard',
	'north' => 7,
	'actors' => [
		new Shopkeeper([
		])
	]
]);

new Room([
	'id' => 17,
	'title' => 'Midgaard Wand Shop',
	'description' => '',
	'area' => 'midgaard',
	'south' => 6,
	'actors' => [
		new Shopkeeper([
		])
	]
]);

new Room([
	'id' => 26,
	'title' => "Entrance to the Mage's Guild",
	'description' => '',
	'area' => 'midgaard',
	'north' => 6
]);

new Room([
	'id' => 27,
	'title' => "Entrance to the Warrior's Guild",
	'description' => '',
	'area' => 'midgaard',
	'north' => 9
]);

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
