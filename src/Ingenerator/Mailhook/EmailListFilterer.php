<?php
/**
 * Defines Ingenerator\Mailhook\EmailListFilterer
 *
 * @copyright  2014 inGenerator Ltd
 * @licence    BSD
 */

namespace Ingenerator\Mailhook;

/**
 * Filters a list of emails to find all the ones matching a given set of matchers
 *
 * @package Ingenerator\Mailhook
 * @see     spec\Ingenerator\Mailhook\EmailListFiltererSpec
 */
class EmailListFilterer {

	/**
	 * @var EmailMatcher[]
	 */
	protected $matchers;

	/**
	 * @param Email[]        $emails
	 * @param EmailMatcher[] $matchers
	 *
	 * @return Email[]
	 */
	public function filterEmails($emails, $matchers)
	{
		if ($emails and $matchers)
		{
			$this->matchers = $matchers;

			return array_filter($emails, array($this, 'matchesAllMatchers'));
		}
		else
		{
			return array();
		}
	}

	/**
	 * @param Email $email
	 *
	 * @return bool
	 */
	protected function matchesAllMatchers(Email $email)
	{
		foreach ($this->matchers as $matcher)
		{
			if (!$matcher->matches($email))
			{
				return FALSE;
			}
		}

		return TRUE;
	}
}
