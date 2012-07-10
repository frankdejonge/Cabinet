<?php

namespace Cabinet\Database;

abstract class Fn
{
	/**
	 * @var  array  $params  function params
	 */
	protected $params = array();

	/**
	 * @var  string  $fn  function name
	 */
	protected $fn

	/**
	 * Constructor, stores function name and ensures $params is an array.
	 *
	 * @param  string  $fn      function name
	 * @param  mixed   $params  function params
	 */
	public function __construct($fn, $params = array())
	{
		is_array($params) or $params = array($params);

		$this->fn = $fn;
		$this->params = $params;
	}

	/**
	 * Retrieve the function name.
	 *
	 * @return  string  function name
	 */
	public function getFn()
	{
		return $this->fn;
	}

	/**
	 * Retrieve the function params.
	 *
	 * @return  array  function params
	 */
	public function getParams()
	{
		return $this->params;
	}
}