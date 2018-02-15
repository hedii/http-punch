<?php

namespace Hedii\HttpPunch;

use Exception;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\TransferStats;

class HttpPunch
{
    /** @var int */
    public $connectionTimeout;

    /** @var int */
    public $requestTimeout;

    /** @var string */
    private $effectiveUri;

    /** @var bool */
    private $success = false;

    /** @var null|int */
    private $statusCode = null;

    /** @var string */
    private $message;

    /** @var float */
    private $transferTime = 0;

    /** @var string */
    private $ip = '0.0.0.0';

    /** @var array */
    private $headers = [];

    /**
     * HttpPunch constructor.
     *
     * @param int $requestTimeout
     * @param int $connectionTimeout
     */
    public function __construct(int $requestTimeout = 30, int $connectionTimeout = 10)
    {
        $this->requestTimeout = $requestTimeout;
        $this->connectionTimeout = $connectionTimeout;
    }

    /**
     * Set the outgoing ip address.
     *
     * @param string $ip
     * @return \Hedii\HttpPunch\HttpPunch
     */
    public function setIp(string $ip): self
    {
        $this->ip = $ip;

        return $this;
    }

    /**
     * Set the request headers.
     *
     * @param array $headers
     * @return \Hedii\HttpPunch\HttpPunch
     */
    public function setHeaders(array $headers): self
    {
        $this->headers = $headers;

        return $this;
    }

    /**
     * Perform a punch on a given url.
     *
     * @param string $url
     * @param string $method
     * @param array $body
     * @return array
     */
    public function punch(string $url, string $method = 'get', array $body = []): array
    {
        $this->effectiveUri = $url;

        try {
            $response = $this->client()->request($method, $url, [
                'form_params' => $body,
                'on_stats' => function (TransferStats $stats) {
                    $this->effectiveUri = (string) $stats->getEffectiveUri();
                    $this->transferTime = $stats->getTransferTime();
                }
            ]);

            $this->success = true;
            $this->statusCode = $response->getStatusCode();
            $this->message = $response->getReasonPhrase();
        } catch (ClientException $e) {
            $this->success = false;
            $this->statusCode = $e->getResponse()->getStatusCode();
            $this->message = $e->getResponse()->getReasonPhrase();
        } catch (RequestException $e) {
            if ($e->hasResponse()) {
                $this->statusCode = $e->getResponse()->getStatusCode();
                $this->message = $e->getResponse()->getReasonPhrase();
            } else {
                $this->message = isset($e->getHandlerContext()['errno'])
                    ? curl_strerror($e->getHandlerContext()['errno'])
                    : $e->getMessage();
            }
        } catch (Exception $e) {
            $this->message = 'Unknown Error';
        }

        return [
            'url' => $this->effectiveUri,
            'success' => $this->success,
            'status_code' => $this->statusCode,
            'message' => $this->message,
            'transfer_time' => $this->transferTime
        ];
    }

    /**
     * Get the http client.
     *
     * @return \GuzzleHttp\Client
     */
    protected function client(): Client
    {
        return new Client([
            'connect_timeout' => $this->connectionTimeout,
            'timeout' => $this->requestTimeout,
            'verify' => false,
            'headers' => $this->headers,
            'curl' => [
                CURLOPT_INTERFACE => $this->ip
            ]
        ]);
    }
}
