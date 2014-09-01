<?php
/**
 * Defines Ingenerator\Mailhook\Assert\NegativeAssertionRunner
 *
 * @copyright  2014 inGenerator Ltd
 * @licence    BSD
 */


namespace Ingenerator\Mailhook\Assert;

use Ingenerator\Mailhook\Email;
use Ingenerator\Mailhook\EmailAssertionFailedException;
use Ingenerator\Mailhook\EmailMatcher;

/**
 * Runs a negative assertion - throws if any mails match the criteria
 *
 * @package Ingenerator\Mailhook\Assert
 * @see     spec\Ingenerator\Mailhook\Assert\NegativeAssertionRunnerSpec
 */
class NegativeAssertionRunner extends AssertionRunner {

	/**
	 * @param Email[]        $matched_mails
	 * @param EmailMatcher[] $matchers
	 *
	 * @throws \Ingenerator\Mailhook\EmailAssertionFailedException
	 * @return void
	 */
	protected function do_assert($matched_mails, $matchers)
	{
		if ($matched_mails)
		{
			throw new EmailAssertionFailedException('Unexpected matching emails', $matchers);
		}
	}

}
