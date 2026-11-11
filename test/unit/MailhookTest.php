<?php

declare(strict_types=1);

namespace test\unit\Ingenerator\Mailhook;

use Ingenerator\Mailhook\Email;
use Ingenerator\Mailhook\EmailParser;
use Ingenerator\Mailhook\Mailhook;
use Ingenerator\Mailhook\MailhookAsserter;
use org\bovigo\vfs\vfsStream;
use org\bovigo\vfs\vfsStreamDirectory;
use org\bovigo\vfs\vfsStreamFile;
use PHPUnit\Framework\TestCase;

class MailhookTest extends TestCase
{
    private const string DUMP_FILE = 'mail.dump';

    private const string SINGLE_MAIL_DUMP = <<<'MAIL'
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

    private const string MULTI_MAIL_DUMP = <<<'MAIL'
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

    private const string NEW_MAIL_DUMP = <<<'MAIL'
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

    private vfsStreamDirectory $tmp_dir;

    protected function setUp(): void
    {
        parent::setUp();
        $this->tmp_dir = vfsStream::setup('tmp');
        \clearstatcache();
    }

    private function newSubject(?EmailParser $parser = null): Mailhook
    {
        return new Mailhook(vfsStream::url('tmp/'.self::DUMP_FILE), $parser);
    }

    public function test_it_is_initializable(): void
    {
        $subject = $this->newSubject();
        $this->assertInstanceOf(Mailhook::class, $subject);
    }

    public function test_its_purge_does_nothing_when_no_dump_file(): void
    {
        $subject = $this->newSubject();
        $subject->purge();
        $this->assertFalse($this->tmp_dir->hasChild(self::DUMP_FILE));
    }

    public function test_its_purge_deletes_dump_file(): void
    {
        $this->givenDumpFile('');
        $subject = $this->newSubject();
        $subject->purge();
        $this->assertFalse($this->tmp_dir->hasChild(self::DUMP_FILE));
    }

    public function test_its_purge_throws_if_dump_file_cannot_be_deleted(): void
    {
        $this->givenDumpFile('', 0400);
        $subject = $this->newSubject();

        $this->expectException(\RuntimeException::class);
        $subject->purge();
    }

    public function test_its_get_emails_returns_empty_array_when_no_emails(): void
    {
        $subject = $this->newSubject();
        $this->assertSame([], $subject->getEmails());
    }

    public function test_it_has_no_emails_when_no_emails(): void
    {
        $subject = $this->newSubject();
        $this->assertFalse($subject->hasEmails());
    }

    public function test_its_refresh_does_nothing_when_no_dump_file(): void
    {
        $parser = $this->createMock(EmailParser::class);
        $parser->expects($this->never())->method('parse');

        $subject = $this->newSubject($parser);
        $subject->refresh();
    }

    public function test_its_refresh_does_nothing_when_dump_file_is_empty(): void
    {
        $this->givenDumpFile('');
        $parser = $this->createMock(EmailParser::class);
        $parser->expects($this->never())->method('parse');

        $subject = $this->newSubject($parser);
        $subject->refresh();
    }

    public function test_its_refresh_parses_single_mail_when_in_dump_file(): void
    {
        $this->givenDumpFile(self::SINGLE_MAIL_DUMP);
        $parser = $this->createMock(EmailParser::class);
        $parser->expects($this->once())
            ->method('parse')
            ->with($this->matchesRegularExpression('/^From vagrant@ccstravel.dev.+?test\s+$/s'))
            ->willReturn(new Email([]));

        $subject = $this->newSubject($parser);
        $subject->refresh();
    }

    public function test_its_refresh_parses_multiple_mails_when_in_dump_file(): void
    {
        $this->givenDumpFile(self::MULTI_MAIL_DUMP);
        $parsed = [];
        $parser = $this->createMock(EmailParser::class);
        $parser->expects($this->exactly(2))
            ->method('parse')
            ->willReturnCallback(function (string $mail) use (&$parsed) {
                $parsed[] = $mail;
                return new Email([]);
            });

        $subject = $this->newSubject($parser);
        $subject->refresh();

        $this->assertCount(2, $parsed);
        $this->assertMatchesRegularExpression('/^From vagrant@ccstravel.dev.+?test\s+$/s', $parsed[0]);
        $this->assertMatchesRegularExpression('/^From test@ingenerator.com.+?lines\s+$/s', $parsed[1]);
    }

    public function test_its_refresh_parses_only_new_mails_when_called_again(): void
    {
        $this->givenDumpFile(self::MULTI_MAIL_DUMP);
        $parsed = [];
        $parser = $this->createMock(EmailParser::class);
        $parser->expects($this->exactly(3))
            ->method('parse')
            ->willReturnCallback(function (string $mail) use (&$parsed) {
                $parsed[] = $mail;
                return new Email([]);
            });

        $subject = $this->newSubject($parser);
        $subject->refresh();
        $this->givenDumpFileAppended(self::NEW_MAIL_DUMP);
        $subject->refresh();

        $this->assertCount(3, $parsed);
        $this->assertMatchesRegularExpression('/^From vagrant@ccstravel.dev.+?test\s+$/s', $parsed[0]);
        $this->assertMatchesRegularExpression('/^From test@ingenerator.com.+?lines\s+$/s', $parsed[1]);
        $this->assertMatchesRegularExpression('/^From new@ingenerator.dev.+?new mail test\.\s+$/s', $parsed[2]);
    }

    public function test_its_refresh_parses_only_new_mails_when_called_multiple_times(): void
    {
        $this->givenDumpFile(self::SINGLE_MAIL_DUMP);
        $parser = $this->createMock(EmailParser::class);
        $parser->expects($this->exactly(2))
            ->method('parse')
            ->willReturn(new Email([]));

        $subject = $this->newSubject($parser);
        $subject->refresh();
        $subject->refresh();
        $this->givenDumpFileAppended(self::NEW_MAIL_DUMP);
        $subject->refresh();
    }

    public function test_its_refresh_parses_new_mails_after_purge(): void
    {
        $this->givenDumpFile(self::SINGLE_MAIL_DUMP);
        $parsed = [];
        $parser = $this->createMock(EmailParser::class);
        $parser->expects($this->exactly(2))
            ->method('parse')
            ->willReturnCallback(function (string $mail) use (&$parsed) {
                $parsed[] = $mail;
                return new Email([]);
            });

        $subject = $this->newSubject($parser);
        $subject->refresh();
        $subject->purge();
        $this->givenDumpFile(self::SINGLE_MAIL_DUMP);
        $subject->refresh();

        $this->assertCount(2, $parsed);
        $this->assertMatchesRegularExpression('/^From vagrant@ccstravel.dev/', $parsed[0]);
        $this->assertMatchesRegularExpression('/^From vagrant@ccstravel.dev/', $parsed[1]);
    }

    public function test_its_get_emails_returns_parsed_emails_when_refreshed(): void
    {
        $email1 = new Email([]);
        $email2 = new Email([]);
        $parser = $this->createMock(EmailParser::class);
        $parser->expects($this->exactly(2))
            ->method('parse')
            ->willReturnCallback(function (string $mail) use ($email1, $email2) {
                if (\preg_match('/^From vagrant@ccstravel.dev/', $mail)) {
                    return $email1;
                }
                if (\preg_match('/^From test@ingenerator.com/', $mail)) {
                    return $email2;
                }
                throw new \InvalidArgumentException('Unexpected mail: '.$mail);
            });

        $this->givenDumpFile(self::MULTI_MAIL_DUMP);
        $subject = $this->newSubject($parser);
        $subject->refresh();
        $this->assertSame([$email1, $email2], $subject->getEmails());
    }

    public function test_its_has_emails_is_true_when_refreshed_with_emails(): void
    {
        $this->givenDumpFile(self::SINGLE_MAIL_DUMP);
        $parser = $this->createStub(EmailParser::class);
        $parser->method('parse')->willReturn(new Email([]));

        $subject = $this->newSubject($parser);
        $subject->refresh();
        $this->assertTrue($subject->hasEmails());
    }

    public function test_its_get_emails_and_has_emails_are_empty_after_purge(): void
    {
        $this->givenDumpFile(self::SINGLE_MAIL_DUMP);
        $parser = $this->createStub(EmailParser::class);
        $parser->method('parse')->willReturn(new Email([]));

        $subject = $this->newSubject($parser);
        $subject->refresh();
        $subject->purge();
        $this->assertFalse($subject->hasEmails());
        $this->assertSame([], $subject->getEmails());
    }

    public function test_its_assert_method_returns_constructed_asserter(): void
    {
        $subject = $this->newSubject();
        $this->assertInstanceOf(MailhookAsserter::class, $subject->assert());
    }

    private function givenDumpFile(string $content, int $mode = 0777): void
    {
        $file = new vfsStreamFile(self::DUMP_FILE, $mode);
        $file->setContent($content);
        $this->tmp_dir->addChild($file);
    }

    private function givenDumpFileAppended(string $new_mail): void
    {
        /** @var vfsStreamFile $dump */
        $dump = $this->tmp_dir->getChild(self::DUMP_FILE);
        $content = $dump->getContent();
        $content .= $new_mail;
        $dump->setContent($content);
    }
}
