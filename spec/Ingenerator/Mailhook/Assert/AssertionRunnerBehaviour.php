<?php
/**
 * Defines common specifications for assertion runners
 *
 * @copyright  2014 inGenerator Ltd
 * @licence    BSD
 */

namespace spec\Ingenerator\Mailhook\Assert;

use Ingenerator\Mailhook\EmailAssertionFailedException;
use Ingenerator\Mailhook\EmailListFilterer;
use Ingenerator\Mailhook\EmailMatcher;
use Ingenerator\Mailhook\Mailhook;
use PhpSpec\Exception\Example\FailureException;
use Prophecy\Argument;
use spec\ObjectBehavior;

class AssertionRunnerBehaviour extends ObjectBehavior
{
	/**
	 * @param \Ingenerator\Mailhook\EmailListFilterer $filterer
	 * @param \Ingenerator\Mailhook\Mailhook          $mailhook
	 * @param \Ingenerator\Mailhook\Email             $email
	 */
	function it_filters_received_emails($filterer, $mailhook, $email)
	{
		$mailhook->getEmails()
		         ->willReturn(array($email));
		$this->try_assert_with();
		$filterer->filterEmails(array($email), Argument::any())
		         ->shouldHaveBeenCalled();
	}

	/**
	 * @param \Ingenerator\Mailhook\EmailListFilterer $filterer
	 * @param \Ingenerator\Mailhook\EmailMatcher      $matcher_1
	 * @param \Ingenerator\Mailhook\EmailMatcher      $matcher_2
	 * @param \Ingenerator\Mailhook\EmailMatcher      $matcher_3
	 */
	function it_filters_with_multiple_matchers($filterer, $matcher_1, $matcher_2, $matcher_3)
	{
		$this->try_assert_with(array($matcher_1, $matcher_2, $matcher_3));
		$filterer->filterEmails(Argument::any(), array($matcher_1, $matcher_2, $matcher_3))
		         ->shouldHaveBeenCalled();
	}

	/**
	 * @param \Ingenerator\Mailhook\EmailListFilterer $filterer
	 * @param \Ingenerator\Mailhook\EmailMatcher      $matcher
	 */
	function it_filters_with_one_matcher($filterer, $matcher)
	{
		$this->try_assert_with(array($matcher));
		$filterer->filterEmails(Argument::any(), array($matcher))
		         ->shouldHaveBeenCalled();
	}

	/**
	 * @param \Ingenerator\Mailhook\Mailhook $mailhook
	 */
	function it_refreshes_mailhook($mailhook)
	{
		$this->try_assert_with();
		$mailhook->refresh()->shouldHaveBeenCalled();
	}

	/**
	 * @param \Ingenerator\Mailhook\Mailhook          $mailhook
	 * @param \Ingenerator\Mailhook\EmailListFilterer $filterer
	 */
	function let($mailhook, $filterer)
	{
        $mailhook->beADoubleOf(Mailhook::class);
        $filterer->beADoubleOf(EmailListFilterer::class);

		$mailhook->refresh()->willReturn(NULL);
		$mailhook->getEmails()->willReturn(array());

		$this->beConstructedWith($mailhook, $filterer);
	}

	protected function try_assert_with($matchers = array())
	{
		foreach ($matchers as $matcher) {
		    $matcher->beADoubleOf(EmailMatcher::class);
			$matcher->__toString()->willReturn('');
		}

		try
		{
			$this->subject->assert($matchers);
		}
		catch (EmailAssertionFailedException $e)
		{
			// OK
		}
	}
}
