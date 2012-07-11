<?php

use Doctissimo\Bencher\ArticulableBencher;

$name = 'time';

$sorter = function($a, $b)
{
	$a_time = $a['time'];
	$b_time = $b['time'];

	if ($a_time < $b_time) return -1;
	if ($a_time == $b_time) return 0;
	if ($a_time > $b_time) return 1;
};

$medianer = function($values)
{
	sort($values);
	$count = count($values); //total numbers in array
	$middleval = floor(($count - 1) / 2); // find the middle value, or the lowest middle value
	if ($count % 2) { // odd number, middle is the median
		$median = $values[$middleval];
	} else { // even number, calculate avg of 2 medians
		$low = $values[$middleval];
		$high = $values[$middleval + 1];
		$median = (($low + $high) / 2);
	}
	return $median;
};


$comparer = function($results) use($sorter, $medianer)
{
	$total = null;
	$sum_computer = function($a) use (&$total)
	{
		$total += $a['time'];
	};

	$return = array();
	foreach ($results as $bench => $result) {
		$_ = array('min' => null, 'max' => null, 'avg' => null);

		//calculate min & max
		$diffs = $result['diffs'];
		usort($diffs, $sorter);
		$_['min'] = $diffs[0]['time'];
		$_['max'] = $diffs[count($diffs) - 1]['time'];

		//calculate avg
		$total = 0;
		array_walk($diffs, $sum_computer);
		$_['avg'] = $total / count($diffs);

		//calculate median
		$times = array();
		foreach ($diffs as $diff) {
			$times[] = $diff['time'];
		}
		$_['med'] = $medianer($times);


		$return[$bench] = $_;
	}

	return $return;

};

$renderer = function($result, ArticulableBencher $bencher) use ($name)
{
	/** @var $time_formater Closure */
	$time_formater = $bencher->getOption('time_converter');
	echo sprintf("\t%-20s'\t min(%s) max(%s) avg(%s) med(%s)", $name, $time_formater($result['min']), $time_formater($result['max']), $time_formater($result['avg']), $time_formater($result['med']));
};


return array($name, $comparer, $renderer);