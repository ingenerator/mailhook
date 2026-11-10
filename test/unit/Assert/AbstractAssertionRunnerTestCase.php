<?php

declare(strict_types=1);

namespace test\unit\Ingenerator\Mailhook\Assert;

use Ingenerator\Mailhook\Assert\AssertionRunner;
use Ingenerator\Mailhook\Email;
use Ingenerator\Mailhook\EmailAssertionFailedException;
use Ingenerator\Mailhook\EmailListFilterer;
use Ingenerator\Mailhook\EmailMatcher;
use Ingenerator\Mailhook\Mailhook;
use PHPUnit\Framework\TestCase;

abstract class AbstractAssertionRunnerTestCase extends TestCase
{
    abstract protected function createRunner(Mailhook $mailhook, EmailListFilterer $filterer): AssertionRunner;

    public function test_it_filters_received_emails(): void
    {
        $email = new Email([]);
        $mailhook = $this->createStub(Mailhook::class);
        $mailhook->method('getEmails')->willReturn([$email]);

        $filterer = $this->createMock(EmailListFilterer::class);
        $filterer->expects($this->once())
            ->method('filterEmails')
            ->with([$email], $this->anything());

        $runner = $this->createRunner($mailhook, $filterer);
        $this->tryAssertWith($runner);
    }

    public function test_it_filters_with_multiple_matchers(): void
    {
        $matcher_1 = $this->createStub(EmailMatcher::class);
        $matcher_2 = $this->createStub(EmailMatcher::class);
        $matcher_3 = $this->createStub(EmailMatcher::class);
        $matchers = [$matcher_1, $matcher_2, $matcher_3];

        $mailhook = $this->createStub(Mailhook::class);
        $mailhook->method('getEmails')->willReturn([]);

        $filterer = $this->createMock(EmailListFilterer::class);
        $filterer->expects($this->once())
            ->method('filterEmails')
            ->with($this->anything(), $matchers);

        $runner = $this->createRunner($mailhook, $filterer);
        $this->tryAssertWith($runner, $matchers);
    }

    public function test_it_filters_with_one_matcher(): void
    {
        $matcher = $this->createStub(EmailMatcher::class);
        $matchers = [$matcher];

        $mailhook = $this->createStub(Mailhook::class);
        $mailhook->method('getEmails')->willReturn([]);

        $filterer = $this->createMock(EmailListFilterer::class);
        $filterer->expects($this->once())
            ->method('filterEmails')
            ->with($this->anything(), $matchers);

        $runner = $this->createRunner($mailhook, $filterer);
        $this->tryAssertWith($runner, $matchers);
    }

    public function test_it_refreshes_mailhook(): void
    {
        $mailhook = $this->createMock(Mailhook::class);
        $mailhook->expects($this->once())->method('refresh');
        $mailhook->method('getEmails')->willReturn([]);

        $filterer = $this->createStub(EmailListFilterer::class);

        $runner = $this->createRunner($mailhook, $filterer);
        $this->tryAssertWith($runner);
    }

    /**
     * @param EmailMatcher[] $matchers
     */
    protected function tryAssertWith(AssertionRunner $runner, array $matchers = []): void
    {
        try {
            $runner->assert($matchers);
        } catch (EmailAssertionFailedException) {
            // OK
        }
    }
}
