<?php

declare(strict_types=1);

namespace App\Services\Status\Providers;

use Throwable;
use function filter_var;
use InvalidArgumentException;
use const FILTER_VALIDATE_URL;
use GuzzleHttp\ClientInterface;
use App\Contracts\Services\Status\StatusServiceProvider;

class WebsiteProvider implements StatusServiceProvider
{
    /**
     * Guzzle HTTP client.
     *
     * @var \GuzzleHttp\ClientInterface
     */
    protected $httpClient;

    /**
     * Route to hit to check website status.
     *
     * @var string
     */
    protected $routeToPing;

    /**
     * Construct a new website status provider.
     *
     * @param \GuzzleHttp\ClientInterface $httpClient
     * @param string                      $routeToPing
     *
     * @return void
     */
    public function __construct(ClientInterface $httpClient, string $routeToPing = 'https://example.com/')
    {
        $this->httpClient = $httpClient;

        if (filter_var($routeToPing, FILTER_VALIDATE_URL) === false) {
            throw new InvalidArgumentException("Invalid URL [{$routeToPing}] provided.");
        }

        $this->routeToPing = $routeToPing;
    }

    /**
     * {@inheritdoc}
     */
    public function getStatus(): array
    {
        try {
            $response = $this->httpClient->request('GET', $this->routeToPing);

            $code = (int) $response->getStatusCode();

            // Check for a successful HTTP response.
            if ($code >= 200 && $code < 300) {
                return ['status' => StatusServiceProvider::STATUS_OK];
            }
        } catch (Throwable $e) {
            // Swallow exceptions on purpose.
        }

        return ['status' => StatusServiceProvider::STATUS_ERROR];
    }
}
