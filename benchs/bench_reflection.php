<?php

/** @var $bencher Doctissimo\Bencher\ArticulableBencher */
$bencher = require dirname(__FILE__) . '/../src/se/Bencher/ArticulatedBencher.php';

$bencher->setOption('loop_runs', 45000);


class TestClass
{
	public function testMethod(TestClass $arg1 = null)
	{
	}
}

$bencher->add(function()
{
	$methods = get_class_methods('TestClass');
}, 'get_class_methods_method');


$refl = new ReflectionClass('TestClass');

$bencher->add(function() use ($refl)
{
	$methods = $refl->getMethods();
}, 'get_class_methods_reflection');


$t = new TestClass();

$bencher->add(function() use ($t)
{
	$refl = get_class_methods($t);
}, 'get_class_methods_from_object');








$bencher->execute()->render();