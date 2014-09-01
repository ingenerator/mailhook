<?php
/**
 * Defines Ingenerator\Mailhook\Matcher\EmailSentToMatcher
 *
 * @copyright  2014 inGenerator Ltd
 * @licence    BSD
 */

namespace Ingenerator\Mailhook\Matcher;

use Ingenerator\Mailhook\Email;
use Ingenerator\Mailhook\EmailMatcher;

/**
 * Matches the recipient email address
 *
 * @package Ingenerator\Mailhook\Matcher
 * @see     spec\Ingenerator\Mailhook\Matcher\EmailSentToMatcherSpec
 */
class EmailSentToMatcher implements EmailMatcher {

	/**
	 * @var string
	 */
	protected  $recipient;

	/**
	 * @param string $recipient
	 */
	public function __construct($recipient)
	{
		$this->recipient = $recipient;
	}

	/**
	 * {@inheritdoc}
	 */
	public function __toString()
	{
		return sprintf('To "%s"', $this->recipient);
	}

	/**
	 * {@inheritdoc}
	 */
	public function matches(Email $email)
	{
		return ($email->getTo() === $this->recipient);
	}
}
