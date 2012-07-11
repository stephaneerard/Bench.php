<?php

/** @var $bencher Doctissimo\Bencher\ArticulableBencher */
$bencher = require dirname(__FILE__) . '/../src/se/Bencher/ArticulatedBencher.php';

$bencher->setOption('loop_runs', 10000);

/***
 *
 *             OBJECT
 *
 */


$bencher->add(function()
{
	$a = new stdClass();
	$a->hey = rand(0, 100);
}, 'stdClass');

$object = new stdClass();
$object->hey = 'hey';
$object->hoy = 'hoy';

$bencher->add(function() use ($object)
{
	$b = $object->hey;
}, 'stdClass_usage');


/**
 *
 *         ARRAY
 *
 */
$bencher->add(function()
{
	$a = array();
	$a['hey'] = rand(0, 100);
}, 'array');

$array = array('hey' => 'hey', 'hoy' => 'hoy');

$bencher->add(function() use ($array)
{
	$b = $array['hey'];
}, 'array_usage');


/**
 *
 *
 *             TYPE CASTING
 *
 */
$int = 0;
$bencher->add(function() use($int)
{
	$a = intval($int);
}, 'int_cast');

$bencher->add(function() use($int)
{
	$a = $int;
}, 'int_nocast');


/**
 *
 *             EMPTY vs bitmasking
 *
 */
$wrong1 = 0;
$wrong2 = 0;
$wrong3 = 0;
$wrong4 = 0;
$good1 = 1;
$good2 = 1;
$good3 = 1;
$good4 = 1;
$bencher->add(function() use($wrong1, $wrong2, $wrong3, $wrong4)
{
	empty($wrong1) || empty($wrong2) || empty($wrong3) || empty($wrong4);
}, 'check_many_ints_>0_with_empty');

$bencher->add(function() use($good1, $good2, $good3, $good4)
{
	($good1 & $good2 & $good3 & $good4) > 0;
}, 'check_many_ints_>0_with_bitmask_expected');

$bencher->add(function() use($wrong1, $wrong2, $wrong3, $wrong4)
{
	($wrong1 & $wrong2 & $wrong3 & $wrong4) == 0;
}, 'check_many_ints_>0_with_bitmask_expected_not');


$bencher->execute()->render();
