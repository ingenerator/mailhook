<?php
/**
 * Defines NegativeAssertionRunnerSpec - specifications for Ingenerator\Mailhook\Assert\NegativeAssertionRunner
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
 * @see Ingenerator\Mailhook\Assert\NegativeAssertionRunner
 */
class NegativeAssertionRunnerSpec extends AssertionRunnerBehaviour
{
    /**
     * Use $this->subject to get proper type hinting for the subject class
     * @var \Ingenerator\Mailhook\Assert\NegativeAssertionRunner
     */
	protected $subject;

	function it_is_initializable()
    {
		$this->subject->shouldHaveType('Ingenerator\Mailhook\Assert\NegativeAssertionRunner');
	}

	/**
	 * @param \Ingenerator\Mailhook\EmailListFilterer $filterer
	 */
	function it_returns_empty_array_if_no_matching_emails($filterer)
	{
		$filterer->filterEmails(Argument::any(), Argument::any())->willReturn(array());
		$this->subject->assert()->shouldBe(array());
	}

	/**
	 * @param \Ingenerator\Mailhook\EmailListFilterer $filterer
	 * @param \Ingenerator\Mailhook\Email             $email_1
	 * @param \Ingenerator\Mailhook\Email             $email_2
	 *
	 * @throws \PhpSpec\Exception\Example\FailureException
	 */
	function it_throws_if_any_matching_emails($filterer, $email_1, $email_2)
	{
		$filterer->filterEmails(Argument::any(), Argument::any())->willReturn(array($email_1, $email_2));
		try {
			$this->subject->assert();
			throw new FailureException("Expected EmailAssertionFailedException");
		} catch (EmailAssertionFailedException $e) {
			// Expected
		}
	}

}
