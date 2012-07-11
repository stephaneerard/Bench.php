<?php

use Doctissimo\Bencher\ArticulableBencher;

$name = 'memory_usage';

$sorter = function($a, $b)
{
	$a_time = $a['memory']['usage'];
	$b_time = $b['memory']['usage'];

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


$comparer = function($results) use ($sorter, $medianer)
{
	$total = null;
	$sum_computer = function($a) use (&$total)
	{
		$total += $a['memory']['usage'];
	};

	$return = array();
	foreach ($results as $bench => $result) {
		$_ = array('min' => null, 'max' => null, 'avg' => null);

		//calculate min & max
		$diffs = $result['diffs'];
		usort($diffs, $sorter);
		$_['min'] = $diffs[0]['memory']['usage'];
		$_['max'] = $diffs[count($diffs) - 1]['memory']['usage'];

		//calculate avg
		$total = 0;
		array_walk($diffs, $sum_computer);
		$_['avg'] = $total / count($diffs);

		//calculate median
		$times = array();
		foreach ($diffs as $diff) {
			$times[] = $diff['memory']['usage'];
		}
		$_['med'] = $medianer($times);

		$return[$bench] = $_;
	}

	return $return;

};

$renderer = function($result, ArticulableBencher $bencher) use($name)
{
	/** @var $time_formater Closure */
	$time_formater = $bencher->getOption('size_converter');
	echo sprintf("\t%-20s'\t min(%s) max(%s) avg(%s) med(%s)", $name, $time_formater($result['min']), $time_formater($result['max']), $time_formater($result['avg']), $time_formater($result['med']));
};


return array($name, $comparer, $renderer);