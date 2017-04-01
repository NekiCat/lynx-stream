<?php

namespace Lynx\Core;

use ArrayIterator;
use Closure;
use InvalidArgumentException;
use IteratorAggregate;

/**
 * Class Stream
 * @package Lynx\Core
 */
class Stream implements IteratorAggregate, StreamInterface
{
	use StreamTrait;

	/**
	 * @param Stream|\Traversable|array|Closure $source
	 * @return Stream|static
	 */
	public static function over($source): Stream
	{
		if ($source instanceof Stream)
		{
			return $source;
		}
		elseif ($source instanceof \Traversable)
		{
			return new static($source);
		}
		elseif (is_array($source))
		{
			return new static(new ArrayIterator($source));
		}
		elseif ($source instanceof Closure)
		{
			return new static(new RewindableGenerator($source));
		}
		else
		{
			throw new InvalidArgumentException('The source must be traversable.');
		}
	}

	/**
	 * Creates a range of integers that can be traversed. Both ascending and descending ranges are supported.
	 *
	 * @param int $low
	 * @param int $high
	 * @return Stream
	 */
	public static function range(int $low, int $high): Stream
	{
		if ($low <= $high)
		{
			return Stream::over(function () use ($low, $high)
			{
				for ($i = $low; $i <= $high; $i++)
				{
					yield $i;
				}
			});
		}
		else
		{
			return Stream::over(function () use ($low, $high)
			{
				for ($i = $low; $i >= $high; $i--)
				{
					yield $i;
				}
			});
		}
	}

	/**
	 * @type \Traversable
	 */
	protected $source;

	/**
	 * Traverse constructor.
	 *
	 * @param \Traversable
	 */
	protected function __construct(\Traversable $source)
	{
		$this->source = $source;
	}

	/**
	 * Retrieve an external iterator
	 *
	 * @link  http://php.net/manual/en/iteratoraggregate.getiterator.php
	 * @return \Traversable An instance of an object implementing <b>Iterator</b> or
	 *        <b>Traversable</b>
	 * @since 5.0.0
	 */
	public function getIterator(): \Traversable
	{
		return $this->source;
	}
}
