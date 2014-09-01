<?php
/**
 * Defines EmailWithLinkMatcherSpec - specifications for Ingenerator\Mailhook\Matcher\EmailWithLinkMatcher
 *
 * @copyright  2014 inGenerator Ltd
 * @licence    BSD
 */

namespace spec\Ingenerator\Mailhook\Matcher;

use Prophecy\Argument;
use spec\ObjectBehavior;

/**
 *
 * @see Ingenerator\Mailhook\Matcher\EmailWithLinkMatcher
 */
class EmailWithLinkMatcherSpec extends EmailMatcherBehaviour
{
    /**
     * Use $this->subject to get proper type hinting for the subject class
     * @var \Ingenerator\Mailhook\Matcher\EmailWithLinkMatcher
     */
	protected $subject;

	function it_is_initializable()
    {
		$this->subject->shouldHaveType('Ingenerator\Mailhook\Matcher\EmailWithLinkMatcher');
	}

	/**
	 * @param \Ingenerator\Mailhook\Email $email
	 */
	function it_does_not_match_email_with_no_links_when_no_pattern($email)
	{
		$this->beConstructedWith(NULL);
		$email->getLinks()->willReturn(array());
		$this->subject->matches($email)->shouldBe(FALSE);
	}

	/**
	 * @param \Ingenerator\Mailhook\Email $email
	 */
	function it_does_not_match_email_with_no_links_when_pattern($email)
	{
		$this->beConstructedWith('/reset/');
		$email->getLinksMatching('/reset/')->willReturn(array());
		$this->subject->matches($email)->shouldBe(FALSE);
	}

	/**
	 * @param \Ingenerator\Mailhook\Email $email
	 */
	function it_matches_email_with_any_link_when_pattern_is_empty($email)
	{
		$this->beConstructedWith(NULL);
		$email->getLinks()->willReturn(array('href' => 'http://foo.bar'));
		$this->subject->matches($email)->shouldBe(TRUE);
	}

	/**
	 * @param \Ingenerator\Mailhook\Email $email
	 */
	function it_matches_email_with_matching_link_when_pattern_provided($email)
	{
		$this->beConstructedWith('/reset/');
		$email->getLinksMatching('/reset/')->willReturn(array('href' => 'http://foo.bar/reset'));
		$this->subject->matches($email)->shouldBe(TRUE);
	}

	function it_describes_with_pattern_when_provided()
	{
		$this->beConstructedWith('/reset/');
		$this->subject->__toString()->shouldBe('With link matching "/reset/"');
	}

	function it_describes_without_pattern_when_empty()
	{
		$this->beConstructedWith(NULL);
		$this->subject->__toString()->shouldBe('With any link');
	}
}
