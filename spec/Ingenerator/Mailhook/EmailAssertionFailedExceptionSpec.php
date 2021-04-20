<?php
/**
 * Defines EmailAssertionFailedExceptionSpec - specifications for Ingenerator\Mailhook\EmailAssertionFailedException
 *
 * @copyright  2014 inGenerator Ltd
 * @licence    BSD
 */

namespace spec\Ingenerator\Mailhook;

use Ingenerator\Mailhook\EmailMatcher;
use Prophecy\Argument;
use spec\ObjectBehavior;

/**
 *
 * @see Ingenerator\Mailhook\EmailAssertionFailedException
 */
class EmailAssertionFailedExceptionSpec extends ObjectBehavior
{
    /**
     * Use $this->subject to get proper type hinting for the subject class
     * @var \Ingenerator\Mailhook\EmailAssertionFailedException
     */
	protected $subject;

	function let()
	{
		$this->beConstructedWith('Message', array());
	}

	function it_is_initializable()
    {
		$this->subject->shouldHaveType('Ingenerator\Mailhook\EmailAssertionFailedException');
	}

	function it_includes_message_in_message()
	{
		$this->beConstructedWith('Problem email', array());
		$this->subject->getMessage()->shouldMatch('/^Problem email/');
	}

	function it_describes_when_no_matchers()
	{
		$this->beConstructedWith('Problem email', array());
		$this->subject->getMessage()->shouldMatch('/\nMatchers: none$/');
	}

	/**
	 * @param \Ingenerator\Mailhook\EmailMatcher $matcher1
	 * @param \Ingenerator\Mailhook\EmailMatcher $matcher2
	 */
	function it_describes_matchers_in_message($matcher1, $matcher2)
	{
        $matcher1->beADoubleOf(EmailMatcher::class);
        $matcher2->beADoubleOf(EmailMatcher::class);
		$matcher1->__toString()->willReturn('Containing "here"');
		$matcher2->__toString()->willReturn('To test@ingenerator.com');

		$this->beConstructedWith('Problem email', array($matcher1, $matcher2));
		$this->subject->getMessage()->shouldMatch('/\nMatchers: Containing "here", To test@ingenerator.com$/');
	}


}
