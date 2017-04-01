<?php

namespace Lynx\Core;

/**
 * Interface for a container that may or may not hold a value.
 * @package Lynx\Core
 */
interface Optional
{
	/**
	 * Returns true if this optional holds a non-null value, false otherwise.
	 *
	 * @return bool
	 */
	public function isPresent(): bool;

	/**
	 * Calls a consumer function if this optional holds a value.
	 *
	 * @param callable $consumer
	 * @return Optional
	 */
	public function ifPresent(callable $consumer): Optional;

	/**
	 * Returns the value contained inside this optional. If there is no value stored in this optional,
	 * throws a LogicException instead.
	 *
	 * @return mixed
	 */
	public function get();

	/**
	 * Calls a selector function if this optional holds a value. The value returned from the
	 * selector function is wrapped in a optional and returned.
	 *
	 * @param callable $selector
	 * @return Optional
	 */
	public function map(callable $selector): Optional;

	/**
	 * Calls a predicate function if this optional holds a value. If the predicate function
	 * returns true, this optional object is returned, otherwise an empty optional is returned.
	 *
	 * @param callable $predicate
	 * @return Optional
	 */
	public function filter(callable $predicate): Optional;

	/**
	 * If this optional holds a value, returns this value, otherwise returns the argument value.
	 *
	 * @param $value
	 * @return mixed
	 */
	public function orElse($value): mixed;

	/**
	 * If this optional holds a value, returns this value, otherwise calls the supplier function and
	 * returns the return value from the supplier function. Use this over Optional::orElse() for
	 * values that are difficult to compute for lazy evaluation.
	 *
	 * @param callable $supplier
	 * @return mixed
	 */
	public function orElseGet(callable $supplier): mixed;

	/**
	 * If this optional holds a value, returns this value, otherwise calls the supplier function and
	 * throws the returned exception. If the supplier function does not return an exception object, throws
	 * a LogicException instead.
	 *
	 * @param callable $supplier
	 * @return mixed
	 */
	public function orElseThrow(callable $supplier): mixed;
}
