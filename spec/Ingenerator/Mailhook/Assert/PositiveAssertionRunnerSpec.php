<?php
/**
 * Defines PositiveAssertionRunnerSpec - specifications for Ingenerator\Mailhook\Assert\PositiveAssertionRunner
 *
 * @copyright  2014 inGenerator Ltd
 * @licence    BSD
 */

namespace spec\Ingenerator\Mailhook\Assert;

use Ingenerator\Mailhook\EmailAssertionFailedException;
use PhpSpec\Exception\Example\FailureException;
use Prophecy\Argument;

/**
 *
 * @see Ingenerator\Mailhook\Assert\PositiveAssertionRunner
 */
class PositiveAssertionRunnerSpec extends AssertionRunnerBehaviour
{
    /**
     * Use $this->subject to get proper type hinting for the subject class
     * @var \Ingenerator\Mailhook\Assert\PositiveAssertionRunner
     */
	protected $subject;

	function it_is_initializable()
    {
		$this->subject->shouldHaveType('Ingenerator\Mailhook\Assert\PositiveAssertionRunner');
	}

	/**
	 * @param \Ingenerator\Mailhook\EmailListFilterer $filterer
	 *
	 * @throws \PhpSpec\Exception\Example\FailureException
	 */
	function it_throws_if_no_matching_emails($filterer)
	{
		$filterer->filterEmails(Argument::any(), Argument::any())->willReturn(array());
		try {
			$this->subject->assert();
			throw new FailureException("Expected EmailAssertionFailedException");
		} catch (EmailAssertionFailedException $e) {
			// Expected
		}
	}

	/**
	 * @param \Ingenerator\Mailhook\EmailListFilterer $filterer
	 * @param \Ingenerator\Mailhook\Email             $email_1
	 * @param \Ingenerator\Mailhook\Email             $email_2
	 */
	function it_returns_matching_emails_on_success($filterer, $email_1, $email_2)
	{
		$filterer->filterEmails(Argument::any(), Argument::any())->willReturn(array($email_1, $email_2));
		$this->subject->assert()->shouldBe(array($email_1, $email_2));
	}

}
