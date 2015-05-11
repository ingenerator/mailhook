<?php
/**
 * Defines EmailParserSpec - specifications for Ingenerator\Mailhook\EmailParser
 *
 * @copyright  2014 inGenerator Ltd
 * @licence    BSD
 */

namespace spec\Ingenerator\Mailhook;

use Prophecy\Argument;
use spec\ObjectBehavior;

/**
 *
 * @see Ingenerator\Mailhook\EmailParser
 */
class EmailParserSpec extends ObjectBehavior
{
    /**
     * Use $this->subject to get proper type hinting for the subject class
     * @var \Ingenerator\Mailhook\EmailParser
     */
	protected $subject;

	function it_is_initializable()
    {
		$this->subject->shouldHaveType('Ingenerator\Mailhook\EmailParser');
	}

	function it_parses_text_to_email()
	{
		$this->subject->parse(self::SIMPLE_MAIL)->shouldBeAnInstanceOf('Ingenerator\Mailhook\Email');
	}

	function it_parses_email_recipient()
	{
		$this->subject->parse(self::SIMPLE_MAIL)->getTo()->shouldBe('vagrant@ccstravel.dev');
		$this->subject->parse(self::HTML_MAIL)->getTo()->shouldBe('test+recipient@ingenerator.com');
		$this->subject->parse(self::ADDRESSED_MAIL)->getTo()->shouldBe('vagrant@ccstravel.dev');
	}

	function it_parses_email_subject()
	{
		$this->subject->parse(self::SIMPLE_MAIL)->getSubject()->shouldBe(NULL);
		$this->subject->parse(self::HTML_MAIL)->getSubject()->shouldBe('Complete your registration');
	}

	function it_parses_email_content()
	{
		$this->subject->parse(self::SIMPLE_MAIL)->getContent()->shouldBe(<<<'TEXT'
test


with newlines and http://www.ingenerator.com/foo?bar=baz
and lines

TEXT
);

		$this->subject->parse(self::HTML_MAIL)->getContent()->shouldBe(<<<'TEXT'
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
);

	}

	function it_parses_unique_links_from_content()
	{
		$this->subject->parse(self::SIMPLE_MAIL)->getLinks()
			->shouldBe(array(
				'http://www.ingenerator.com/foo?bar=baz'
			));

		$this->subject->parse(self::HTML_MAIL)->getLinks()
			->shouldBe(array(
				'https://ccstravel.dev/register/verify?token=0uAdL%2BRD0XnnnH%2Be-1409482211-7ea81e981ad6c8f8b2e41d74b257c147e664087f',
			    'http://ccstravel.dev/',
			));
	}

	const SIMPLE_MAIL = <<<'MAIL'
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

    const ADDRESSED_MAIL = <<<'MAIL'
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

	const HTML_MAIL = <<<'MAIL'
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


}
