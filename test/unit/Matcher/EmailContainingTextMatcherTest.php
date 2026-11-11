<?php

declare(strict_types=1);

namespace test\unit\Ingenerator\Mailhook\Matcher;

use Ingenerator\Mailhook\Email;
use Ingenerator\Mailhook\EmailMatcher;
use Ingenerator\Mailhook\Matcher\EmailContainingTextMatcher;

class EmailContainingTextMatcherTest extends AbstractEmailMatcherTestCase
{
    protected function createMatcher(): EmailMatcher
    {
        return $this->newSubject('text');
    }

    private function newSubject(string $text = 'text'): EmailContainingTextMatcher
    {
        return new EmailContainingTextMatcher($text);
    }

    public function test_it_is_initializable(): void
    {
        $subject = $this->newSubject();
        $this->assertInstanceOf(EmailContainingTextMatcher::class, $subject);
    }

    public function test_it_does_not_match_email_with_empty_text(): void
    {
        $subject = $this->newSubject();
        $email_empty = new Email(['content' => '']);
        $this->assertFalse($subject->matches($email_empty));

        $email_null = new Email(['content' => null]);
        $this->assertFalse($subject->matches($email_null));
    }

    public function test_it_matches_email_beginning_with_text(): void
    {
        $subject = $this->newSubject('Some text');
        $email = new Email(['content' => 'Some text that matches']);
        $this->assertTrue($subject->matches($email));
    }

    public function test_it_matches_email_containing_text(): void
    {
        $subject = $this->newSubject('Some text');
        $email = new Email(['content' => 'This email has Some text that matches']);
        $this->assertTrue($subject->matches($email));
    }

    public function test_it_describes_with_search_text(): void
    {
        $subject = $this->newSubject('Some text');
        $this->assertSame('With text "Some text"', (string) $subject);
    }
}
