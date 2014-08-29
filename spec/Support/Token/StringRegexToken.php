<?php
/**
 * Matches an argument matching a regex
 *
 * @author    Andrew Coulton <andrew@ingenerator.com>
 * @copyright 2014 inGenerator Ltd
 * @licence   BSD
 */
namespace spec\Support\Token;

use Prophecy\Argument;

class StringRegexToken implements Argument\Token\TokenInterface {

	/**
	 * @var string
	 */
	protected $pattern;

	/**
	 * @param string $pattern
	 */
	public function __construct($pattern)
	{
		$this->pattern = $pattern;
	}

	/**
	 * Calculates token match score for provided argument.
	 *
	 * @param $argument
	 *
	 * @return bool|int
	 */
	public function scoreArgument($argument)
	{
		return preg_match($this->pattern, $argument) ? 10 : FALSE;
	}

	/**
	 * Returns true if this token prevents check of other tokens (is last one).
	 *
	 * @return bool|int
	 */
	public function isLast()
	{
		return FALSE;
	}

	/**
	 * Returns string representation for token.
	 *
	 * @return string
	 */
	public function __toString()
	{
		return sprintf('regex(%s)', $this->pattern);
	}

}
