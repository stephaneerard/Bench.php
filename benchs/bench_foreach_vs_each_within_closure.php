<?php

/** @var $bencher Doctissimo\Bencher\ArticulableBencher */
$bencher = require dirname(__FILE__) . '/../src/se/Bencher/ArticulatedBencher.php';

$bencher->setOption('loop_runs', 450);


$big_array = range(0, 1000);

function loop(&$array, $callback) {
	reset($array);
	while ($current = each($array)) {
		$callback($current);
	}
}


$bencher->add(function() use ($big_array)
{
	foreach($big_array as $element){

	}

}, 'foreach');


$bencher->add(function() use ($big_array)
{
	while($current = each($big_array)){

	};

}, 'each');


$bencher->add(function() use ($big_array)
{
	loop($big_array, function($element){

	});

}, 'each_within_closure');





$bencher->execute()->render();