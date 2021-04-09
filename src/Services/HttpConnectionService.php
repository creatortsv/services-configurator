<?php

namespace App\Services;

use Creatortsv\ServicesConfiguratorBundle\Services\ServiceInterface;
use Symfony\Component\HttpClient\MockHttpClient;
use Symfony\Component\HttpClient\Response\MockResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Constraints\All;
use Symfony\Component\Validator\Constraints\Choice;
use Symfony\Component\Validator\Constraints\Collection;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class HttpConnectionService extends ServiceAbstract implements ServiceInterface
{
    const FIELD_MAINTENANCE = 'maintenance_mode';
    const FIELD_TITLE = 'title';
    const FIELD_FRIENDS = 'friends';
    const FIELD_PATRIC_IQ = 'patric_iq';

    const STORAGE_FILE = __DIR__.'/../../var/http.service.json';

    protected HttpClientInterface $client;
    protected string $url;

    public function __construct(string $url, HttpClientInterface $client)
    {
        $this->client = $client;
        $this->url = $url;

        if (!file_exists(self::STORAGE_FILE) || !filesize(self::STORAGE_FILE)) {
            $handle = fopen(self::STORAGE_FILE, 'w');
            fwrite($handle, json_encode(self::defaultValues()));
            fclose($handle);
        }
    }

    /**
     * @return HttpConnectionService
     */
    public function configure(): self
    {
        return $this;
    }

    /**
     * @return array
     */
    public function fetchSettings(): array
    {
        /* Эмулируем клиента микросервиса */
        $this->client = new MockHttpClient(function (): MockResponse {
            $handle = fopen(self::STORAGE_FILE, 'r');
            $stream = stream_get_contents($handle);
            fclose($handle);
            return new MockResponse($stream);
        });

        return json_decode($this
            ->client
            ->request(Request::METHOD_GET, $this->url)
            ->getContent(), true);
    }

    /**
     * @return array
     */
    public function saveSettings(array $settings): array
    {
        $this->validate($settings, self::constraints());
        $this->client = new MockHttpClient(function () use ($settings): MockResponse {
            $handle = fopen(self::STORAGE_FILE, 'r+');
            $stream = json_decode(stream_get_contents($handle), true);
            $stream = array_merge($stream, $settings);
            $stream = json_encode($stream);
            fclose($handle);
            $handle = fopen(self::STORAGE_FILE, 'w');
            fwrite($handle, $stream);
            fclose($handle);
            return new MockResponse($stream);
        });

        return json_decode($this
            ->client
            ->request(Request::METHOD_POST, $this->url, $settings)
            ->getContent(), true);
    }

    /**
     * @return array
     */
    public static function defaultValues(): array
    {
        return [
            self::FIELD_MAINTENANCE => '1',
            self::FIELD_TITLE => 'Ocean service',
            self::FIELD_PATRIC_IQ => '-34',
            self::FIELD_FRIENDS => [
                'Patric',
                'Mr. Crubs',
            ],
        ];
    }

    /**
     * @return Symfony\Component\Validator\Constraints\Collection
     */
    protected static function constraints(): Collection
    {
        return new Collection([
            self::FIELD_MAINTENANCE => new Choice(['1', '2']),
            self::FIELD_TITLE       => new Length(['min' => 2]),
            self::FIELD_PATRIC_IQ   => new Length(['min' => -100]),
            self::FIELD_FRIENDS     => new All([
                new Length(['min' => 3]),
            ]),
        ]);
    }
}