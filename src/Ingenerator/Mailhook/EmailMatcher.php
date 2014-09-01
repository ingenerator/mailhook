<?php
/**
 * Defines Ingenerator\Mailhook\EmailMatcher
 *
 * @copyright  2014 inGenerator Ltd
 * @licence    BSD
 */

namespace Ingenerator\Mailhook;


interface EmailMatcher {

	/**
	 * Test if an email matches the conditions
	 *
	 * @param Email $email
	 *
	 * @return bool
	 */
	public function matches(Email $email);

	/**
	 * Describe the matcher for use in exceptions etc
	 *
	 * @return string
	 */
	public function __toString();

} 
