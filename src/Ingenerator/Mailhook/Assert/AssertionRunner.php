<?php
/**
 * Defines Ingenerator\Mailhook\Assert\AssertionRunner
 *
 * @copyright  2014 inGenerator Ltd
 * @licence    BSD
 */

namespace Ingenerator\Mailhook\Assert;

use Ingenerator\Mailhook\Email;
use Ingenerator\Mailhook\EmailListFilterer;
use Ingenerator\Mailhook\EmailMatcher;
use Ingenerator\Mailhook\Mailhook;

abstract class AssertionRunner {

	/**
	 * @var \Ingenerator\Mailhook\EmailListFilterer
	 */
	protected $filterer;
	/**
	 * @var \Ingenerator\Mailhook\Mailhook
	 */
	protected $mailhook;

	/**
	 * @param \Ingenerator\Mailhook\Mailhook          $mailhook
	 * @param \Ingenerator\Mailhook\EmailListFilterer $filterer
	 */
	public function __construct(Mailhook $mailhook, EmailListFilterer $filterer)
	{
		$this->mailhook = $mailhook;
		$this->filterer = $filterer;
	}

	/**
	 * Run assertions and throw if they do not pass
	 *
	 * @param EmailMatcher[] $matchers
	 *
	 * @return \Ingenerator\Mailhook\Email[] emails that matched the assertion
	 */
	public function assert($matchers = array())
	{
		$this->mailhook->refresh();
		$mails = $this->filterer->filterEmails($this->mailhook->getEmails(), $matchers);
		$this->do_assert($mails, $matchers);

		return $mails;
	}

	/**
	 * @param Email[]        $matched_mails
	 * @param EmailMatcher[] $matchers
	 *
	 * @return mixed
	 */
	abstract protected function do_assert($matched_mails, $matchers);
}
