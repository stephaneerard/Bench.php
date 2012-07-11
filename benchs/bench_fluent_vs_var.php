<?php

/** @var $bencher Doctissimo\Bencher\ArticulableBencher */
$bencher = require dirname(__FILE__) . '/../src/se/Bencher/ArticulatedBencher.php';

$bencher->setOption('loop_runs', 45000);


class TestMachinWithReturn{
	public function nothing(){
		return $this;
	}
}



$machin = new TestMachinWithReturn();

$bencher->add(function() use($machin){
	$machin->nothing()->nothing();
}, 'test_fluent_recall');



$bencher->add(function() use($machin){
	$machin->nothing();
	$machin->nothing();
}, 'test_fluent_no_recall');





$bencher->add(function() use($machin){
	$machin->nothing();
}, 'test_no_return_cost');


/**
 *
 *
 *
 */

class TestMachineWithoutReturn{
	public function nothing(){

	}
}


$machin = new TestMachineWithoutReturn();

$bencher->add(function() use($machin){
	$machin->nothing();
	$machin->nothing();
}, 'test_cost_no_fluent_no_return');

$bencher->execute()->render();