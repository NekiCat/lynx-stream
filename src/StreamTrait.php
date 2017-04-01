<?php

namespace Lynx\Core;

use ArrayIterator;
use Closure;
use InvalidArgumentException;
use Traversable;

/**
 * A quick way to implement the StreamInterface interface. This trait assumes that the class this trait is included in is
 * itself already iterable and includes the StreamInterface interface.
 * @package Lynx\Core
 */
trait StreamTrait
{
	/**
	 * @type int
	 */
	private $count;

	/**
	 * @type bool
	 */
	private $hasConstructor;

	/**
	 * @param $args
	 * @return StreamInterface
	 */
	private function build($args): StreamInterface
	{
		if ($this->hasConstructor === null)
		{
			$this->hasConstructor = false;

			$params = (new \ReflectionClass($this))->getConstructor()->getParameters();
			if (count($params) == 1)
			{
				$type = $params[0]->getType();
				$this->hasConstructor = $type === null || $type == Traversable::class;
			}
		}

		if ($this->hasConstructor)
		{
			if ($args instanceof StreamInterface)
			{
				return $args;
			}
			elseif ($args instanceof Traversable)
			{
				/** @noinspection PhpIncompatibleReturnTypeInspection */
				return new static($args);
			}
			elseif (is_array($args))
			{
				/** @noinspection PhpIncompatibleReturnTypeInspection */
				return new static(new ArrayIterator($args));
			}
			elseif ($args instanceof Closure)
			{
				/** @noinspection PhpIncompatibleReturnTypeInspection */
				return new static(new RewindableGenerator($args));
			}
			else
			{
				throw new InvalidArgumentException('The source must be traversable.');
			}
		}
		else
		{
			return Stream::over($args);
		}
	}

	/**
	 * Returns the number of elements in a sequence. If a callback is given, returns the number of elements in
	 * a sequence that satisfy the callback predicate.
	 *
	 * @param callable|null $predicate
	 * @return int
	 */
	public function count(callable $predicate = null): int
	{
		if ($predicate !== null)
		{
			/** @noinspection PhpParamsInspection */
			return iterator_count($this->filter($predicate));
		}
		else
		{
			if ($this->count === null)
			{
				/** @noinspection PhpParamsInspection */
				$this->count = iterator_count($this);
			}

			return $this->count;
		}
	}

	/**
	 * Calls a function for each element in a sequence. This does not modify the sequence.
	 *
	 * @param callable $callback
	 * @return StreamInterface
	 */
	public function each(callable $callback): StreamInterface
	{
		foreach ($this as $item)
		{
			call_user_func($callback, $item);
		}

		/** @noinspection PhpIncompatibleReturnTypeInspection */
		return $this;
	}

	/**
	 * Projects each element of a sequence into a new form.
	 *
	 * @param callable $selector
	 * @return StreamInterface
	 */
	public function map(callable $selector): StreamInterface
	{
		/** @noinspection PhpIncompatibleReturnTypeInspection */
		return $this->build(function () use ($selector)
		{
			foreach ($this as $item)
			{
				yield call_user_func($selector, $item);
			}
		});
	}

	/**
	 * Projects each element of a sequence to a StreamInterface or array
	 * and flattens the resulting sequences into one sequence.
	 *
	 * @param callable $selector
	 * @return StreamInterface
	 */
	public function mapFlat(callable $selector): StreamInterface
	{
		/** @noinspection PhpIncompatibleReturnTypeInspection */
		return $this->build(function () use ($selector)
		{
			foreach ($this as $item)
			{
				$list = call_user_func($selector, $item);
				foreach ($list as $subItem)
				{
					yield $subItem;
				}
			}
		});
	}

	/**
	 * Filters a sequence of values based on a predicate.
	 *
	 * @param callable $predicate
	 * @return StreamInterface
	 */
	public function filter(callable $predicate): StreamInterface
	{
		/** @noinspection PhpIncompatibleReturnTypeInspection */
		return $this->build(function () use ($predicate)
		{
			foreach ($this as $item)
			{
				if (call_user_func($predicate, $item))
				{
					yield $item;
				}
			}
		});
	}

	/**
	 * Computes the sum of a sequence of numeric values. If a callback is given, the values are obtained by
	 * invoking the callback function on each element.
	 *
	 * @param callable|null $selector
	 * @return OptionalInterface
	 */
	public function sum(callable $selector = null): OptionalInterface
	{
		$sum = null;
		foreach ($this as $item)
		{
			if ($selector !== null)
			{
				$value = call_user_func($selector, $item);
			}
			else
			{
				$value = $item;
			}

			if ($sum === null)
			{
				$sum = $value;
			}
			else
			{
				$sum += $value;
			}
		}

		return Optional::ofNullable($sum);
	}

	/**
	 * Returns the minimum value in a sequence of numeric values. If a callback is given, the values are obtained by
	 * invoking the callback function on each element.
	 *
	 * @param callable|null $selector
	 * @return OptionalInterface
	 */
	public function min(callable $selector = null): OptionalInterface
	{
		$min = null;
		foreach ($this as $item)
		{
			if ($selector !== null)
			{
				$value = call_user_func($selector, $item);
			}
			else
			{
				$value = $item;
			}

			if ($min === null || $min > $value)
			{
				$min = $value;
			}
		}

		return Optional::ofNullable($min);
	}

	/**
	 * Returns the maximum value in a sequence of numeric values. If a callback is given, the values are obtained by
	 * invoking the callback function on each element.
	 *
	 * @param callable|null $selector
	 * @return OptionalInterface
	 */
	public function max(callable $selector = null): OptionalInterface
	{
		$max = null;
		foreach ($this as $item)
		{
			if ($selector !== null)
			{
				$value = call_user_func($selector, $item);
			}
			else
			{
				$value = $item;
			}

			if ($max === null || $max < $value)
			{
				$max = $value;
			}
		}

		return Optional::ofNullable($max);
	}

	/**
	 * Computes the average of a sequence of values. If a callback is given, the values are obtained by
	 * invoking the callback function on each element.
	 *
	 * @param callable|null $selector
	 * @return OptionalInterface
	 */
	public function average(callable $selector = null): OptionalInterface
	{
		$count = $this->count($selector);
		return $this->sum($selector)
			->map(function ($sum) use ($count)
			{
				return $sum / $count;
			});
	}

	/**
	 * Computes the median of a sequence of values. If a callback is given, the values are obtained by
	 * invoking the callback function on each element.
	 *
	 * @param callable|null $selector
	 * @return OptionalInterface
	 */
	public function median(callable $selector = null): OptionalInterface
	{
		$count = $this->count($selector);
		if ($count == 0)
		{
			return Optional::empty();
		}

		if ($selector !== null)
		{
			$sorted = $this->sortBy($selector);
		}
		else
		{
			$sorted = $this->sort();
		}

		if ($count % 2 == 0)
		{
			return $sorted->skip($count / 2 - 1)->take(2)->average($selector);
		}
		else
		{
			$opt = $sorted->skip(floor($count / 2))->first();
			if ($selector !== null && $opt->isPresent()) {
				return $opt->map($selector);
			}

			return $opt;
		}
	}

	/**
	 * Applies an accumulator function over a sequence.
	 *
	 * @param callable $accumulator
	 * @return OptionalInterface
	 */
	public function reduce(callable $accumulator): OptionalInterface
	{
		$first = true;
		$aggregate = null;
		foreach ($this as $item)
		{
			if ($first)
			{
				$aggregate = $item;
				$first = false;
			}
			else
			{
				$aggregate = call_user_func($accumulator, $aggregate, $item);
			}
		}

		return Optional::ofNullable($aggregate);
	}

	/**
	 * Applies an accumulator function over a sequence. The specified seed value
	 * is used as the initial accumulator value.
	 *
	 * @param                $seed
	 * @param callable $accumulator
	 * @return OptionalInterface
	 */
	public function reduceWith($seed, callable $accumulator): OptionalInterface
	{
		$aggregate = $seed;
		foreach ($this as $item)
		{
			$aggregate = call_user_func($accumulator, $aggregate, $item);
		}

		return Optional::ofNullable($aggregate);
	}

	/**
	 * Returns a specified number of contiguous elements from the start of a sequence.
	 *
	 * @param int $count
	 * @return StreamInterface
	 */
	public function take($count): StreamInterface
	{
		/** @noinspection PhpIncompatibleReturnTypeInspection */
		return $this->build(function () use ($count)
		{
			foreach ($this as $item)
			{
				if ($count > 0)
				{
					$count--;
					yield $item;
				}
				else
				{
					break;
				}
			}
		});
	}

	/**
	 * Returns elements from a sequence as long as a specified condition is true.
	 *
	 * @param callable $predicate
	 * @return StreamInterface
	 */
	public function takeWhile(callable $predicate): StreamInterface
	{
		/** @noinspection PhpIncompatibleReturnTypeInspection */
		return $this->build(function () use ($predicate)
		{
			foreach ($this as $item)
			{
				if (!call_user_func($predicate, $item))
				{
					break;
				}

				yield $item;
			}
		});
	}

	/**
	 * Bypasses a specified number of elements in a sequence and then returns the remaining elements.
	 *
	 * @param $count
	 * @return StreamInterface
	 */
	public function skip($count): StreamInterface
	{
		/** @noinspection PhpIncompatibleReturnTypeInspection */
		return $this->build(function () use ($count)
		{
			foreach ($this as $item)
			{
				if ($count > 0)
				{
					$count--;
				}
				else
				{
					yield $item;
				}
			}
		});
	}

	/**
	 * Bypasses elements in a sequence as long as a specified
	 * condition is true and then returns the remaining elements.
	 *
	 * @param callable $predicate
	 * @return StreamInterface
	 */
	public function skipWhile(callable $predicate): StreamInterface
	{
		/** @noinspection PhpIncompatibleReturnTypeInspection */
		return $this->build(function () use ($predicate)
		{
			$skip = true;
			foreach ($this as $item)
			{
				if ($skip && !call_user_func($predicate, $item))
				{
					$skip = false;
				}

				if (!$skip)
				{
					yield $item;
				}
			}
		});
	}

	/**
	 * Concatenates sequences.
	 *
	 * @param array|StreamInterface $sources
	 * @return StreamInterface
	 */
	public function concat(...$sources): StreamInterface
	{
		if (count($sources) == 0)
		{
			/** @noinspection PhpIncompatibleReturnTypeInspection */
			return $this;
		}

		/** @noinspection PhpIncompatibleReturnTypeInspection */
		return $this->build(function () use ($sources)
		{
			foreach ($this as $item)
			{
				yield $item;
			}

			foreach ($sources as $source)
			{
				foreach ($source as $item)
				{
					yield $item;
				}
			}
		});
	}

	/**
	 * Sorts the elements of a sequence in ascending order.
	 *
	 * @param int $flags
	 * @return StreamInterface
	 */
	public function sort($flags = SORT_REGULAR): StreamInterface
	{
		$array = $this->toArray();
		sort($array, $flags);

		/** @noinspection PhpIncompatibleReturnTypeInspection */
		return $this->build($array);
	}

	/**
	 * Sorts the elements of a sequence in descending order.
	 *
	 * @param int $flags
	 * @return StreamInterface
	 */
	public function sortDescending($flags = SORT_REGULAR): StreamInterface
	{
		$array = $this->toArray();
		arsort($array, $flags);

		/** @noinspection PhpIncompatibleReturnTypeInspection */
		return $this->build($array);
	}

	/**
	 * Sorts the elements of a sequence in ascending order according to a key.
	 *
	 * @param callable $selector
	 * @return StreamInterface
	 */
	public function sortBy(callable $selector): StreamInterface
	{
		$array = $this->toArray();
		usort($array, function ($a, $b) use ($selector)
		{
			return call_user_func($selector, $a) - call_user_func($selector, $b);
		});

		/** @noinspection PhpIncompatibleReturnTypeInspection */
		return $this->build($array);
	}

	/**
	 * Sorts the elements of a sequence in descending order according to a key.
	 *
	 * @param callable $selector
	 * @return StreamInterface
	 */
	public function sortByDescending(callable $selector): StreamInterface
	{
		$array = $this->toArray();
		usort($array, function ($a, $b) use ($selector)
		{
			return call_user_func($selector, $b) - call_user_func($selector, $a);
		});

		/** @noinspection PhpIncompatibleReturnTypeInspection */
		return $this->build($array);
	}

	/**
	 * Inverts the order of elements in a sequence.
	 *
	 * @return StreamInterface
	 */
	public function reverse(): StreamInterface
	{
		/** @noinspection PhpIncompatibleReturnTypeInspection */
		return $this->build(array_reverse($this->toArray()));
	}

	/**
	 * Groups the elements of a sequence according to a specified key selector function.
	 *
	 * @param callable $selector
	 * @return StreamInterface
	 */
	public function groupBy(callable $selector): StreamInterface
	{
		$groups = [];
		foreach ($this as $item)
		{
			$key = call_user_func($selector, $item);
			$groups[$key][] = $item;
		}

		/** @noinspection PhpIncompatibleReturnTypeInspection */
		return $this->build(function () use ($groups)
		{
			foreach ($groups as $key => $item)
			{
				yield [
					'key' => $key,
					'values' => $item,
				];
			}
		});
	}

	/**
	 * Returns a sequence without duplicate elements.
	 *
	 * @return StreamInterface
	 */
	public function distinct(): StreamInterface
	{
		$distinct = [];
		foreach ($this as $item)
		{
			$distinct[$item] = true;
		}

		/** @noinspection PhpIncompatibleReturnTypeInspection */
		return $this->build(array_keys($distinct));
	}

	/**
	 * Returns the first element of a sequence. If a callback is given, the first element matching
	 * the predicate is returned.
	 *
	 * @param callable|null $predicate
	 * @return OptionalInterface
	 */
	public function first(callable $predicate = null): OptionalInterface
	{
		foreach ($this as $item)
		{
			if ($predicate !== null)
			{
				if (call_user_func($predicate, $item))
				{
					return Optional::of($item);
				}
			}
			else
			{
				return Optional::of($item);
			}
		}

		return Optional::empty();
	}

	/**
	 * Returns the last element of a sequence. If a callback is given, the last element matching
	 * the predicate is returned.
	 *
	 * @param callable|null $predicate
	 * @return OptionalInterface
	 */
	public function last(callable $predicate = null): OptionalInterface
	{
		$last = null;
		foreach ($this as $item)
		{
			if ($predicate !== null)
			{
				if (call_user_func($predicate, $item))
				{
					$last = $item;
				}
			}
			else
			{
				$last = $item;
			}
		}

		return Optional::ofNullable($last);
	}

	/**
	 * Determines whether any element of a sequence exists or satifies a condition.
	 *
	 * @param callable $predicate
	 * @return bool
	 */
	public function any(callable $predicate): bool
	{
		foreach ($this as $item)
		{
			if (call_user_func($predicate, $item))
			{
				return true;
			}
		}

		return false;
	}

	/**
	 * Determines whether all elements of a sequence satisfy a condition.
	 *
	 * @param callable $predicate
	 * @return bool
	 */
	public function all(callable $predicate): bool
	{
		foreach ($this as $item)
		{
			if (!call_user_func($predicate, $item))
			{
				return false;
			}
		}

		return true;
	}

	/**
	 * Determines whether a sequence contains a specified element.
	 *
	 * @param mixed $element
	 * @param bool $strict
	 * @return bool
	 */
	public function contains($element, $strict = false): bool
	{
		foreach ($this as $item)
		{
			if ($strict)
			{
				if ($item === $element)
				{
					return true;
				}
			}
			else
			{
				if ($item == $element)
				{
					return true;
				}
			}
		}

		return false;
	}

	/**
	 * @param string $separator
	 * @return string
	 */
	public function join($separator = ''): string
	{
		return join($separator, $this->toArray());
	}

	/**
	 * Iterates over a sequence and aggregates all elements to an array.
	 *
	 * @return array
	 */
	public function toArray(): array
	{
		$result = [];
		foreach ($this as $item)
		{
			$result[] = $item;
		}

		return $result;
	}
}
