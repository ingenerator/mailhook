<?php

declare(strict_types=1);

namespace test\unit\Ingenerator\Mailhook\Matcher;

use Ingenerator\Mailhook\Email;
use Ingenerator\Mailhook\EmailMatcher;
use Ingenerator\Mailhook\Matcher\EmailSentToMatcher;

class EmailSentToMatcherTest extends AbstractEmailMatcherTestCase
{
    protected function createMatcher(): EmailMatcher
    {
        return $this->newSubject('test@ingenerator.com');
    }

    private function newSubject(string $to = 'test@ingenerator.com'): EmailSentToMatcher
    {
        return new EmailSentToMatcher($to);
    }

    public function test_it_is_initializable(): void
    {
        $subject = $this->newSubject();
        $this->assertInstanceOf(EmailSentToMatcher::class, $subject);
    }

    public function test_it_does_not_match_email_sent_to_someone_else(): void
    {
        $subject = $this->newSubject();
        $email = new Email(['to' => 'someoneelse@ingenerator.com']);
        $this->assertFalse($subject->matches($email));
    }

    public function test_it_matches_email_sent_to_recipient(): void
    {
        $subject = $this->newSubject();
        $email = new Email(['to' => 'test@ingenerator.com']);
        $this->assertTrue($subject->matches($email));
    }

    public function test_it_describes_with_requested_recipient(): void
    {
        $subject = $this->newSubject();
        $this->assertSame('To "test@ingenerator.com"', (string) $subject);
    }
}
