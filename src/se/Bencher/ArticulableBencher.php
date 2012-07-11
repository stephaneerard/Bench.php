<?php

namespace Doctissimo\Bencher;

class ArticulableBencher
{

	const OPT_SIZE_CONVERTER = 'size_converter';
	const OPT_TIME_CONVERTER = 'time_converter';
	const OPT_COMPARER = 'comparer';
	const OPT_BENCHS_RUNNER = 'benchers';
	const OPT_BENCH_RUNNER = 'bencher';
	const OPT_RENDERER = 'renderer';
	const OPT_EXECUTER = 'executer';
	const OPT_LOOPER = 'looper';
	const OPT_LOOP_RUNS = 'loop_runs';
	const OPT_DATA_COLLECTOR = 'data_collector';
	const OPT_DATA_COLLECTOR_DIFF = 'data_collector_differ';

	/**
	 * @var array[callback] contains bench callback functions
	 */
	protected $benchs = array();

	/**
	 * @var array contains named results of benchs
	 */
	protected $results = array();

	/**
	 * @var array contains results of comparisons
	 */
	protected $comparisons = array();

	/**
	 * @var array comparers
	 */
	protected $comparers = array();

	/**
	 * @var array renderers
	 */
	protected $renderers = array();

	/**
	 * @var array contains options (callback utils for convertion, comparison)
	 */
	protected $options = array();


	public function __construct($options)
	{
		$this->options = $options;
	}

	public function add($bench, $name, $stop = null, $start = 0)
	{
		if ($stop == null) {
			$stop = $this->options[self::OPT_LOOP_RUNS];
		}
		$this->benchs[$name] = array('name' => $name, 'callback' => $bench, 'start' => $start, 'stop' => $stop);
	}

	public function getBenchs()
	{
		return $this->benchs;
	}

	public function getResults()
	{
		return $this->results;
	}

	public function getComparers()
	{
		return $this->comparers;
	}

	public function addComparer($comparer, $name)
	{
		$this->comparers[$name] = $comparer;
		return $this;
	}

	public function getRenderers()
	{
		return $this->renderers;
	}

	public function addRenderer($renderer, $name)
	{
		$this->renderers[$name] = $renderer;
		return $this;
	}

	public function setResults($results)
	{
		$this->results = $results;
		return $this;
	}

	public function getComparisons()
	{
		return $this->comparisons;
	}

	public function setComparisons($comparisons)
	{
		$this->comparisons = $comparisons;
		return $this;
	}

	public function setOption($name, $value)
	{
		$this->options[$name] = $value;
		return $this;
	}

	public function getOption($name, $default = null)
	{
		return $this->options[$name] ? : $default;
	}

	public function execute()
	{
		call_user_func_array($this->options[self::OPT_EXECUTER], array($this));
		return $this;
	}

	public function render()
	{
		/** @var $renderer Closure */
		$renderer = $this->getOption(self::OPT_RENDERER);
		$renderer($this);
	}

}


