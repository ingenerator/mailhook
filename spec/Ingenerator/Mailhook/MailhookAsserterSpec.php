<?php
/**
 * Defines MailhookAsserterSpec - specifications for Ingenerator\Mailhook\MailhookAsserter
 *
 * @copyright  2014 inGenerator Ltd
 * @licence    BSD
 */

namespace spec\Ingenerator\Mailhook;

use Prophecy\Argument;
use spec\ObjectBehavior;

/**
 *
 * @see Ingenerator\Mailhook\MailhookAsserter
 */
class MailhookAsserterSpec extends ObjectBehavior
{
    /**
     * Use $this->subject to get proper type hinting for the subject class
     * @var \Ingenerator\Mailhook\MailhookAsserter
     */
	protected $subject;

	/**
	 * @param \Ingenerator\Mailhook\Assert\PositiveAssertionRunner $positive_runner
	 * @param \Ingenerator\Mailhook\Assert\NegativeAssertionRunner $negative_runner
	 */
	function let($positive_runner, $negative_runner)
	{
		$this->beConstructedWith($positive_runner, $negative_runner);
	}

	function it_is_initializable()
    {
		$this->subject->shouldHaveType('Ingenerator\Mailhook\MailhookAsserter');
	}

	/**
	 * @param \Ingenerator\Mailhook\Assert\PositiveAssertionRunner $positive_runner
	 * @param \Ingenerator\Mailhook\EmailMatcher $matcher1
	 * @param \Ingenerator\Mailhook\EmailMatcher $matcher2
	 * @param \Ingenerator\Mailhook\Email        $email
	 */
	function it_runs_positive_assertions_with_matchers($positive_runner, $matcher1, $matcher2, $email)
	{
		$positive_runner->assert(Argument::any())->willReturn(array($email));

		$this->subject->emailsMatching($matcher1, $matcher2)->shouldBe(array($email));
		$positive_runner->assert(array($matcher1, $matcher2))->shouldHaveBeenCalled();
	}

	/**
	 * @param \Ingenerator\Mailhook\Assert\NegativeAssertionRunner $negative_runner
	 * @param \Ingenerator\Mailhook\EmailMatcher $matcher1
	 * @param \Ingenerator\Mailhook\EmailMatcher $matcher2
	 */
	function it_runs_negative_assertions_with_matchers($negative_runner, $matcher1, $matcher2)
	{
		$negative_runner->assert(Argument::any())->willReturn(array());

		$this->subject->noEmailMatching($matcher1, $matcher2)->shouldBe(NULL);
		$negative_runner->assert(array($matcher1, $matcher2))->shouldHaveBeenCalled();
	}

	/**
	 * @param \Ingenerator\Mailhook\Assert\PositiveAssertionRunner $positive_runner
	 * @param \Ingenerator\Mailhook\EmailMatcher $matcher
	 * @param \Ingenerator\Mailhook\Email        $email1
	 * @param \Ingenerator\Mailhook\Email        $email2
	 */
	function it_runs_positive_assertions_and_returns_first_matching($positive_runner, $matcher, $email1, $email2)
	{
		$positive_runner->assert(array($matcher))->willReturn(array($email1, $email2));

		$this->subject->firstEmailMatching($matcher)->shouldBe($email1);
	}

}
