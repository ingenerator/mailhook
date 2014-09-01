<?php
/**
 * Defines Ingenerator\Mailhook\Matcher\EmailWithLinkMatcher
 *
 * @copyright  2014 inGenerator Ltd
 * @licence    BSD
 */


namespace Ingenerator\Mailhook\Matcher;
use Ingenerator\Mailhook\Email;
use Ingenerator\Mailhook\EmailMatcher;

/**
 * Matches an email containing a link - optionally with a particular URL pattern
 *
 * @package Ingenerator\Mailhook\Matcher
 * @see     spec\Ingenerator\Mailhook\Matcher\EmailWithLinkMatcherSpec
 */
class EmailWithLinkMatcher implements EmailMatcher {

	/**
	 * @var string
	 */
	protected $url_pattern;

	/**
	 * @param string $url_pattern
	 */
	public function __construct($url_pattern = NULL)
	{
		$this->url_pattern = $url_pattern;
	}

	/**
	 * {@inheritdoc}
	 */
	public function matches(Email $email)
	{
		if ($this->url_pattern) {
			return (bool) $email->getLinksMatching($this->url_pattern);
		} else {
			return (bool) $email->getLinks();
		}
	}

	/**
	 * {@inheritdoc}
	 */
	public function __toString()
	{
		if ($this->url_pattern)
		{
			return sprintf('With link matching "%s"', $this->url_pattern);
		} else {
			return 'With any link';
		}
	}

}
