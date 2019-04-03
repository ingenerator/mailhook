<?php
/**
 * Defines MailhookSpec - specifications for Ingenerator\Mailhook\Mailhook
 *
 * @copyright  2014 inGenerator Ltd
 * @licence    BSD
 */

namespace spec\Ingenerator\Mailhook;

use org\bovigo\vfs\vfsStream;
use org\bovigo\vfs\vfsStreamDirectory;
use org\bovigo\vfs\vfsStreamFile;
use PhpSpec\Exception\Example\FailureException;
use Prophecy\Argument;
use spec\ObjectBehavior;
use spec\Support\Token\StringRegexToken;

/**
 *
 * @see Ingenerator\Mailhook\Mailhook
 */
class MailhookSpec extends ObjectBehavior
{
    /**
     * Use $this->subject to get proper type hinting for the subject class
     * @var \Ingenerator\Mailhook\Mailhook
     */
	protected $subject;

	const DUMP_FILE = 'mail.dump';

	/**
	 * @var vfsStreamDirectory
	 */
	protected $tmp_dir;

	/**
	 * @param \Ingenerator\Mailhook\EmailParser $parser
	 */
	function let($parser)
	{
		$this->tmp_dir = vfsStream::setup('tmp');
		\clearstatcache();
		$this->subject->beConstructedWith(vfsStream::url('tmp/'.self::DUMP_FILE), $parser);
	}

	function it_is_initializable()
    {
		$this->subject->shouldHaveType('Ingenerator\Mailhook\Mailhook');
	}

	function its_purge_does_nothing_when_no_dump_file()
	{
		$this->subject->purge();
	}

	function its_purge_deletes_dump_file()
	{
		$this->given_dump_file('');
		$this->subject->purge();
		expect($this->tmp_dir->hasChild(self::DUMP_FILE))->toBe(FALSE);
	}

	function its_purge_throws_if_dump_file_cannot_be_deleted()
	{
		$this->given_dump_file('', 0400);
		$this->shouldThrow('RuntimeException')->during('purge');
	}

	function its_get_emails_returns_empty_array_when_no_emails()
	{
		$this->subject->getEmails()->shouldReturn(array());
	}

	function it_has_no_emails_when_no_emails()
	{
		$this->subject->shouldNotHaveEmails();
	}

	/**
	 * @param \Ingenerator\Mailhook\EmailParser $parser
	 */
	function its_refresh_does_nothing_when_no_dump_file($parser)
	{
		$this->subject->refresh();
		$parser->parse(Argument::any())->shouldNotHaveBeenCalled();
	}

	/**
	 * @param \Ingenerator\Mailhook\EmailParser $parser
	 */
	function its_refresh_does_nothing_when_dump_file_is_empty($parser)
	{
		$this->given_dump_file('');
		$this->subject->refresh();
		$parser->parse(Argument::any())->shouldNotHaveBeenCalled();
	}

	/**
	 * @param \Ingenerator\Mailhook\EmailParser $parser
	 */
	function its_refresh_parses_single_mail_when_in_dump_file($parser)
	{
		$this->given_dump_file(self::SINGLE_MAIL_DUMP);
		$this->subject->refresh();
		$parser->parse(new StringRegexToken('/^From vagrant@ccstravel.dev.+?test\s+$/s'))->shouldHaveBeenCalled();
	}

	/**
	 * @param \Ingenerator\Mailhook\EmailParser $parser
	 */
	function its_refresh_parses_multiple_mails_when_in_dump_file($parser)
	{
		$this->given_dump_file(self::MULTI_MAIL_DUMP);
		$this->subject->refresh();
		$parser->parse(new StringRegexToken('/^From vagrant@ccstravel.dev.+?test\s+$/s'))->shouldHaveBeenCalledTimes(1);
		$parser->parse(new StringRegexToken('/^From test@ingenerator.com.+?lines\s+$/s'))->shouldHaveBeenCalledTimes(1);
	}

	/**
	 * @param \Ingenerator\Mailhook\EmailParser $parser
	 */
	function its_refresh_parses_only_new_mails_when_called_again($parser)
	{
		$this->given_dump_file(self::MULTI_MAIL_DUMP);
		$this->subject->refresh();
		$this->given_dump_file_appended(self::NEW_MAIL_DUMP);
		$this->subject->refresh();
		$parser->parse(new StringRegexToken('/^From vagrant@ccstravel.dev.+?test\s+$/s'))->shouldHaveBeenCalledTimes(1);
		$parser->parse(new StringRegexToken('/^From test@ingenerator.com.+?lines\s+$/s'))->shouldHaveBeenCalledTimes(1);
		$parser->parse(new StringRegexToken('/^From new@ingenerator.dev.+?new mail test\.\s+$/s'))->shouldHaveBeenCalledTimes(1);
	}

	/**
	 * @param \Ingenerator\Mailhook\EmailParser $parser
	 */
	function its_refresh_parses_only_new_mails_when_called_multiple_times($parser)
	{
		$this->given_dump_file(self::SINGLE_MAIL_DUMP);
		$this->subject->refresh();
		$this->subject->refresh();
		$this->given_dump_file_appended(self::NEW_MAIL_DUMP);
		$this->subject->refresh();
		$parser->parse(Argument::any())->shouldHaveBeenCalledTimes(2);
	}

	/**
	 * @param \Ingenerator\Mailhook\EmailParser $parser
	 */
	function its_refresh_parses_new_mails_after_purge($parser)
	{
		$this->given_dump_file(self::SINGLE_MAIL_DUMP);
		$this->subject->refresh();
		$this->subject->purge();
		$this->given_dump_file(self::SINGLE_MAIL_DUMP);
		$this->subject->refresh();

		$parser->parse(new StringRegexToken('/^From vagrant@ccstravel.dev/'))->shouldHaveBeenCalledTimes(2);
	}

	/**
	 * @param \Ingenerator\Mailhook\EmailParser $parser
	 * @param \Ingenerator\Mailhook\Email       $email1
	 * @param \Ingenerator\Mailhook\Email       $email2
	 */
	function its_get_emails_returns_parsed_emails_when_refreshed($parser, $email1, $email2)
	{
		$parser->parse(new StringRegexToken('/^From vagrant@ccstravel.dev/'))->willReturn($email1);
		$parser->parse(new StringRegexToken('/^From test@ingenerator.com/'))->willReturn($email2);
		$this->given_dump_file(self::MULTI_MAIL_DUMP);
		$this->subject->refresh();
		$this->subject->getEmails()->shouldBe(array($email1, $email2));
	}

	function its_has_emails_is_true_when_refreshed_with_emails()
	{
		$this->given_dump_file(self::SINGLE_MAIL_DUMP);
		$this->subject->refresh();
		$this->subject->shouldHaveEmails();
	}

	function its_get_emails_and_has_emails_are_empty_after_purge()
	{
		$this->given_dump_file(self::SINGLE_MAIL_DUMP);
		$this->subject->refresh();
		$this->subject->purge();
		$this->subject->shouldNotHaveEmails();
		$this->subject->getEmails()->shouldBe(array());
	}

	function its_assert_method_returns_constructed_asserter()
	{
		$this->subject->assert()->shouldBeAnInstanceOf('Ingenerator\Mailhook\MailhookAsserter');
	}

	/**
	 * @param string $content
	 * @param int    $mode
	 */
	protected function given_dump_file($content, $mode = 0777)
	{
		$file = new vfsStreamFile(self::DUMP_FILE, $mode);
		$file->setContent($content);
		$this->tmp_dir->addChild($file);
	}

	protected function given_dump_file_appended($new_mail)
	{
		$dump = $this->tmp_dir->getChild(self::DUMP_FILE);
		/** @var vfsStreamFile $dump */
		$content = $dump->getContent();
		$content .= $new_mail;
		$dump->setContent($content);
	}

	const SINGLE_MAIL_DUMP = <<<'MAIL'
From vagrant@ccstravel.dev  Thu Aug 28 14:57:53 2014
Received: by ccstravel.dev (Postfix, from userid 1000)
        id 3CC753A1145; Thu, 28 Aug 2014 14:57:53 +0000 (UTC)
To: <test@ingenerator.com>
X-Mailer: mail (GNU Mailutils 2.2)
Message-Id: <20140828145753.3CC753A1145@ccstravel.dev>
Date: Thu, 28 Aug 2014 14:57:53 +0000 (UTC)
From: vagrant@ccstravel.dev (vagrant)

test

MAIL;

	const MULTI_MAIL_DUMP = <<<'MAIL'
From vagrant@ccstravel.dev  Thu Aug 28 14:57:53 2014
Received: by ccstravel.dev (Postfix, from userid 1000)
        id 3CC753A1145; Thu, 28 Aug 2014 14:57:53 +0000 (UTC)
To: <test@ingenerator.com>
X-Mailer: mail (GNU Mailutils 2.2)
Message-Id: <20140828145753.3CC753A1145@ccstravel.dev>
Date: Thu, 28 Aug 2014 14:57:53 +0000 (UTC)
From: vagrant@ccstravel.dev (vagrant)

test

From test@ingenerator.com  Thu Aug 28 15:09:47 2014
Received: by ccstravel.dev (Postfix, from userid 1000)
        id A57B83A1145; Thu, 28 Aug 2014 15:09:47 +0000 (UTC)
To: <vagrant@ccstravel.dev>
X-Mailer: mail (GNU Mailutils 2.2)
Message-Id: <20140828150947.A57B83A1145@ccstravel.dev>
Date: Thu, 28 Aug 2014 15:09:47 +0000 (UTC)
From: test@ingenerator.com (test)

test


with newlines
and lines

MAIL;

	const NEW_MAIL_DUMP = <<<'MAIL'
From new@ingenerator.dev  Thu Aug 28 14:57:53 2014
Received: by ccstravel.dev (Postfix, from userid 1000)
        id 3CC753A1145; Thu, 28 Aug 2014 14:57:53 +0000 (UTC)
To: <test@ingenerator.com>
X-Mailer: mail (GNU Mailutils 2.2)
Message-Id: <20140828145753.3CC753A1145@ccstravel.dev>
Date: Thu, 28 Aug 2014 14:57:53 +0000 (UTC)
From: vagrant@ccstravel.dev (vagrant)

new mail test.

MAIL;

}
