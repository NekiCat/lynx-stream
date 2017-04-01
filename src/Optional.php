<?php

namespace Lynx\Core;

/**
 * Class Optional
 * @package Lynx\Core
 */
class Optional implements OptionalInterface
{
	private static $empty;

	/**
	 * Returns an optional that is always empty and does not contain a value.
	 *
	 * @return Optional
	 */
	public static final function empty(): Optional
	{
		if (self::$empty === null)
		{
			self::$empty = new Optional(null);
		}

		return self::$empty;
	}

	/**
	 * Creates a new optional from an initialized element. If the element is null, an exception is thrown.
	 * If the element already is an optional, that optional is returned to avoid wrapping optionals.
	 *
	 * @param $element
	 * @return Optional
	 */
	public static function of($element): Optional
	{
		if ($element === null)
		{
			throw new \InvalidArgumentException('The given element cannot be null.');
		}

		if ($element instanceof Optional)
		{
			return $element;
		}

		return new Optional($element);
	}

	/**
	 * Creates a new optional from an element. The element may be null, in which case the empty
	 * optional as returned from Optional::empty() is returned. If the element already is an optional,
	 * that optional is returned to avoid wrapping optionals.
	 *
	 * @param $element
	 * @return Optional
	 */
	public static function ofNullable($element): Optional
	{
		if ($element === null)
		{
			return self::empty();
		}

		if ($element instanceof Optional)
		{
			return $element;
		}

		return new Optional($element);
	}

	protected $element;

	private function __construct($element)
	{
		$this->element = $element;
	}

	/**
	 * Returns true if this optional holds a non-null value, false otherwise.
	 *
	 * @return bool
	 */
	public function isPresent(): bool
	{
		return $this->element !== null;
	}

	/**
	 * Calls a consumer function if this optional holds a value.
	 *
	 * Hint: The return type is always Optional or descendant, but PHP does not support covariant return types yet.
	 *
	 * @param callable $consumer
	 * @return Optional|OptionalInterface
	 */
	public function ifPresent(callable $consumer): OptionalInterface
	{
		if ($this->isPresent())
		{
			call_user_func($consumer, $this->element);
		}

		return $this;
	}

	/**
	 * Returns the value contained inside this optional. If there is no value stored in this optional,
	 * throws a LogicException instead.
	 *
	 * @return mixed
	 */
	public function get()
	{
		if ($this->element === null)
		{
			throw new \LogicException('The optional element does not exist and cannot be retrieved.');
		}

		return $this->element;
	}

	/**
	 * Calls a selector function if this optional holds a value. The value returned from the
	 * selector function is wrapped in a optional and returned.
	 *
	 * Hint: The return type is always Optional or descendant, but PHP does not support covariant return types yet.
	 *
	 * @param callable $selector
	 * @return Optional|OptionalInterface
	 */
	public function map(callable $selector): OptionalInterface
	{
		if (!$this->isPresent())
		{
			return self::empty();
		}

		return static::ofNullable(call_user_func($selector, $this->element));
	}

	/**
	 * Calls a predicate function if this optional holds a value. If the predicate function
	 * returns true, this optional object is returned, otherwise an empty optional is returned.
	 *
	 * Hint: The return type is always Optional or descendant, but PHP does not support covariant return types yet.
	 *
	 * @param callable $predicate
	 * @return Optional|OptionalInterface
	 */
	public function filter(callable $predicate): OptionalInterface
	{
		if (!$this->isPresent())
		{
			return self::empty();
		}

		if (!call_user_func($predicate, $this->element))
		{
			return self::empty();
		}

		return $this;
	}

	/**
	 * If this optional holds a value, returns this value, otherwise returns the argument value.
	 *
	 * @param $value
	 * @return mixed
	 */
	public function orElse($value)
	{
		if (!$this->isPresent())
		{
			return $value;
		}

		return $this->element;
	}

	/**
	 * If this optional holds a value, returns this value, otherwise calls the supplier function and
	 * returns the return value from the supplier function. Use this over Optional::orElse() for
	 * values that are difficult to compute for lazy evaluation.
	 *
	 * @param callable $supplier
	 * @return mixed
	 */
	public function orElseGet(callable $supplier)
	{
		if (!$this->isPresent())
		{
			return call_user_func($supplier);
		}

		return $this->element;
	}

	/**
	 * If this optional holds a value, returns this value, otherwise calls the supplier function and
	 * throws the returned exception. If the supplier function does not return an exception object, throws
	 * a LogicException instead.
	 *
	 * @param callable $supplier
	 * @return mixed
	 * @throws \Throwable
	 */
	public function orElseThrow(callable $supplier)
	{
		if (!$this->isPresent())
		{
			$ex = call_user_func($supplier);
			if (!($ex instanceof \Throwable))
			{
				throw new \LogicException('The supplied error object must be throwable.');
			}

			throw $ex;
		}

		return $this->element;
	}
}
