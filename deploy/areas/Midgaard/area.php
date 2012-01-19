<?php
use \Mechanics\Room,
	\Mechanics\Item,
	\Mechanics\Actor,
	\Mechanics\Affect,
	\Mechanics\Attributes,
	\Mechanics\Equipment,
	\Living\Shopkeeper,
	\Items\Food,
	\Items\Armor;

new Room([
	'id' => 1,
	'title' => 'Temple of Midgaard',
	'description' => 'A large temple surrounds you, with sculptures of gods adorning the walls.',
	'area' => 'midgaard',
	'south' => 2
]);

new Room([
	'id' => 2,
	'title' => 'Temple Garden',
	'description' => 'A small fountain lies to the side of the cobblestone path, which dissects an impressive garden. The temple of Midgaard is to the north, with the market square to the south.',
	'area' => 'midgaard',
	'north' => 1,
	'south' => 3,
	'west' => 'midgaard_arena5,3'
]);

new Room([
	'id' => 3,
	'title' => 'Midgaard Market Square',
	'description' => 'Cobblestone paths from all directions converge here, with shops lining a massive square.',
	'area' => 'midgaard',
	'north' => 2,
	'west' => 4,
	'east' => 7
]);


new Room([
	'id' => 10,
	'title' => 'Blue Moon Bakery',
	'description' => 'You enter a small but brightly lit room. All along the walls are shelves lined with baked goods.',
	'area' => 'midgaard',
	'south' => 4,
	'actors' => [
		new Shopkeeper([
			'alias' => 'Anyan',
			'nouns' => 'anyan baker',
			'short' => 'Anyan, the town baker',
			'long' => 'An old and wirey elf stands before you.',
			'race' => 'elf',
			'items' => [
				new Food([
					'alias' => 'a baked apple pie',
					'short' => 'a baked apple pie',
					'nouns' => 'baked apple pie',
					'nourishment' => 1,
					'value' => 5
				]),
				new Food([
					'alias' => 'a big pot pie',
					'short' => 'a big pot pie',
					'nouns' => 'pot pie',
					'nourishment' => 2,
					'value' => 8
				])
			]
		])
	]
]);

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
			'short' => 'Halek, the blacksmith',
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
			'short' => 'Mardwyn, the general store clerk',
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
					'value' => 10,
					'affects' => [
						new Affect(['affect' => 'glow'])
					]
				])
			]
		])
	]
]);

$p = [
	'title' => 'Market Street',
	'description' => 'A cobblestone path travels in a east-west direction with shops lining both sides.',
	'area' => 'midgaard'
];

new Room(array_merge(['id' => 4, 'east' => 3, 'west' => 5, 'north' => 10, 'south' => 11], $p));
new Room(array_merge(['id' => 5, 'east' => 4, 'west' => 6], $p));
new Room(array_merge(['id' => 6, 'east' => 5, 'west' => 13], $p));
new Room(array_merge(['id' => 7, 'east' => 8, 'west' => 3, 'north' => 12], $p));
new Room(array_merge(['id' => 8, 'east' => 9, 'west' => 7], $p));
new Room(array_merge(['id' => 9, 'west' => 8, 'east' => 14], $p));

new Room([
	'id' => 13,
	'title' => 'West Gate of Midgaard',
	'description' => 'A stone gate to the west marks the end of Midgaard. Cobblestone paths lead in all directions.',
	'area' => 'midgaard',
	'east' => 6
]);

new Room([
	'id' => 14,
	'title' => 'East Gate of Midgaard',
	'description' => 'A stone gate to the east marks the end of Midgaard. Cobblestone paths lead in all directions.',
	'area' => 'midgaard',
	'west' => 9
]);

Room::setStartRoom(1);

?>
