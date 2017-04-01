<?php

namespace Lynx\Core;

/**
 * Interface for stream functionality.
 * @package Lynx\Core
 */
interface StreamInterface extends \Countable, \Traversable
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
	 * @return StreamInterface
	 */
	public function each(callable $callback): StreamInterface;

	/**
	 * Projects each element of a sequence into a new form.
	 *
	 * @param callable $selector
	 * @return StreamInterface
	 */
	public function map(callable $selector): StreamInterface;

	/**
	 * Projects each element of a sequence to a Traversable or array
	 * and flattens the resulting sequences into one sequence.
	 *
	 * @param callable $selector
	 * @return StreamInterface
	 */
	public function mapFlat(callable $selector): StreamInterface;

	/**
	 * Filters a sequence of values based on a predicate.
	 *
	 * @param callable $predicate
	 * @return StreamInterface
	 */
	public function filter(callable $predicate): StreamInterface;

	/**
	 * Computes the sum of a sequence of numeric values. If a callback is given, the values are obtained by
	 * invoking the callback function on each element.
	 *
	 * @param callable|null $selector
	 * @return OptionalInterface
	 */
	public function sum(callable $selector = null): OptionalInterface;

	/**
	 * Returns the minimum value in a sequence of numeric values. If a callback is given, the values are obtained by
	 * invoking the callback function on each element.
	 *
	 * @param callable|null $selector
	 * @return OptionalInterface
	 */
	public function min(callable $selector = null): OptionalInterface;

	/**
	 * Returns the maximum value in a sequence of numeric values. If a callback is given, the values are obtained by
	 * invoking the callback function on each element.
	 *
	 * @param callable|null $selector
	 * @return OptionalInterface
	 */
	public function max(callable $selector = null): OptionalInterface;

	/**
	 * Computes the average of a sequence of values. If a callback is given, the values are obtained by
	 * invoking the callback function on each element.
	 *
	 * @param callable|null $selector
	 * @return OptionalInterface
	 */
	public function average(callable $selector = null): OptionalInterface;

	/**
	 * Computes the median of a sequence of values. If a callback is given, the values are obtained by
	 * invoking the callback function on each element.
	 *
	 * @param callable|null $selector
	 * @return OptionalInterface
	 */
	public function median(callable $selector = null): OptionalInterface;

	/**
	 * Applies an accumulator function over a sequence.
	 *
	 * @param callable $accumulator
	 * @return OptionalInterface
	 */
	public function reduce(callable $accumulator): OptionalInterface;

	/**
	 * Applies an accumulator function over a sequence. The specified seed value
	 * is used as the initial accumulator value.
	 *
	 * @param                $seed
	 * @param callable $accumulator
	 * @return OptionalInterface
	 */
	public function reduceWith($seed, callable $accumulator): OptionalInterface;

	/**
	 * Returns a specified number of contiguous elements from the start of a sequence.
	 *
	 * @param int $count
	 * @return StreamInterface
	 */
	public function take($count): StreamInterface;

	/**
	 * Returns elements from a sequence as long as a specified condition is true.
	 *
	 * @param callable $predicate
	 * @return StreamInterface
	 */
	public function takeWhile(callable $predicate): StreamInterface;

	/**
	 * Bypasses a specified number of elements in a sequence and then returns the remaining elements.
	 *
	 * @param $count
	 * @return StreamInterface
	 */
	public function skip($count): StreamInterface;

	/**
	 * Bypasses elements in a sequence as long as a specified
	 * condition is true and then returns the remaining elements.
	 *
	 * @param callable $predicate
	 * @return StreamInterface
	 */
	public function skipWhile(callable $predicate): StreamInterface;

	/**
	 * Concatenates sequences.
	 *
	 * @param array|StreamInterface $sources
	 * @return StreamInterface
	 */
	public function concat(...$sources): StreamInterface;

	/**
	 * Sorts the elements of a sequence in ascending order.
	 *
	 * @param int $flags
	 * @return StreamInterface
	 */
	public function sort($flags = SORT_REGULAR): StreamInterface;

	/**
	 * Sorts the elements of a sequence in descending order.
	 *
	 * @param int $flags
	 * @return StreamInterface
	 */
	public function sortDescending($flags = SORT_REGULAR): StreamInterface;

	/**
	 * Sorts the elements of a sequence in ascending order according to a key.
	 *
	 * @param callable $selector
	 * @return StreamInterface
	 */
	public function sortBy(callable $selector): StreamInterface;

	/**
	 * Sorts the elements of a sequence in descending order according to a key.
	 *
	 * @param callable $selector
	 * @return StreamInterface
	 */
	public function sortByDescending(callable $selector): StreamInterface;

	/**
	 * Inverts the order of elements in a sequence.
	 *
	 * @return StreamInterface
	 */
	public function reverse(): StreamInterface;

	/**
	 * Groups the elements of a sequence according to a specified key selector function.
	 *
	 * @param callable $selector
	 * @return StreamInterface
	 */
	public function groupBy(callable $selector): StreamInterface;

	/**
	 * Returns a sequence without duplicate elements.
	 *
	 * @return StreamInterface
	 */
	public function distinct(): StreamInterface;

	/**
	 * Returns the first element of a sequence. If a callback is given, the first element matching
	 * the predicate is returned.
	 *
	 * @param callable|null $predicate
	 * @return OptionalInterface
	 */
	public function first(callable $predicate = null): OptionalInterface;

	/**
	 * Returns the last element of a sequence. If a callback is given, the last element matching
	 * the predicate is returned.
	 *
	 * @param callable|null $predicate
	 * @return OptionalInterface
	 */
	public function last(callable $predicate = null): OptionalInterface;

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
