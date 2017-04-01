<?php

namespace Lynx\Core;

/**
 * Interface for stream functionality.
 * @package Lynx\Core
 */
interface Stream extends \Countable, \Traversable
{
	/**
	 * Returns the number of elements in a sequence. If a callback is given, returns the number of elements in
	 * a sequence that satisfy the callback predicate.
	 *
	 * @param callable|null $predicate
	 * @return int
	 */
	public function count(callable $predicate = null): int;

	/**
	 * Calls a function for each element in a sequence. This does not modify the sequence.
	 *
	 * @param callable $callback
	 * @return Stream
	 */
	public function each(callable $callback): Stream;

	/**
	 * Projects each element of a sequence into a new form.
	 *
	 * @param callable $selector
	 * @return Stream
	 */
	public function map(callable $selector): Stream;

	/**
	 * Projects each element of a sequence to a Traversable or array
	 * and flattens the resulting sequences into one sequence.
	 *
	 * @param callable $selector
	 * @return Stream
	 */
	public function mapFlat(callable $selector): Stream;

	/**
	 * Filters a sequence of values based on a predicate.
	 *
	 * @param callable $predicate
	 * @return Stream
	 */
	public function filter(callable $predicate): Stream;

	/**
	 * Computes the sum of a sequence of numeric values. If a callback is given, the values are obtained by
	 * invoking the callback function on each element.
	 *
	 * @param callable|null $selector
	 * @return Optional
	 */
	public function sum(callable $selector = null): Optional;

	/**
	 * Returns the minimum value in a sequence of numeric values. If a callback is given, the values are obtained by
	 * invoking the callback function on each element.
	 *
	 * @param callable|null $selector
	 * @return Optional
	 */
	public function min(callable $selector = null): Optional;

	/**
	 * Returns the maximum value in a sequence of numeric values. If a callback is given, the values are obtained by
	 * invoking the callback function on each element.
	 *
	 * @param callable|null $selector
	 * @return Optional
	 */
	public function max(callable $selector = null): Optional;

	/**
	 * Computes the average of a sequence of values. If a callback is given, the values are obtained by
	 * invoking the callback function on each element.
	 *
	 * @param callable|null $selector
	 * @return Optional
	 */
	public function average(callable $selector = null): Optional;

	/**
	 * Computes the median of a sequence of values. If a callback is given, the values are obtained by
	 * invoking the callback function on each element.
	 *
	 * @param callable|null $selector
	 * @return Optional
	 */
	public function median(callable $selector = null): Optional;

	/**
	 * Applies an accumulator function over a sequence.
	 *
	 * @param callable $accumulator
	 * @return Optional
	 */
	public function reduce(callable $accumulator): Optional;

	/**
	 * Applies an accumulator function over a sequence. The specified seed value
	 * is used as the initial accumulator value.
	 *
	 * @param                $seed
	 * @param callable $accumulator
	 * @return Optional
	 */
	public function reduceWith($seed, callable $accumulator): Optional;

	/**
	 * Returns a specified number of contiguous elements from the start of a sequence.
	 *
	 * @param int $count
	 * @return Stream
	 */
	public function take($count): Stream;

	/**
	 * Returns elements from a sequence as long as a specified condition is true.
	 *
	 * @param callable $predicate
	 * @return Stream
	 */
	public function takeWhile(callable $predicate): Stream;

	/**
	 * Bypasses a specified number of elements in a sequence and then returns the remaining elements.
	 *
	 * @param $count
	 * @return Stream
	 */
	public function skip($count): Stream;

	/**
	 * Bypasses elements in a sequence as long as a specified
	 * condition is true and then returns the remaining elements.
	 *
	 * @param callable $predicate
	 * @return Stream
	 */
	public function skipWhile(callable $predicate): Stream;

	/**
	 * Concatenates sequences.
	 *
	 * @param array|Stream $sources
	 * @return Stream
	 */
	public function concat(...$sources): Stream;

	/**
	 * Sorts the elements of a sequence in ascending order.
	 *
	 * @param int $flags
	 * @return Stream
	 */
	public function sort($flags = SORT_REGULAR): Stream;

	/**
	 * Sorts the elements of a sequence in descending order.
	 *
	 * @param int $flags
	 * @return Stream
	 */
	public function sortDescending($flags = SORT_REGULAR): Stream;

	/**
	 * Sorts the elements of a sequence in ascending order according to a key.
	 *
	 * @param callable $selector
	 * @return Stream
	 */
	public function sortBy(callable $selector): Stream;

	/**
	 * Sorts the elements of a sequence in descending order according to a key.
	 *
	 * @param callable $selector
	 * @return Stream
	 */
	public function sortByDescending(callable $selector): Stream;

	/**
	 * Inverts the order of elements in a sequence.
	 *
	 * @return Stream
	 */
	public function reverse(): Stream;

	/**
	 * Groups the elements of a sequence according to a specified key selector function.
	 *
	 * @param callable $selector
	 * @return Stream
	 */
	public function groupBy(callable $selector): Stream;

	/**
	 * Returns a sequence without duplicate elements.
	 *
	 * @return Stream
	 */
	public function distinct(): Stream;

	/**
	 * Returns the first element of a sequence. If a callback is given, the first element matching
	 * the predicate is returned.
	 *
	 * @param callable|null $predicate
	 * @return Optional
	 */
	public function first(callable $predicate = null): Optional;

	/**
	 * Returns the last element of a sequence. If a callback is given, the last element matching
	 * the predicate is returned.
	 *
	 * @param callable|null $predicate
	 * @return Optional
	 */
	public function last(callable $predicate = null): Optional;

	/**
	 * Determines whether any element of a sequence exists or satifies a condition.
	 *
	 * @param callable $predicate
	 * @return bool
	 */
	public function any(callable $predicate): bool;

	/**
	 * Determines whether all elements of a sequence satisfy a condition.
	 *
	 * @param callable $predicate
	 * @return bool
	 */
	public function all(callable $predicate): bool;

	/**
	 * Determines whether a sequence contains a specified element.
	 *
	 * @param mixed $element
	 * @param bool $strict
	 * @return bool
	 */
	public function contains($element, $strict = false): bool;

	/**
	 * @param string $separator
	 * @return string
	 */
	public function join($separator = ''): string;

	/**
	 * Iterates over a sequence and aggregates all elements to an array.
	 *
	 * @return array
	 */
	public function toArray(): array;
}
