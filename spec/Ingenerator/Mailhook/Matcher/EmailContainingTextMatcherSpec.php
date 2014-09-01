<?php
/**
 * Defines EmailContainingTextMatcherSpec - specifications for Ingenerator\Mailhook\Matcher\EmailContainingTextMatcher
 *
 * @copyright  2014 inGenerator Ltd
 * @licence    BSD
 */

namespace spec\Ingenerator\Mailhook\Matcher;

use Prophecy\Argument;
use spec\ObjectBehavior;

/**
 *
 * @see Ingenerator\Mailhook\Matcher\EmailContainingTextMatcher
 */
class EmailContainingTextMatcherSpec extends EmailMatcherBehaviour
{
    /**
     * Use $this->subject to get proper type hinting for the subject class
     * @var \Ingenerator\Mailhook\Matcher\EmailContainingTextMatcher
     */
	protected $subject;

	function let()
	{
		$this->subject->beConstructedWith('text');
	}

	function it_is_initializable()
    {
		$this->subject->shouldHaveType('Ingenerator\Mailhook\Matcher\EmailContainingTextMatcher');
	}

	/**
	 * @param \Ingenerator\Mailhook\Email $email
	 */
	function it_does_not_match_email_with_empty_text($email)
	{
		$email->getContent()->willReturn('');
		$this->subject->matches($email)->shouldBe(FALSE);

		$email->getContent()->willReturn(NULL);
		$this->subject->matches($email)->shouldBe(FALSE);
	}

	/**
	 * @param \Ingenerator\Mailhook\Email $email
	 */
	function it_matches_email_beginning_with_text($email)
	{
		$this->beConstructedWith('Some text');
		$email->getContent()->willReturn('Some text that matches');
		$this->subject->matches($email)->shouldBe(TRUE);
	}

	/**
	 * @param \Ingenerator\Mailhook\Email $email
	 */
	function it_matches_email_containing_text($email)
	{
		$this->beConstructedWith('Some text');
		$email->getContent()->willReturn('This email has Some text that matches');
		$this->subject->matches($email)->shouldBe(TRUE);
	}

	function it_describes_with_search_text()
	{
		$this->beConstructedWith('Some text');
		$this->subject->__toString()->shouldBe('With text "Some text"');
	}

}
