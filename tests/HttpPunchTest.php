<?php

namespace Hedii\HttpPunch\Tests;

use Hedii\HttpPunch\HttpPunch;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Response;

class HttpPunchTest extends TestCase
{
    /**
     * This method is called before the first test of this test class is run.
     */
    public static function setUpBeforeClass(): void
    {
        TestServer::start();
    }

    /** @test */
    public function it_should_punch_a_get_endpoint(): void
    {
        $puncher = new HttpPunch();
        $response = $puncher->punch($this->url('/get'));

        $this->assertSame($this->url('/get'), $response['url']);
        $this->assertTrue($response['success']);
        $this->assertSame(Response::HTTP_OK, $response['status_code']);
        $this->assertSame('OK', $response['message']);
        $this->assertTrue($response['transfer_time'] > 0);
    }

    /** @test */
    public function it_should_punch_a_post_endpoint(): void
    {
        $puncher = new HttpPunch();
        $response = $puncher->punch($this->url('/post'), 'post');

        $this->assertSame($this->url('/post'), $response['url']);
        $this->assertTrue($response['success']);
        $this->assertSame(Response::HTTP_OK, $response['status_code']);
        $this->assertSame('OK', $response['message']);
        $this->assertTrue($response['transfer_time'] > 0);
    }

    /** @test */
    public function it_should_report_correct_data_on_a_forbidden_endpoint(): void
    {
        $puncher = new HttpPunch();
        $response = $puncher->punch($this->url('/endpoint-forbidden'));

        $this->assertSame($this->url('/endpoint-forbidden'), $response['url']);
        $this->assertFalse($response['success']);
        $this->assertSame(Response::HTTP_FORBIDDEN, $response['status_code']);
        $this->assertSame('Forbidden', $response['message']);
        $this->assertTrue($response['transfer_time'] > 0);
    }

    /** @test */
    public function it_should_report_correct_data_on_an_error_endpoint(): void
    {
        $puncher = new HttpPunch();
        $response = $puncher->punch($this->url('/endpoint-error'));

        $this->assertSame($this->url('/endpoint-error'), $response['url']);
        $this->assertFalse($response['success']);
        $this->assertSame(Response::HTTP_INTERNAL_SERVER_ERROR, $response['status_code']);
        $this->assertSame('Internal Server Error', $response['message']);
        $this->assertTrue($response['transfer_time'] > 0);
    }

    /** @test */
    public function it_should_report_correct_data_on_a_not_existing_endpoint(): void
    {
        $puncher = new HttpPunch();
        $response = $puncher->punch('http://an_url_that_does_not_exists.tld');

        $this->assertSame('http://an_url_that_does_not_exists.tld', $response['url']);
        $this->assertFalse($response['success']);
        $this->assertNull($response['status_code']);
        $this->assertSame('Couldn\'t resolve host name', $response['message']);
        $this->assertTrue($response['transfer_time'] > 0);
    }

    /** @test */
    public function it_should_report_correct_data_on_a_redirect_endpoint(): void
    {
        $puncher = new HttpPunch();
        $response = $puncher->punch($this->url('/endpoint-redirect'));

        $this->assertSame($this->url('/endpoint-ok'), $response['url']);
        $this->assertTrue($response['success']);
        $this->assertSame(Response::HTTP_OK, $response['status_code']);
        $this->assertSame('OK', $response['message']);
        $this->assertTrue($response['transfer_time'] > 0);
    }

    /** @test */
    public function it_should_report_correct_data_on_request_timeout(): void
    {
        $puncher = new HttpPunch(1);
        $response = $puncher->punch($this->url('/endpoint-timeout'));

        $this->assertSame($this->url('/endpoint-timeout'), $response['url']);
        $this->assertFalse($response['success']);
        $this->assertNull($response['status_code']);
        $this->assertSame('Timeout was reached', $response['message']);
        $this->assertTrue($response['transfer_time'] > 1);
    }

    /** @test */
    public function it_should_punch_an_https_endpoint(): void
    {
        $puncher = new HttpPunch();
        $response = $puncher->punch('https://www.google.fr');

        $this->assertSame('https://www.google.fr', $response['url']);
        $this->assertTrue($response['success']);
        $this->assertSame(Response::HTTP_OK, $response['status_code']);
        $this->assertSame('OK', $response['message']);
        $this->assertTrue($response['transfer_time'] > 0);
    }

    /** @test */
    public function it_should_set_the_outgoing_ip_address(): void
    {
        $puncher = new HttpPunch();
        $response = $puncher->setIp('1.2.3.4')->punch($this->url('/get'));

        $this->assertSame('Failed binding local connection end', $response['message']);
    }

    /**
     * Build a test url from a given endpoint path.
     *
     * @param string $url
     * @return string
     */
    private function url(string $url): string
    {
        return vsprintf('%s/%s', [
            'http://localhost:' . getenv('TEST_SERVER_PORT'),
            ltrim($url, '/'),
        ]);
    }
}
