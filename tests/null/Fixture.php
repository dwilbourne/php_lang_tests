<?php

/**
 * @author: Doug Wilbourne (dougwilbourne@gmail.com)
 */
declare (strict_types=1);


namespace tests\null;


/**
 * Class Fixture
 */
class Fixture
{
	protected ? string $foo;

	/**
	 * @param string|null $foo
	 */
	public function setFoo(?string $foo): void
	{
		$this->foo = $foo;
	}

	/**
	 * @return string|null
	 */
	public function getFoo(): ?string
	{
		return $this->foo;
	}

}