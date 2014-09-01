<?php
/**
 * Defines Ingenerator\Mailhook\Assert\PositiveAssertionRunner
 *
 * @copyright  2014 inGenerator Ltd
 * @licence    BSD
 */

namespace Ingenerator\Mailhook\Assert;

use Ingenerator\Mailhook\Email;
use Ingenerator\Mailhook\EmailAssertionFailedException;
use Ingenerator\Mailhook\EmailMatcher;

/**
 * Runs a positive assertion - one that throws if no emails match the provided criteria
 *
 * @package Ingenerator\Mailhook\Assert
 * @see     spec\Ingenerator\Mailhook\Assert\PositiveAssertionRunnerSpec
 */
class PositiveAssertionRunner extends AssertionRunner {


	/**
	 * @param Email[]        $matched_mails
	 * @param EmailMatcher[] $matchers
	 *
	 * @return void
	 * @throws \Ingenerator\Mailhook\EmailAssertionFailedException
	 */
	protected function do_assert($matched_mails, $matchers)
	{
		if ( ! $matched_mails)
		{
			throw new EmailAssertionFailedException('No emails matched criteria', $matchers);
		}
	}
}
