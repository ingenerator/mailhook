<?php
/**
 * Defines common specs for all email matchers
 *
 * @copyright  2014 inGenerator Ltd
 * @licence    BSD
 */

namespace spec\Ingenerator\Mailhook\Matcher;


use spec\ObjectBehavior;

class EmailMatcherBehaviour extends ObjectBehavior {

	/**
	 * Use $this->subject to get proper type hinting for the subject class
	 * @var \Ingenerator\Mailhook\EmailMatcher
	 */
	protected $subject;

	function it_is_an_email_matcher()
	{
		$this->subject->shouldHaveType('Ingenerator\Mailhook\EmailMatcher');
	}


} 
