<?php

declare(strict_types=1);

namespace test\unit\Ingenerator\Mailhook;

use Ingenerator\Mailhook\Email;
use Ingenerator\Mailhook\EmailParser;
use PHPUnit\Framework\TestCase;

class EmailParserTest extends TestCase
{
    private const string SIMPLE_MAIL = <<<'MAIL'
From test@ingenerator.com  Thu Aug 28 15:09:47 2014
Received: by ccstravel.dev (Postfix, from userid 1000)
        id A57B83A1145; Thu, 28 Aug 2014 15:09:47 +0000 (UTC)
To: <vagrant@ccstravel.dev>
X-Mailer: mail (GNU Mailutils 2.2)
Message-Id: <20140828150947.A57B83A1145@ccstravel.dev>
Date: Thu, 28 Aug 2014 15:09:47 +0000 (UTC)
From: test@ingenerator.com (test)

test


with newlines and http://www.ingenerator.com/foo?bar=3Dbaz
and lines

MAIL;

    private const string ADDRESSED_MAIL = <<<'MAIL'
From test@ingenerator.com  Thu Aug 28 15:09:47 2014
Received: by ccstravel.dev (Postfix, from userid 1000)
        id A57B83A1145; Thu, 28 Aug 2014 15:09:47 +0000 (UTC)
To: Vagrant Vagrant <vagrant@ccstravel.dev>
X-Mailer: mail (GNU Mailutils 2.2)
Message-Id: <20140828150947.A57B83A1145@ccstravel.dev>
Date: Thu, 28 Aug 2014 15:09:47 +0000 (UTC)
From: test@ingenerator.com (test)

test


with newlines and http://www.ingenerator.com/foo?bar=3Dbaz
and lines

MAIL;

    private const string HTML_MAIL = <<<'MAIL'
From test@ingenerator.com  Fri Aug 29 10:50:13 2014
Received: by ccstravel.dev (Postfix, from userid 1000)
        id 9027F3A0C62; Fri, 29 Aug 2014 10:50:13 +0000 (UTC)
Received: from ccstravel.dev (localhost [127.0.0.1])
        by ccstravel.dev (Postfix) with SMTP id 6EB673C1ADB
        for <test+recipient@ingenerator.com>; Fri, 29 Aug 2014 10:50:13 +0000 (UTC)
Message-ID: <a9bfaf8d36cbfc40740dbe0cd098727b@ccstravel.dev>
Date: Fri, 29 Aug 2014 10:50:13 +0000
Subject: Complete your registration
From: "ingenerator.com" <test@ingenerator.com>
To: test+recipient@ingenerator.com
MIME-Version: 1.0
Content-Type: text/html; charset=utf-8
Content-Transfer-Encoding: quoted-printable

<html>
<head>
        <title>Welcome to ingenerator.com</title>
</=
head>
<body>
<p>
        Hi test+recipient@ingenerator.com,
</p>
<=
p>
        Thank you for registering for an account. Y=
ou're almost there - we just
        need to verify your email and take a c=
ouple more details (including a password you can use
        to login in fu=
ture).
</p>
<p>
        <a h=
ref=3D"https://ccstravel.dev/register/verify?token=3D0uAdL%2BRD0XnnnH%2Be=
-1409482211-7ea81e981ad6c8f8b2e41d74b257c147e664087f">
                Activate yo=
ur account<span style=3D"font-size: 0;"> at https://ccstravel.dev/register/=
verify?token=3D0uAdL%2BRD0XnnnH%2Be-1409482211-7ea81e981ad6c8f8b2e41d74b25=
7c147e664087f</span>
        </a>
</p>
<hr>
<p><small>
Visit us at <a href=3D"http://ccstravel.dev/">ccstravel.dev</a></small>
</p>
</body>
</html=
>

MAIL;

    private function newSubject(): EmailParser
    {
        return new EmailParser();
    }

    public function test_it_is_initializable(): void
    {
        $subject = $this->newSubject();
        $this->assertInstanceOf(EmailParser::class, $subject);
    }

    public function test_it_parses_text_to_email(): void
    {
        $subject = $this->newSubject();
        $this->assertInstanceOf(Email::class, $subject->parse(self::SIMPLE_MAIL));
    }

    public function test_it_parses_email_recipient(): void
    {
        $subject = $this->newSubject();
        $this->assertSame('vagrant@ccstravel.dev', $subject->parse(self::SIMPLE_MAIL)->getTo());
        $this->assertSame('test+recipient@ingenerator.com', $subject->parse(self::HTML_MAIL)->getTo());
        $this->assertSame('vagrant@ccstravel.dev', $subject->parse(self::ADDRESSED_MAIL)->getTo());
    }

    public function test_it_parses_email_subject(): void
    {
        $subject = $this->newSubject();
        $this->assertNull($subject->parse(self::SIMPLE_MAIL)->getSubject());
        $this->assertSame('Complete your registration', $subject->parse(self::HTML_MAIL)->getSubject());
    }

    public function test_it_parses_email_content(): void
    {
        $subject = $this->newSubject();
        $this->assertSame(
            <<<'TEXT'
test


with newlines and http://www.ingenerator.com/foo?bar=baz
and lines

TEXT
            ,
            $subject->parse(self::SIMPLE_MAIL)->getContent()
        );

        $this->assertSame(
            <<<'TEXT'
<html>
<head>
        <title>Welcome to ingenerator.com</title>
</head>
<body>
<p>
        Hi test+recipient@ingenerator.com,
</p>
<p>
        Thank you for registering for an account. You're almost there - we just
        need to verify your email and take a couple more details (including a password you can use
        to login in future).
</p>
<p>
        <a href="https://ccstravel.dev/register/verify?token=0uAdL%2BRD0XnnnH%2Be-1409482211-7ea81e981ad6c8f8b2e41d74b257c147e664087f">
                Activate your account<span style="font-size: 0;"> at https://ccstravel.dev/register/verify?token=0uAdL%2BRD0XnnnH%2Be-1409482211-7ea81e981ad6c8f8b2e41d74b257c147e664087f</span>
        </a>
</p>
<hr>
<p><small>
Visit us at <a href="http://ccstravel.dev/">ccstravel.dev</a></small>
</p>
</body>
</html>

TEXT
            ,
            $subject->parse(self::HTML_MAIL)->getContent()
        );
    }

    public function test_it_parses_unique_links_from_content(): void
    {
        $subject = $this->newSubject();
        $this->assertSame(
            ['http://www.ingenerator.com/foo?bar=baz'],
            $subject->parse(self::SIMPLE_MAIL)->getLinks()
        );

        $this->assertSame(
            [
                'https://ccstravel.dev/register/verify?token=0uAdL%2BRD0XnnnH%2Be-1409482211-7ea81e981ad6c8f8b2e41d74b257c147e664087f',
                'http://ccstravel.dev/',
            ],
            $subject->parse(self::HTML_MAIL)->getLinks()
        );
    }
}
