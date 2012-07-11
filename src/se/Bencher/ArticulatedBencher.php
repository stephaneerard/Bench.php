<?php
//@todo spl autoloader

require dirname(__FILE__) . '/ArticulableBencher.php';

use Doctissimo\Bencher\ArticulableBencher;

$bencher = new ArticulableBencher(array(
	'size_converter' => function($size)
	{
		$unit = array('b', 'kb', 'mb', 'gb', 'tb', 'pb');
		$i = floor(log($size, 1024));
		$value = @round($size / pow(1024, $i), 2);
		if ($i < 0) return '0b';
		return $value . $unit[$i];
	},
	'time_converter' => function($time_as_float)
	{
		return number_format($time_as_float, 10);
	},
	'benchers' => function(ArticulableBencher $bencher)
	{
		/** @var $unit_bencher Closure */
		$unit_bencher = $bencher->getOption('bencher');
		$results = array();
		foreach ($bencher->getBenchs() as $name => $bench) {
			$results[$name] = $unit_bencher($bench, $bencher);
		}
		return $results;
	},
	'bencher' => function($bench, ArticulableBencher $bencher)
	{
		/** @var $looper Closure */
		$looper = $bencher->getOption(ArticulableBencher::OPT_LOOPER);
		$diffs = $looper($bench['callback'], $bench['start'], $bench['stop'], $bencher);
		return array('diffs' => $diffs);
	},
	'renderer' => function(ArticulableBencher $bencher)
	{
		$benches = $bencher->getBenchs();
		$comparers = $bencher->getComparers();
		$comparisons = $bencher->getComparisons();
		$renderers = $bencher->getRenderers();

		foreach ($benches as $name => $bench) {
			echo $name, PHP_EOL;
			foreach ($comparers as $comparer => $comparerCb) {
				echo $renderers[$comparer]($comparisons[$comparer][$name], $bencher) . PHP_EOL;
			}
		}
	},
	'comparer' => function(ArticulableBencher $bencher)
	{
		$comparisons = array();
		$results = $bencher->getResults();
		foreach ($bencher->getComparers() as $name => $comparer) {
			$comparisons[$name] = $comparer($results);
		}
		return $comparisons;
	},
	'executer' => function(ArticulableBencher $bencher)
	{
		$bencher->setResults(call_user_func_array($bencher->getOption(ArticulableBencher::OPT_BENCHS_RUNNER), array($bencher)));
		$bencher->setComparisons(call_user_func_array($bencher->getOption(ArticulableBencher::OPT_COMPARER), array($bencher)));
	},
	'looper' => function($callback, $start, $stop, ArticulableBencher $bencher)
	{
		$diffs = array();
		/** @var $data_collector Closure */
		$data_collector = $bencher->getOption('data_collector');
		/** @var $data_collector_differ Closure */
		$data_collector_differ = $bencher->getOption('data_collector_differ');
		for ($i = $start; $i < $stop; $i++) {
			$begin = $data_collector();
			call_user_func($callback);
			$end = $data_collector();
			$diffs[] = $data_collector_differ($begin, $end);
		}
		return $diffs;
	},
	'data_collector' => function()
	{
		return array('time' => microtime(true), 'memory' => array('usage' => memory_get_usage(), 'peak' => memory_get_peak_usage()));
	},
	'data_collector_differ' => function($begin, $end)
	{
		return array('time' => $end['time'] - $begin['time'], 'memory' => array('usage' => $end['memory']['usage'] - $begin['memory']['usage'], 'peak' => $end['memory']['peak'] - $begin['memory']['peak']));
	}
));


$extras = array('time', 'memory_peak', 'memory_usage');

foreach ($extras as $extra) {
	list($name, $comparer, $renderer) = require dirname(__FILE__) . '/../../../extra/' . $extra . '.php';
	$bencher
		->addComparer($comparer, $name)
		->addRenderer($renderer, $name);
}


return $bencher;