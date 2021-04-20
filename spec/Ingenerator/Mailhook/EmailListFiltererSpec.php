<?php
/**
 * Defines EmailListFiltererSpec - specifications for Ingenerator\Mailhook\EmailListFilterer
 *
 * @copyright  2014 inGenerator Ltd
 * @licence    BSD
 */

namespace spec\Ingenerator\Mailhook;

use Ingenerator\Mailhook\Email;
use Ingenerator\Mailhook\EmailMatcher;
use Prophecy\Argument;
use spec\ObjectBehavior;

/**
 *
 * @see Ingenerator\Mailhook\EmailListFilterer
 */
class EmailListFiltererSpec extends ObjectBehavior
{
    /**
     * Use $this->subject to get proper type hinting for the subject class
     * @var \Ingenerator\Mailhook\EmailListFilterer
     */
	protected $subject;

	function it_is_initializable()
    {
		$this->subject->shouldHaveType('Ingenerator\Mailhook\EmailListFilterer');
	}

	function it_filters_empty_array_to_empty_array()
	{
		$this->subject->filterEmails(array(), array())->shouldBe(array());
	}

	function it_filters_emails_to_empty_array_with_no_matchers()
	{
		$this->subject->filterEmails(array(new DummyEmail), array())->shouldBe(array());
	}

	/**
	 * @param \Ingenerator\Mailhook\EmailMatcher $matcher
	 */
	function it_filters_emails_to_empty_array_when_matcher_does_not_match($matcher)
	{
	    $matcher->beADoubleOf(EmailMatcher::class);
		$matcher->matches(Argument::any())->willReturn(FALSE);
		$this->subject->filterEmails(array(new DummyEmail), array($matcher))->shouldBe(array());
	}

	/**
	 * @param \Ingenerator\Mailhook\EmailMatcher $yes_matcher
	 * @param \Ingenerator\Mailhook\EmailMatcher $no_matcher
	 */
	function it_filters_emails_to_empty_array_when_one_matcher_matches_and_one_does_not($yes_matcher, $no_matcher)
	{
        $yes_matcher->beADoubleOf(EmailMatcher::class);
        $no_matcher->beADoubleOf(EmailMatcher::class);
		$yes_matcher->matches(Argument::any())->willReturn(TRUE);
		$no_matcher->matches(Argument::any())->willReturn(FALSE);
		$this->subject->filterEmails(array(new DummyEmail), array($yes_matcher, $no_matcher))->shouldBe(array());
	}

	/**
	 * @param \Ingenerator\Mailhook\EmailMatcher $matcher
	 */
	function it_returns_all_emails_when_matcher_matches($matcher)
	{
        $matcher->beADoubleOf(EmailMatcher::class);
		$email = new DummyEmail;
		$this->given_matcher_matches($matcher, $email);
		$this->subject->filterEmails(array($email), array($matcher))->shouldBe(array($email));
	}

	/**
	 * @param \Ingenerator\Mailhook\EmailMatcher $matcher
	 */
	function it_returns_matching_emails_when_matcher_matches_some($matcher)
	{
        $matcher->beADoubleOf(EmailMatcher::class);
		$good = new DummyEmail;
		$bad = new DummyEmail;
		$this->given_matcher_matches($matcher, $good);
		$this->subject->filterEmails(array($good, $bad), array($matcher))->shouldBe(array($good));
	}

	/**
	 * @param \Ingenerator\Mailhook\EmailMatcher $matcher_1
	 * @param \Ingenerator\Mailhook\EmailMatcher $matcher_2
	 */
	function it_returns_emails_matching_all_matchers_with_some_good_and_some_bad($matcher_1, $matcher_2)
	{
        $matcher_1->beADoubleOf(EmailMatcher::class);
        $matcher_2->beADoubleOf(EmailMatcher::class);
		$both_good = new DummyEmail;
		$both_bad  = new DummyEmail;
		$one_good  = new DummyEmail;
		$two_good  = new DummyEmail;

		$this->given_matcher_matches($matcher_1, array($both_good, $one_good));
		$this->given_matcher_matches($matcher_2, array($both_good, $two_good));

		$this->subject->filterEmails(
			array($both_good, $one_good, $two_good, $both_bad),
			array($matcher_1, $matcher_2)
		)->shouldBe(array($both_good));
	}

	/**
	 * @param $matcher
	 * @param $emails
	 */
	protected function given_matcher_matches($matcher, $emails)
	{
		if ( ! \is_array($emails)) {
			$emails = array($emails);
		}

		$matcher->matches(Argument::any())->will(function ($args) use ($emails) {
			return \in_array($args[0], $emails, TRUE);
		});
	}

}

/**
 * Just so we have some emails
 *
 * @package spec\Ingenerator\Mailhook
 */
class DummyEmail extends Email {
	public function __construct() {}
}
