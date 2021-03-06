<?php
/**
 * Defines AnyEmailMatcherSpec - specifications for Ingenerator\Mailhook\Matcher\AnyEmailMatcher
 *
 * @copyright  2014 inGenerator Ltd
 * @licence    BSD
 */

namespace spec\Ingenerator\Mailhook\Matcher;

use Ingenerator\Mailhook\Email;
use Prophecy\Argument;
use spec\ObjectBehavior;

/**
 *
 * @see Ingenerator\Mailhook\Matcher\AnyEmailMatcher
 */
class AnyEmailMatcherSpec extends EmailMatcherBehaviour
{
    /**
     * Use $this->subject to get proper type hinting for the subject class
     * @var \Ingenerator\Mailhook\Matcher\AnyEmailMatcher
     */
	protected $subject;

	function it_is_initializable()
    {
		$this->subject->shouldHaveType('Ingenerator\Mailhook\Matcher\AnyEmailMatcher');
	}

	function it_describes_itself_as_an_any_matcher()
	{
		$this->subject->__toString()->shouldBe('Any Email');
	}

	/**
	 * @param \Ingenerator\Mailhook\Email $email
	 */
	function it_matches_any_email($email)
	{
        $email->beADoubleOf(Email::class);
		$this->subject->matches($email)->shouldBe(TRUE);
	}


}
