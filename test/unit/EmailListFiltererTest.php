<?php

declare(strict_types=1);

namespace test\unit\Ingenerator\Mailhook;

use Ingenerator\Mailhook\Email;
use Ingenerator\Mailhook\EmailListFilterer;
use Ingenerator\Mailhook\EmailMatcher;
use PHPUnit\Framework\TestCase;

class EmailListFiltererTest extends TestCase
{
    private function newSubject(): EmailListFilterer
    {
        return new EmailListFilterer();
    }

    public function test_it_is_initializable(): void
    {
        $subject = $this->newSubject();
        $this->assertInstanceOf(EmailListFilterer::class, $subject);
    }

    public function test_it_filters_empty_array_to_empty_array(): void
    {
        $subject = $this->newSubject();
        $this->assertSame([], $subject->filterEmails([], []));
    }

    public function test_it_filters_emails_to_empty_array_with_no_matchers(): void
    {
        $subject = $this->newSubject();
        $this->assertSame([], $subject->filterEmails([new Email([])], []));
    }

    public function test_it_filters_emails_to_empty_array_when_matcher_does_not_match(): void
    {
        $matcher = $this->createStub(EmailMatcher::class);
        $matcher->method('matches')->willReturn(false);

        $subject = $this->newSubject();
        $this->assertSame([], $subject->filterEmails([new Email([])], [$matcher]));
    }

    public function test_it_filters_emails_to_empty_array_when_one_matcher_matches_and_one_does_not(): void
    {
        $yes_matcher = $this->createStub(EmailMatcher::class);
        $yes_matcher->method('matches')->willReturn(true);

        $no_matcher = $this->createStub(EmailMatcher::class);
        $no_matcher->method('matches')->willReturn(false);

        $subject = $this->newSubject();
        $this->assertSame([], $subject->filterEmails([new Email([])], [$yes_matcher, $no_matcher]));
    }

    public function test_it_returns_all_emails_when_matcher_matches(): void
    {
        $email = new Email([]);
        $matcher = $this->givenMatcherMatches($email);

        $subject = $this->newSubject();
        $this->assertSame([$email], $subject->filterEmails([$email], [$matcher]));
    }

    public function test_it_returns_matching_emails_when_matcher_matches_some(): void
    {
        $good = new Email([]);
        $bad = new Email([]);
        $matcher = $this->givenMatcherMatches($good);

        $subject = $this->newSubject();
        $this->assertSame([$good], $subject->filterEmails([$good, $bad], [$matcher]));
    }

    public function test_it_returns_emails_matching_all_matchers_with_some_good_and_some_bad(): void
    {
        $both_good = new Email([]);
        $both_bad = new Email([]);
        $one_good = new Email([]);
        $two_good = new Email([]);

        $matcher_1 = $this->givenMatcherMatches([$both_good, $one_good]);
        $matcher_2 = $this->givenMatcherMatches([$both_good, $two_good]);

        $subject = $this->newSubject();
        $this->assertSame(
            [$both_good],
            $subject->filterEmails(
                [$both_good, $one_good, $two_good, $both_bad],
                [$matcher_1, $matcher_2]
            )
        );
    }

    /**
     * @param Email|Email[] $emails
     */
    private function givenMatcherMatches(Email|array $emails): EmailMatcher
    {
        $emails = \is_array($emails) ? $emails : [$emails];
        $matcher = $this->createStub(EmailMatcher::class);
        $matcher->method('matches')->willReturnCallback(
            fn(Email $email) => \in_array($email, $emails, true)
        );

        return $matcher;
    }
}
