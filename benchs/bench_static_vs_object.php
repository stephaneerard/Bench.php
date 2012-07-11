<?php

/** @var $bencher Doctissimo\Bencher\ArticulableBencher */
$bencher = require dirname(__FILE__) . '/../src/se/Bencher/ArticulatedBencher.php';

$bencher->setOption('loop_runs', 10000);

/***
 *
 *             Static call
 *
 */

class StaticClassWithPublicMethod
{
	static public function static_method()
	{

	}
}

$bencher->add(function()
{
	StaticClassWithPublicMethod::static_method();

}, 'static_call');


/**
 *
 *                Object call
 *
 */

class ObjectClassWithPublicMethod
{
	public function object_method()
	{

	}
}

$a = new ObjectClassWithPublicMethod();

$bencher->add(function() use($a)
{
	$a->object_method();

}, 'object_call');


/**
 *
 *
 *             Object access of public property via direct
 *
 */

class ObjectClassWithPublicProperty
{
	public $a = null;
}

$b = new ObjectClassWithPublicProperty();

$bencher->add(function() use($b)
{
	$b->a;

}, 'object_access_public_property_via_direct');


/**
 *
 *             Object access of protected property via public getter
 *
 */
class ObjectClassWithProtectedProperty
{
	protected $a = null;

	public function getA()
	{
		return $this->a;
	}
}

$b = new ObjectClassWithProtectedProperty();

$bencher->add(function() use($b)
{
	$b->getA();

}, 'object_access_protected_property_via_public_getter');


/**
 *
 *             Object access of private property via public getter
 *
 */
class ObjectClassWithPrivateProperty
{
	private $a = null;

	public function getA()
	{
		return $this->a;
	}
}

$b = new ObjectClassWithPrivateProperty();

$bencher->add(function() use($b)
{
	$b->getA();

}, 'object_access_private_property_via_public_getter');


$bencher->execute()->render();