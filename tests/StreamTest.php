<?php

namespace Lynx\Core;

use PHPUnit\Framework\TestCase;

class StreamTest extends TestCase
{
	public function testAcceptTraversable()
	{
		Stream::over(new \ArrayIterator([1]));
		Stream::over([1]);
		$a = Stream::over(function ()
		{
			yield 1;
		});
		$b = Stream::over($a);

		$this->assertEquals($a, $b);
	}

	public function testRejectOther()
	{
		try
		{
			/** @noinspection PhpParamsInspection */
			Stream::over('string');
			$this->fail('Stream::over(string) did not throw an exception.');
		}
		catch (\InvalidArgumentException $ex)
		{
			$this->assertEquals('The source must be traversable.', $ex->getMessage());
		}
	}

	public function testCount()
	{
		$this->assertEquals(0, Stream::over([])->count());
		$this->assertEquals(1, Stream::over([1])->count());
		$this->assertEquals(1, Stream::over([1, 2, 3])->count(function ($n)
		{
			return $n < 2;
		}));
	}

	public function testEach()
	{
		$count = 0;
		Stream::over([1, 2, 3])->each(function ($n) use (&$count)
		{
			$count += 1;
			$this->assertEquals($count, $n);
		});

		$this->assertEquals(3, $count);
	}

	public function testMap()
	{
		$count = 0;
		$array = Stream::over([1, 2, 3])->map(function ($n) use (&$count)
		{
			$count += 1;
			$this->assertEquals($count, $n);
			return $n * $n;
		})->toArray();

		$this->assertEquals(3, $count);
		$this->assertEquals([1, 4, 9], $array);
	}

	public function testMapFlat()
	{
		$count = 0;
		$array = Stream::over([1, 2, 3])->mapFlat(function ($n) use (&$count)
		{
			$count += 1;
			$this->assertEquals($count, $n);
			return [$n, $n * $n];
		})->toArray();

		$this->assertEquals(3, $count);
		$this->assertEquals([1, 1, 2, 4, 3, 9], $array);
	}

	public function testFilter()
	{
		$count = 0;
		$array = Stream::over([1, 2, 3])->filter(function ($n) use (&$count)
		{
			$count += 1;
			$this->assertEquals($count, $n);
			return $n % 2 != 0;
		})->toArray();

		$this->assertEquals(3, $count);
		$this->assertEquals([1, 3], $array);
	}

	public function testSum()
	{
		$opt = Stream::over([1, 2, 3])->sum();
		$this->assertEquals(6, $opt->get());

		$opt = Stream::over([1, 2, 3])->sum(function ($n)
		{
			return $n * $n;
		});
		$this->assertEquals(14, $opt->get());
	}

	public function testMin()
	{
		$opt = Stream::over([1, 2, 3])->min();
		$this->assertEquals(1, $opt->get());

		$opt = Stream::over([1, 2, 3])->min(function ($n)
		{
			return $n - 2;
		});
		$this->assertEquals(-1, $opt->get());
	}

	public function testMax()
	{
		$opt = Stream::over([1, 2, 3])->max();
		$this->assertEquals(3, $opt->get());

		$opt = Stream::over([1, 2, 3])->max(function ($n)
		{
			return $n + 2;
		});
		$this->assertEquals(5, $opt->get());
	}

	public function testAverage()
	{
		$opt = Stream::over([1, 2, 6])->average();
		$this->assertEquals(3, $opt->get());

		$opt = Stream::over([1, 2, 6])->average(function ($n)
		{
			return $n + 3;
		});
		$this->assertEquals(6, $opt->get());
	}

	public function testMedian()
	{
		$opt = Stream::over([1, 2, 6])->median();
		$this->assertEquals(2, $opt->get());

		$opt = Stream::over([1, 2, 6, 7])->median();
		$this->assertEquals(4, $opt->get());

		$opt = Stream::over([6, 2, 1, 7])->median();
		$this->assertEquals(4, $opt->get());

		$opt = Stream::over([1, 2, 6])->median(function ($n)
		{
			return $n + 3;
		});
		$this->assertEquals(5, $opt->get());

		$opt = Stream::over([1, 2, 6, 7])->median(function ($n)
		{
			return $n + 3;
		});
		$this->assertEquals(7, $opt->get());

		$opt = Stream::over([6, 2, 1, 7])->median(function ($n)
		{
			return $n + 3;
		});
		$this->assertEquals(7, $opt->get());
	}

	public function testReduce()
	{
		$opt = Stream::over([1, 2, 3])->reduce(function ($a, $n)
		{
			return $a + $n + 1;
		});

		// Hint: The reduce function is only executed twice.
		$this->assertEquals(8, $opt->get());
	}

	public function testReduceWith()
	{
		$opt = Stream::over([1, 2, 3])->reduceWith(10, function ($a, $n)
		{
			return $a + $n + 1;
		});

		$this->assertEquals(19, $opt->get());
	}

	public function testTake()
	{
		$array = Stream::over([1, 2, 3])->take(2)->toArray();
		$this->assertEquals([1, 2], $array);

		$array = Stream::over([1, 2, 3])->take(10)->toArray();
		$this->assertEquals([1, 2, 3], $array);
	}

	public function testTakeWhile()
	{
		$array = Stream::over([1, 2, 3])->takeWhile(function ($n)
		{
			return $n < 3;
		})->toArray();
		$this->assertEquals([1, 2], $array);
	}

	public function testSkip()
	{
		$array = Stream::over([1, 2, 3])->skip(2)->toArray();
		$this->assertEquals([3], $array);

		$array = Stream::over([1, 2, 3])->skip(10)->toArray();
		$this->assertEquals([], $array);
	}

	public function testSkipWhile()
	{
		$array = Stream::over([1, 2, 3])->skipWhile(function ($n)
		{
			return $n < 3;
		})->toArray();
		$this->assertEquals([3], $array);
	}

	public function testConcat()
	{
		$stream = Stream::over([5, 6]);
		$array = Stream::over([1, 2])->concat([3, 4], $stream)->toArray();
		$this->assertEquals([1, 2, 3, 4, 5, 6], $array);
	}

	public function testSort()
	{
		$array = Stream::over([2, 3, 1])->sort()->toArray();
		$this->assertEquals([1, 2, 3], $array);

		$array = Stream::over([2, 3, 1])->sortDescending()->toArray();
		$this->assertEquals([3, 2, 1], $array);
	}

	public function testSortBy()
	{
		$array = Stream::over([2, 3, 1])->sortBy(function ($n)
		{
			return $n;
		})->toArray();
		$this->assertEquals([1, 2, 3], $array);

		$array = Stream::over([2, 3, 1])->sortByDescending(function ($n)
		{
			return $n;
		})->toArray();
		$this->assertEquals([3, 2, 1], $array);
	}

	public function testReverse()
	{
		$array = Stream::over([1, 2, 3])->reverse()->toArray();
		$this->assertEquals([3, 2, 1], $array);
	}

	public function testGroupBy()
	{
		$array = Stream::over([
			['a' => 1, 'b' => 1],
			['a' => 1, 'b' => 2],
			['a' => 1, 'b' => 3],
			['a' => 2, 'b' => 1],
			['a' => 2, 'b' => 2],
		])->groupBy(function ($n)
		{
			return $n['a'];
		})->toArray();

		$this->assertEquals([
			[
				'key' => 1,
				'values' => [
					['a' => 1, 'b' => 1],
					['a' => 1, 'b' => 2],
					['a' => 1, 'b' => 3],
				]
			],
			[
				'key' => 2,
				'values' => [
					['a' => 2, 'b' => 1],
					['a' => 2, 'b' => 2],
				]
			]
		], $array);
	}

	public function testDistinct()
	{
		$array = Stream::over([1, 1, 2, 2, 3])->distinct()->toArray();
		$this->assertEquals([1, 2, 3], $array);
	}

	public function testFirst()
	{
		$opt = Stream::over([1, 2, 3])->first();
		$this->assertEquals(1, $opt->get());

		$opt = Stream::over([1, 2, 3])->first(function ($n)
		{
			return $n > 1;
		});
		$this->assertEquals(2, $opt->get());
	}

	public function testLast()
	{
		$opt = Stream::over([1, 2, 3])->last();
		$this->assertEquals(3, $opt->get());

		$opt = Stream::over([1, 2, 3])->last(function ($n)
		{
			return $n < 3;
		});
		$this->assertEquals(2, $opt->get());
	}

	public function testAny()
	{
		$this->assertTrue(Stream::over([1, 2, 3])->any(function ($n)
		{
			return $n > 1;
		}));

		$this->assertFalse(Stream::over([1, 2, 3])->any(function ($n)
		{
			return $n < 1;
		}));
	}

	public function testAll()
	{
		$this->assertTrue(Stream::over([1, 2, 3])->all(function ($n)
		{
			return $n > 0;
		}));

		$this->assertFalse(Stream::over([1, 2, 3])->all(function ($n)
		{
			return $n < 3;
		}));
	}

	public function testContains()
	{
		$this->assertTrue(Stream::over([1, 2, 3])->contains(1));
		$this->assertFalse(Stream::over([1, 2, 3])->contains(0));
	}

	public function testJoin()
	{
		$str = Stream::over(['A', 'B', 'C'])->join();
		$this->assertEquals('ABC', $str);

		$str = Stream::over(['A', 'B', 'C'])->join('+');
		$this->assertEquals('A+B+C', $str);
	}
}
