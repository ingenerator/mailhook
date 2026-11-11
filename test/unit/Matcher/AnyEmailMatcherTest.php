<?php

declare(strict_types=1);

namespace test\unit\Ingenerator\Mailhook\Matcher;

use Ingenerator\Mailhook\Email;
use Ingenerator\Mailhook\EmailMatcher;
use Ingenerator\Mailhook\Matcher\AnyEmailMatcher;

class AnyEmailMatcherTest extends AbstractEmailMatcherTestCase
{
    protected function createMatcher(): EmailMatcher
    {
        return $this->newSubject();
    }

    private function newSubject(): AnyEmailMatcher
    {
        return new AnyEmailMatcher();
    }

    public function test_it_is_initializable(): void
    {
        $subject = $this->newSubject();
        $this->assertInstanceOf(AnyEmailMatcher::class, $subject);
    }

    public function test_it_describes_itself_as_an_any_matcher(): void
    {
        $subject = $this->newSubject();
        $this->assertSame('Any Email', (string) $subject);
    }

    public function test_it_matches_any_email(): void
    {
        $subject = $this->newSubject();
        $email = new Email([]);
        $this->assertTrue($subject->matches($email));
    }
}
