<?php

declare(strict_types=1);

namespace App\Service;

use Symfony\Component\HttpClient\Exception\JsonException;
use Symfony\Component\HttpClient\Exception\TransportException;
use Symfony\Component\HttpClient\Response\CurlResponse;
use Symfony\Contracts\HttpClient\HttpClientInterface;

/**
 * Class VersionFetcher.
 */
class VersionFetcher
{
    /**
     * @var HttpClientInterface
     */
    private $client;

    private static $uris = [
        'prod' => [
            'user' => 'http://localhost/version1',
            'newsletter' => 'http://localhost/version2',
            'comment' => 'http://localhost/version3',
            'connect' => 'http://localhost/version4',
        ],
    ];

    /**
     * VersionFetcher constructor.
     *
     * @param HttpClientInterface $client
     */
    public function __construct(HttpClientInterface $client)
    {
        $this->client = $client;
    }

    public function get(): array
    {
        $responses = [];

        foreach (self::$uris as $key => $uri) {
            foreach ($uri as $project => $u) {
                try {
                    $responses[$key][$project] = $this->client->request('GET', $u);
                } catch (TransportException $e) {
                    $responses[$key][$project] = null;
                }
            }
        }

        foreach ($responses as $key => $response) {
            /* @var CurlResponse $r */
            foreach ($response as $project => $r) {
                try {
                    $data = $r->toArray();
                } catch (JsonException | TransportException $e) {
                    continue;
                }

                $result[$key][$project]['version'] = $data['version'] ?? null;
                $result[$key][$project]['date'] = $data['deployed_at'] ?? null;
            }
        }

        return $result ?? [];
    }
}
