<?php
/**
 * Defines Ingenerator\Mailhook\MailhookAsserter
 *
 * @copyright  2014 inGenerator Ltd
 * @licence    BSD
 */

namespace Ingenerator\Mailhook;

use Ingenerator\Mailhook\Assert\NegativeAssertionRunner;
use Ingenerator\Mailhook\Assert\PositiveAssertionRunner;

/**
 * Asserts that emails exist matching a
 *
 * @package Ingenerator\Mailhook
 * @see     spec\Ingenerator\Mailhook\MailhookAsserterSpec
 */
class MailhookAsserter {

	/**
	 * @var Assert\NegativeAssertionRunner
	 */
	protected $negative_runner;

	/**
	 * @var Assert\PositiveAssertionRunner
	 */
	protected $positive_runner;

	public function __construct(
		PositiveAssertionRunner $positive_runner,
		NegativeAssertionRunner $negative_runner
	)
	{
		$this->positive_runner = $positive_runner;
		$this->negative_runner = $negative_runner;
	}

	/**
	 * @param EmailMatcher $matcher,... Matchers to run
	 *
	 * @return Email[]
	 */
	public function emailsMatching($matcher = NULL)
	{
		$matchers = \func_get_args();

		return $this->positive_runner->assert($matchers);
	}

	/**
	 * @param EmailMatcher $matcher,... Matchers to run
	 *
	 * @return Email
	 */
	public function firstEmailMatching($matcher = NULL)
	{
		$matchers = \func_get_args();
		$emails   = $this->positive_runner->assert($matchers);

		return \array_shift($emails);
	}

	/**
	 * @param EmailMatcher $matcher,... Matchers to run
	 *
	 * @return null
	 */
	public function noEmailMatching($matcher = NULL)
	{
		$matchers = \func_get_args();

		return $this->negative_runner->assert($matchers) ? : NULL;
	}
}
