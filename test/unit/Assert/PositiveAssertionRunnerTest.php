<?php

declare(strict_types=1);

namespace test\unit\Ingenerator\Mailhook\Assert;

use Ingenerator\Mailhook\Assert\AssertionRunner;
use Ingenerator\Mailhook\Assert\PositiveAssertionRunner;
use Ingenerator\Mailhook\Email;
use Ingenerator\Mailhook\EmailAssertionFailedException;
use Ingenerator\Mailhook\EmailListFilterer;
use Ingenerator\Mailhook\Mailhook;

class PositiveAssertionRunnerTest extends AbstractAssertionRunnerTestCase
{
    protected function createRunner(Mailhook $mailhook, EmailListFilterer $filterer): AssertionRunner
    {
        return $this->newSubject($mailhook, $filterer);
    }

    private function newSubject(?Mailhook $mailhook = null, ?EmailListFilterer $filterer = null): PositiveAssertionRunner
    {
        return new PositiveAssertionRunner(
            $mailhook ?? $this->createStub(Mailhook::class),
            $filterer ?? $this->createStub(EmailListFilterer::class),
        );
    }

    public function test_it_is_initializable(): void
    {
        $subject = $this->newSubject();
        $this->assertInstanceOf(PositiveAssertionRunner::class, $subject);
    }

    public function test_it_throws_if_no_matching_emails(): void
    {
        $filterer = $this->createStub(EmailListFilterer::class);
        $filterer->method('filterEmails')->willReturn([]);

        $subject = $this->newSubject(filterer: $filterer);

        $this->expectException(EmailAssertionFailedException::class);
        $subject->assert();
    }

    public function test_it_returns_matching_emails_on_success(): void
    {
        $email_1 = new Email([]);
        $email_2 = new Email([]);

        $filterer = $this->createStub(EmailListFilterer::class);
        $filterer->method('filterEmails')->willReturn([$email_1, $email_2]);

        $subject = $this->newSubject(filterer: $filterer);
        $this->assertSame([$email_1, $email_2], $subject->assert());
    }
}
