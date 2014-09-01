<?php
/**
 * Defines Ingenerator\Mailhook\Matcher\EmailContainingTextMatcher
 *
 * @copyright  2014 inGenerator Ltd
 * @licence    BSD
 */

namespace Ingenerator\Mailhook\Matcher;

use Ingenerator\Mailhook\Email;
use Ingenerator\Mailhook\EmailMatcher;

/**
 * Matches an email containing particular body text
 *
 * @package Ingenerator\Mailhook\Matcher
 * @see     spec\Ingenerator\Mailhook\Matcher\EmailContainingTextMatcherSpec
 */
class EmailContainingTextMatcher implements EmailMatcher {
	/**
	 * @var string
	 */
	protected $text;

	/**
	 * @param string $text
	 */
	public function __construct($text)
	{
		$this->text = $text;
	}

	/**
	 * {@inheritdoc}
	 */
	public function __toString()
	{
		return sprintf('With text "%s"', $this->text);
	}

	/**
	 * {@inheritdoc}
	 */
	public function matches(Email $email)
	{
		return (strpos($email->getContent(), $this->text) !== FALSE);
	}
}
