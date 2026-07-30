<?php

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

require_once __DIR__.'/../../src/submission.php';

class SubmissionTest extends TestCase
{
    private function validFields(): array
    {
        return [
            'name'             => 'Test Person',
            'email'            => 'test@example.com',
            'groupNames'       => 'Test Group',
            'socialMediaLinks' => 'https://example.com/group',
            'regions'          => 'London',
            'message'          => 'Hello',
        ];
    }

    public function testClientIpUsesRemoteAddrWithoutForwardedHeader(): void
    {
        $this->assertSame('203.0.113.7', arm_client_ip(['REMOTE_ADDR' => '203.0.113.7']));
    }

    public function testClientIpFallsBackToUnknown(): void
    {
        $this->assertSame('unknown', arm_client_ip([]));
    }

    public function testClientIpPrefersForwardedHeader(): void
    {
        $this->assertSame('203.0.113.7', arm_client_ip([
            'HTTP_X_FORWARDED_FOR' => '203.0.113.7',
            'REMOTE_ADDR'          => '10.0.0.1',
        ]));
    }

    public function testClientIpTakesTheLastForwardedEntry(): void
    {
        // A client can prepend anything it likes; Traefik appends the real
        // peer last, so only the last entry is trustworthy.
        $this->assertSame('198.51.100.9', arm_client_ip([
            'HTTP_X_FORWARDED_FOR' => '1.2.3.4, 5.6.7.8, 198.51.100.9',
        ]));
    }

    public function testClientIpTrimsWhitespace(): void
    {
        $this->assertSame('198.51.100.9', arm_client_ip([
            'HTTP_X_FORWARDED_FOR' => "1.2.3.4,   198.51.100.9   ",
        ]));
    }

    public function testValidSubmissionIsReturnedTrimmed(): void
    {
        $fields = arm_validate_submission([
            'name'             => '  Test Person  ',
            'email'            => '  test@example.com  ',
            'groupNames'       => 'Test Group',
            'socialMediaLinks' => 'https://example.com/group',
            'regions'          => 'London',
            'message'          => '  Hello  ',
        ]);

        $this->assertSame('Test Person', $fields['name']);
        $this->assertSame('test@example.com', $fields['email']);
        $this->assertSame('Hello', $fields['message']);
    }

    public function testMessageIsOptional(): void
    {
        $fields = $this->validFields();
        unset($fields['message']);

        $result = arm_validate_submission($fields);

        $this->assertNotNull($result);
        $this->assertSame('', $result['message']);
    }

    public static function requiredFields(): array
    {
        return [
            ['name'],
            ['email'],
            ['groupNames'],
            ['socialMediaLinks'],
            ['regions'],
        ];
    }

    #[DataProvider('requiredFields')]
    public function testMissingRequiredFieldIsRejected(string $field): void
    {
        $fields = $this->validFields();
        unset($fields[$field]);

        $this->assertNull(arm_validate_submission($fields));
    }

    #[DataProvider('requiredFields')]
    public function testWhitespaceOnlyRequiredFieldIsRejected(string $field): void
    {
        $fields = $this->validFields();
        $fields[$field] = "  \t ";

        $this->assertNull(arm_validate_submission($fields));
    }

    public function testMalformedEmailIsRejected(): void
    {
        $fields = $this->validFields();
        $fields['email'] = 'not-an-email';

        $this->assertNull(arm_validate_submission($fields));
    }

    public function testHeaderInjectionInEmailIsRejected(): void
    {
        $fields = $this->validFields();
        $fields['email'] = "test@example.com\nBcc: victim@example.org";

        $this->assertNull(arm_validate_submission($fields));
    }

    public function testHtmlEscapesEveryField(): void
    {
        $html = arm_submission_html([
            'name'             => '<script>alert(1)</script>',
            'email'            => 'test@example.com',
            'groupNames'       => '"quoted"',
            'socialMediaLinks' => "'single'",
            'regions'          => 'Fish & Chips',
            'message'          => '<b>bold</b>',
        ]);

        $this->assertStringNotContainsString('<script>', $html);
        $this->assertStringNotContainsString('<b>bold</b>', $html);
        $this->assertStringContainsString('&lt;script&gt;', $html);
        $this->assertStringContainsString('&quot;quoted&quot;', $html);
        $this->assertStringContainsString('&#039;single&#039;', $html);
        $this->assertStringContainsString('Fish &amp; Chips', $html);
    }

    public function testHtmlKeepsMessageLineBreaks(): void
    {
        $fields = $this->validFields();
        $fields['message'] = "first\nsecond";

        $this->assertStringContainsString('first<br />'."\n".'second', arm_submission_html($fields));
    }

    public function testHtmlPreservesUnicode(): void
    {
        $fields = $this->validFields();
        $fields['regions'] = 'Zürich, 東京';

        $this->assertStringContainsString('Zürich, 東京', arm_submission_html($fields));
    }
}
