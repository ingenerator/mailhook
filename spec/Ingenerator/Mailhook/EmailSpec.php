<?php
/**
 * Defines EmailSpec - specifications for Ingenerator\Mailhook\Email
 *
 * @copyright  2014 inGenerator Ltd
 * @licence    BSD
 */

namespace spec\Ingenerator\Mailhook;

use Prophecy\Argument;
use spec\ObjectBehavior;

/**
 *
 * @see Ingenerator\Mailhook\Email
 */
class EmailSpec extends ObjectBehavior
{
    /**
     * Use $this->subject to get proper type hinting for the subject class
     * @var \Ingenerator\Mailhook\Email
     */
	protected $subject;

	function let()
	{
		$this->beConstructedWith(array());
	}

	function it_is_initializable()
    {
		$this->subject->shouldHaveType('Ingenerator\Mailhook\Email');
	}

	function it_filters_empty_links_by_regular_expression()
	{
		$this->subject->beConstructedWith(array('links' => array()));
		$this->subject->getLinksMatching('/test/')->shouldBe(array());
	}

	function it_filters_links_by_regular_expression()
	{
		$this->subject->beConstructedWith(array('links' => array(
			'https://www.ingenerator.com/test/matching-url',
			'https://www.ingenerator.com/test/notmatch-url'
		)));
		$this->subject->getLinksMatching('_matching-url$_')
			->shouldBe(array('https://www.ingenerator.com/test/matching-url'));
	}
}
