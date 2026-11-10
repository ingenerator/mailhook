<?php

declare(strict_types=1);

namespace test\unit\Ingenerator\Mailhook;

use Ingenerator\Mailhook\Assert\NegativeAssertionRunner;
use Ingenerator\Mailhook\Assert\PositiveAssertionRunner;
use Ingenerator\Mailhook\Email;
use Ingenerator\Mailhook\EmailMatcher;
use Ingenerator\Mailhook\MailhookAsserter;
use PHPUnit\Framework\TestCase;

class MailhookAsserterTest extends TestCase
{
    private function newSubject(
        ?PositiveAssertionRunner $positive = null,
        ?NegativeAssertionRunner $negative = null,
    ): MailhookAsserter {
        return new MailhookAsserter(
            $positive ?? $this->createStub(PositiveAssertionRunner::class),
            $negative ?? $this->createStub(NegativeAssertionRunner::class),
        );
    }

    public function test_it_is_initializable(): void
    {
        $this->assertInstanceOf(MailhookAsserter::class, $this->newSubject());
    }

    public function test_it_runs_positive_assertions_with_matchers(): void
    {
        $positive_runner = $this->createMock(PositiveAssertionRunner::class);
        $matcher1 = $this->createStub(EmailMatcher::class);
        $matcher2 = $this->createStub(EmailMatcher::class);
        $email = new Email([]);

        $positive_runner
            ->expects($this->once())
            ->method('assert')
            ->with([$matcher1, $matcher2])
            ->willReturn([$email]);

        $subject = $this->newSubject(positive: $positive_runner);

        $this->assertSame([$email], $subject->emailsMatching($matcher1, $matcher2));
    }

    public function test_it_runs_negative_assertions_with_matchers(): void
    {
        $negative_runner = $this->createMock(NegativeAssertionRunner::class);
        $matcher1 = $this->createStub(EmailMatcher::class);
        $matcher2 = $this->createStub(EmailMatcher::class);

        $negative_runner
            ->expects($this->once())
            ->method('assert')
            ->with([$matcher1, $matcher2])
            ->willReturn([]);

        $subject = $this->newSubject(negative: $negative_runner);

        $this->assertNull($subject->noEmailMatching($matcher1, $matcher2));
    }

    public function test_it_runs_positive_assertions_and_returns_first_matching(): void
    {
        $positive_runner = $this->createMock(PositiveAssertionRunner::class);
        $matcher = $this->createStub(EmailMatcher::class);
        $email1 = new Email([]);
        $email2 = new Email([]);

        $positive_runner
            ->expects($this->once())
            ->method('assert')
            ->with([$matcher])
            ->willReturn([$email1, $email2]);

        $subject = $this->newSubject(positive: $positive_runner);

        $this->assertSame($email1, $subject->firstEmailMatching($matcher));
    }
}
