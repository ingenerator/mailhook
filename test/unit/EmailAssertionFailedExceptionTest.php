<?php

declare(strict_types=1);

namespace test\unit\Ingenerator\Mailhook;

use Ingenerator\Mailhook\EmailAssertionFailedException;
use Ingenerator\Mailhook\EmailMatcher;
use PHPUnit\Framework\TestCase;

class EmailAssertionFailedExceptionTest extends TestCase
{
    private function newSubject(string $message = 'Message', array $matchers = []): EmailAssertionFailedException
    {
        return new EmailAssertionFailedException($message, $matchers);
    }

    public function test_it_is_initializable(): void
    {
        $subject = $this->newSubject();
        $this->assertInstanceOf(EmailAssertionFailedException::class, $subject);
    }

    public function test_it_includes_message_in_message(): void
    {
        $subject = $this->newSubject('Problem email', []);
        $this->assertMatchesRegularExpression('/^Problem email/', $subject->getMessage());
    }

    public function test_it_describes_when_no_matchers(): void
    {
        $subject = $this->newSubject('Problem email', []);
        $this->assertMatchesRegularExpression('/\nMatchers: none$/', $subject->getMessage());
    }

    public function test_it_describes_matchers_in_message(): void
    {
        $matcher1 = $this->createStub(EmailMatcher::class);
        $matcher1->method('__toString')->willReturn('Containing "here"');

        $matcher2 = $this->createStub(EmailMatcher::class);
        $matcher2->method('__toString')->willReturn('To test@ingenerator.com');

        $subject = $this->newSubject('Problem email', [$matcher1, $matcher2]);
        $this->assertMatchesRegularExpression(
            '/\nMatchers: Containing "here", To test@ingenerator.com$/',
            $subject->getMessage()
        );
    }
}
