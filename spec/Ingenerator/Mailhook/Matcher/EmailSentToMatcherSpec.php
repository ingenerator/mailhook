<?php
/**
 * Defines EmailSentToMatcherSpec - specifications for Ingenerator\Mailhook\Matcher\EmailSentToMatcher
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
 * @see Ingenerator\Mailhook\Matcher\EmailSentToMatcher
 */
class EmailSentToMatcherSpec extends EmailMatcherBehaviour
{
    /**
     * Use $this->subject to get proper type hinting for the subject class
     * @var \Ingenerator\Mailhook\Matcher\EmailSentToMatcher
     */
	protected $subject;

	function let()
	{
		$this->beConstructedWith('test@ingenerator.com');
	}

	function it_is_initializable()
    {
		$this->subject->shouldHaveType('Ingenerator\Mailhook\Matcher\EmailSentToMatcher');
	}

	/**
	 * @param \Ingenerator\Mailhook\Email $email
	 */
	function it_does_not_match_email_sent_to_someone_else($email)
	{
	    $email->beADoubleOf(Email::class);
		$this->subject->beConstructedWith('test@ingenerator.com');
		$email->getTo()->willReturn('someoneelse@ingenerator.com');
		$this->subject->matches($email)->shouldBe(FALSE);
	}

	/**
	 * @param \Ingenerator\Mailhook\Email $email
	 */
	function it_matches_email_sent_to_recipient($email)
	{
        $email->beADoubleOf(Email::class);
		$this->subject->beConstructedWith('test@ingenerator.com');
		$email->getTo()->willReturn('test@ingenerator.com');
		$this->subject->matches($email)->shouldBe(TRUE);
	}

	function it_describes_with_requested_recipient()
	{
		$this->subject->beConstructedWith('test@ingenerator.com');
		$this->subject->__toString()->shouldBe('To "test@ingenerator.com"');
	}

}
