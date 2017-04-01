<?php

namespace Lynx\Core;

use function foo\func;
use PHPUnit\Framework\TestCase;

class OptionalTest extends TestCase
{
	public function testEmpty()
	{
		$opt = Optional::empty();

		$this->assertFalse($opt->isPresent());
		$this->assertEquals($opt, Optional::empty());
	}

	public function testOf()
	{
		$optA = Optional::of(1);
		$optB = Optional::of($optA);

		$this->assertTrue($optA->isPresent());
		$this->assertEquals($optA, $optB);

		try
		{
			Optional::of(null);
			$this->fail('Optional::of(null) did not throw an exception.');
		}
		catch (\InvalidArgumentException $ex)
		{
			$this->assertEquals('The given element cannot be null.', $ex->getMessage());
		}
	}

	public function testOfNullable()
	{

		$optA = Optional::ofNullable(1);
		$optB = Optional::ofNullable($optA);
		$optC = Optional::ofNullable(null);

		$this->assertTrue($optA->isPresent());
		$this->assertFalse($optC->isPresent());
		$this->assertEquals($optA, $optB);
		$this->assertEquals(Optional::empty(), $optC);
	}

	public function testIsPresent()
	{
		$this->assertTrue(Optional::ofNullable(1)->isPresent());
		$this->assertFalse(Optional::ofNullable(null)->isPresent());
	}

	public function testIfPresent()
	{
		$done = false;
		Optional::ofNullable(1)->ifPresent(function ($n) use (&$done)
		{
			$done = true;
		});

		$this->assertTrue($done);

		Optional::ofNullable(null)->ifPresent(function ($n)
		{
			$this->fail('Optional->ifPresent() called without a value.');
		});
	}

	public function testGet()
	{
		$this->assertEquals(1, Optional::ofNullable(1)->get());

		try
		{
			Optional::ofNullable(null)->get();
			$this->fail('Optional->get() returned without a value.');
		}
		catch (\LogicException $ex)
		{
			$this->assertEquals('The optional element does not exist and cannot be retrieved.', $ex->getMessage());
		}
	}

	public function testMap()
	{
		$value = Optional::ofNullable(1)->map(function ($n)
		{
			return $n + 1;
		})->get();

		$this->assertEquals(2, $value);

		Optional::ofNullable(null)->map(function ($n)
		{
			$this->fail('Optional->map() called without a value.');
		});
	}

	public function testFilter()
	{
		$value = Optional::ofNullable(1)->filter(function ($n)
		{
			return $n > 0;
		})->get();

		$this->assertEquals(1, $value);

		$this->assertFalse(Optional::ofNullable(1)->filter(function ($n)
		{
			return $n > 1;
		})->isPresent());

		Optional::ofNullable(null)->filter(function ($n)
		{
			$this->fail('Optional->filter() called without a value.');
		});
	}

	public function testOrElse()
	{
		$value = Optional::ofNullable(1)->orElse(2);
		$this->assertEquals(1, $value);

		$value = Optional::ofNullable(null)->orElse(2);
		$this->assertEquals(2, $value);
	}

	public function testOrElseGet()
	{
		$value = Optional::ofNullable(1)->orElseGet(function ()
		{
			$this->fail('Optional->orElseGet() called with a value.');
		});
		$this->assertEquals(1, $value);

		$value = Optional::ofNullable(null)->orElseGet(function ()
		{
			return 2;
		});
		$this->assertEquals(2, $value);
	}

	public function testOrElseThrow()
	{
		$value = Optional::ofNullable(1)->orElseThrow(function ()
		{
			$this->fail('Optional->orElseThrow() called with a value.');
		});
		$this->assertEquals(1, $value);

		try
		{
			Optional::ofNullable(null)->orElseThrow(function ()
			{
				return new \BadFunctionCallException('foobar');
			});
			$this->fail('Optional->orElseThrow() did not throw without a value.');
		}
		catch (\BadFunctionCallException $ex)
		{
			$this->assertEquals('foobar', $ex->getMessage());
		}

		try
		{
			Optional::ofNullable(null)->orElseThrow(function ()
			{
				return 2;
			});
			$this->fail('Optional->orElseThrow() did not throw without a value.');
		}
		catch (\LogicException $ex)
		{
			$this->assertEquals('The supplied error object must be throwable.', $ex->getMessage());
		}
	}
}
