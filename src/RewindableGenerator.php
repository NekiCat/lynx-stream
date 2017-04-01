<?php

namespace Lynx\Core;

use Closure;
use InvalidArgumentException;
use Iterator;
use ReflectionFunction;

/**
 * Enables a generator function using yield to be used multiple times.
 * @package Lynx\Core
 */
final class RewindableGenerator implements Iterator
{
	/**
	 * @type Closure
	 */
	private $generator;

	/**
	 * @type Iterator
	 */
	private $source;

	/**
	 * Constructs a new rewindable generator iterator. The given closure must be a generator function.
	 *
	 * @param Closure $generator
	 */
	public function __construct(Closure $generator)
	{
		if (!(new ReflectionFunction($generator))->isGenerator())
		{
			throw new InvalidArgumentException('The closure must be a generator function.');
		}

		$this->generator = $generator;
	}

	/**
	 * Return the current element
	 *
	 * @link  http://php.net/manual/en/iterator.current.php
	 * @return mixed Can return any type.
	 * @since 5.0.0
	 */
	public function current()
	{
		return $this->source->current();
	}

	/**
	 * Move forward to next element
	 *
	 * @link  http://php.net/manual/en/iterator.next.php
	 * @return void Any returned value is ignored.
	 * @since 5.0.0
	 */
	public function next()
	{
		$this->source->next();
	}

	/**
	 * Return the key of the current element
	 *
	 * @link  http://php.net/manual/en/iterator.key.php
	 * @return mixed scalar on success, or null on failure.
	 * @since 5.0.0
	 */
	public function key()
	{
		return $this->source->key();
	}

	/**
	 * Checks if current position is valid
	 *
	 * @link  http://php.net/manual/en/iterator.valid.php
	 * @return boolean The return value will be casted to boolean and then evaluated.
	 *        Returns true on success or false on failure.
	 * @since 5.0.0
	 */
	public function valid()
	{
		return $this->source->valid();
	}

	/**
	 * Rewind the Iterator to the first element
	 *
	 * @link  http://php.net/manual/en/iterator.rewind.php
	 * @return void Any returned value is ignored.
	 * @since 5.0.0
	 */
	public function rewind()
	{
		$this->source = call_user_func($this->generator);
	}
}
