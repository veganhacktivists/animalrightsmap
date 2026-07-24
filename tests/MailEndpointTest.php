<?php

use PHPUnit\Framework\TestCase;

class MailEndpointTest extends TestCase
{
    private const HOST = '127.0.0.1:8943';

    private static $server;

    public static function setUpBeforeClass(): void
    {
        if (file_exists(__DIR__.'/../.env')) {
            self::markTestSkipped('A .env file is present; tests would send real mail.');
        }

        self::$server = proc_open(
            ['php', '-d', 'display_errors=0', '-S', self::HOST, '-t', __DIR__.'/../public'],
            [1 => ['file', '/dev/null', 'w'], 2 => ['file', '/dev/null', 'w']],
            $pipes
        );

        for ($i = 0; $i < 50; $i++) {
            if (@fsockopen('127.0.0.1', 8943)) {
                return;
            }
            usleep(100_000);
        }

        self::fail('Test server did not start');
    }

    public static function tearDownAfterClass(): void
    {
        if (self::$server) {
            proc_terminate(self::$server);
            proc_close(self::$server);
        }
    }

    private function request(string $method, array $data = [], ?string $ip = null): array
    {
        $ip ??= '10.'.random_int(0, 255).'.'.random_int(0, 255).'.'.random_int(1, 254);

        $body = http_build_query($data);
        $response = file_get_contents('http://'.self::HOST.'/mail.php', false, stream_context_create([
            'http' => [
                'method'        => $method,
                'header'        => "Content-Type: application/x-www-form-urlencoded\r\nX-Forwarded-For: {$ip}",
                'content'       => $body,
                'ignore_errors' => true,
            ],
        ]));

        preg_match('{HTTP/\S+ (\d{3})}', $http_response_header[0], $match);

        return [(int) $match[1], $response];
    }

    private function validSubmission(): array
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

    public function testGetIsRejected(): void
    {
        [$status] = $this->request('GET');

        $this->assertSame(405, $status);
    }

    public function testMissingFieldsAreRejected(): void
    {
        [$status] = $this->request('POST', ['name' => 'Test Person']);

        $this->assertSame(400, $status);
    }

    public function testInvalidEmailIsRejected(): void
    {
        [$status] = $this->request('POST', ['email' => 'not-an-email'] + $this->validSubmission());

        $this->assertSame(400, $status);
    }

    public function testMissingMailgunKeyReturnsCleanError(): void
    {
        [$status, $body] = $this->request('POST', $this->validSubmission());

        $this->assertSame(500, $status);
        $this->assertSame('"Error"', $body);
    }

    public function testSixthRequestInWindowIsRateLimited(): void
    {
        $ip = '192.0.2.'.random_int(1, 254);

        for ($i = 1; $i <= 5; $i++) {
            [$status] = $this->request('POST', [], $ip);
            $this->assertSame(400, $status, "Request {$i} should not be rate limited");
        }

        [$status] = $this->request('POST', [], $ip);
        $this->assertSame(429, $status);
    }
}
