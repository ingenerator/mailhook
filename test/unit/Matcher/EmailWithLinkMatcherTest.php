<?php

declare(strict_types=1);

namespace test\unit\Ingenerator\Mailhook\Matcher;

use Ingenerator\Mailhook\Email;
use Ingenerator\Mailhook\EmailMatcher;
use Ingenerator\Mailhook\Matcher\EmailWithLinkMatcher;

class EmailWithLinkMatcherTest extends AbstractEmailMatcherTestCase
{
    protected function createMatcher(): EmailMatcher
    {
        return $this->newSubject();
    }

    private function newSubject(?string $pattern = null): EmailWithLinkMatcher
    {
        return new EmailWithLinkMatcher($pattern);
    }

    public function test_it_is_initializable(): void
    {
        $subject = $this->newSubject();
        $this->assertInstanceOf(EmailWithLinkMatcher::class, $subject);
    }

    public function test_it_does_not_match_email_with_no_links_when_no_pattern(): void
    {
        $subject = $this->newSubject(null);
        $email = new Email(['links' => []]);
        $this->assertFalse($subject->matches($email));
    }

    public function test_it_does_not_match_email_with_no_links_when_pattern(): void
    {
        $subject = $this->newSubject('/reset/');
        $email = new Email(['links' => ['http://foo.bar/other']]);
        $this->assertFalse($subject->matches($email));
    }

    public function test_it_matches_email_with_any_link_when_pattern_is_empty(): void
    {
        $subject = $this->newSubject(null);
        $email = new Email(['links' => ['http://foo.bar']]);
        $this->assertTrue($subject->matches($email));
    }

    public function test_it_matches_email_with_matching_link_when_pattern_provided(): void
    {
        $subject = $this->newSubject('/reset/');
        $email = new Email(['links' => ['http://foo.bar/reset']]);
        $this->assertTrue($subject->matches($email));
    }

    public function test_it_describes_with_pattern_when_provided(): void
    {
        $subject = $this->newSubject('/reset/');
        $this->assertSame('With link matching "/reset/"', (string) $subject);
    }

    public function test_it_describes_without_pattern_when_empty(): void
    {
        $subject = $this->newSubject(null);
        $this->assertSame('With any link', (string) $subject);
    }
}
