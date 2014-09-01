<?php
/**
 * Defines Ingenerator\Mailhook\Matcher\AnyEmailMatcher
 *
 * @copyright  2014 inGenerator Ltd
 * @licence    BSD
 */

namespace Ingenerator\Mailhook\Matcher;
use Ingenerator\Mailhook\Email;
use Ingenerator\Mailhook\EmailMatcher;

/**
 * Matches any email
 *
 * @package Ingenerator\Mailhook\Matcher
 * @see     spec\Ingenerator\Mailhook\Matcher\AnyEmailMatcherSpec
 */
class AnyEmailMatcher implements EmailMatcher {

	/**
	 * Test if an email matches the conditions
	 *
	 * @param Email $email
	 *
	 * @return bool
	 */
	public function matches(Email $email)
	{
		return TRUE;
	}

	/**
	 * Describe the matcher for use in exceptions etc
	 *
	 * @return string
	 */
	public function __toString()
	{
		return 'Any Email';
	}

}
