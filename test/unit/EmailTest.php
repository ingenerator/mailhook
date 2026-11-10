<?php

declare(strict_types=1);

namespace test\unit\Ingenerator\Mailhook;

use Ingenerator\Mailhook\Email;
use PHPUnit\Framework\TestCase;

class EmailTest extends TestCase
{
    private function newSubject(array $props = []): Email
    {
        return new Email($props);
    }

    public function test_it_is_initializable(): void
    {
        $this->assertInstanceOf(Email::class, $this->newSubject());
    }

    public function test_it_filters_empty_links_by_regular_expression(): void
    {
        $subject = $this->newSubject(['links' => []]);
        $this->assertSame([], $subject->getLinksMatching('/test/'));
    }

    public function test_it_filters_links_by_regular_expression(): void
    {
        $subject = $this->newSubject([
            'links' => [
                'https://www.ingenerator.com/test/matching-url',
                'https://www.ingenerator.com/test/notmatch-url',
            ],
        ]);
        $this->assertSame(
            ['https://www.ingenerator.com/test/matching-url'],
            $subject->getLinksMatching('_matching-url$_')
        );
    }
}
