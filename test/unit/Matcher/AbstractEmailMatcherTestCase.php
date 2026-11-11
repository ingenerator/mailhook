<?php

declare(strict_types=1);

namespace test\unit\Ingenerator\Mailhook\Matcher;

use Ingenerator\Mailhook\EmailMatcher;
use PHPUnit\Framework\TestCase;

abstract class AbstractEmailMatcherTestCase extends TestCase
{
    abstract protected function createMatcher(): EmailMatcher;

    public function test_it_is_an_email_matcher(): void
    {
        $this->assertInstanceOf(EmailMatcher::class, $this->createMatcher());
    }
}
