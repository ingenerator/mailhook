<?php
/**
 * Thrown when an email assertion does not match
 *
 * @author    Andrew Coulton <andrew@ingenerator.com>
 * @copyright 2014 inGenerator Ltd
 * @licence   BSD
 */

namespace Ingenerator\Mailhook;

/**
 * Thrown when an assertion fails
 *
 * @package Ingenerator\Mailhook
 */
class EmailAssertionFailedException extends \Exception {

	/**
	 * @param string         $message
	 * @param EmailMatcher[] $matchers
	 */
	public function __construct($message, $matchers)
	{
		$message .= $this->describeMatchers($matchers);
		parent::__construct($message);
	}

	/**
	 * @param EmailMatcher[] $matchers
	 *
	 * @return string
	 */
	protected function describeMatchers($matchers)
	{
		$message = "\nMatchers: ";
		if ( ! $matchers)
		{
			$matchers = array('none');
		}

		return $message.implode(', ', $matchers);
	}

} 
