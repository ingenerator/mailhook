<?php

declare(strict_types=1);

namespace test\unit\Ingenerator\Mailhook\Assert;

use Ingenerator\Mailhook\Assert\AssertionRunner;
use Ingenerator\Mailhook\Assert\NegativeAssertionRunner;
use Ingenerator\Mailhook\Email;
use Ingenerator\Mailhook\EmailAssertionFailedException;
use Ingenerator\Mailhook\EmailListFilterer;
use Ingenerator\Mailhook\Mailhook;

class NegativeAssertionRunnerTest extends AbstractAssertionRunnerTestCase
{
    protected function createRunner(Mailhook $mailhook, EmailListFilterer $filterer): AssertionRunner
    {
        return $this->newSubject($mailhook, $filterer);
    }

    private function newSubject(?Mailhook $mailhook = null, ?EmailListFilterer $filterer = null): NegativeAssertionRunner
    {
        return new NegativeAssertionRunner(
            $mailhook ?? $this->createStub(Mailhook::class),
            $filterer ?? $this->createStub(EmailListFilterer::class),
        );
    }

    public function test_it_is_initializable(): void
    {
        $subject = $this->newSubject();
        $this->assertInstanceOf(NegativeAssertionRunner::class, $subject);
    }

    public function test_it_returns_empty_array_if_no_matching_emails(): void
    {
        $filterer = $this->createStub(EmailListFilterer::class);
        $filterer->method('filterEmails')->willReturn([]);

        $subject = $this->newSubject(filterer: $filterer);
        $this->assertSame([], $subject->assert());
    }

    public function test_it_throws_if_any_matching_emails(): void
    {
        $email_1 = new Email([]);
        $email_2 = new Email([]);

        $filterer = $this->createStub(EmailListFilterer::class);
        $filterer->method('filterEmails')->willReturn([$email_1, $email_2]);

        $subject = $this->newSubject(filterer: $filterer);

        $this->expectException(EmailAssertionFailedException::class);
        $subject->assert();
    }
}
