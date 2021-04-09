<?php

namespace App\Services;

use Creatortsv\ServicesConfiguratorBundle\Services\ServiceInterface;
use Symfony\Component\HttpClient\MockHttpClient;
use Symfony\Component\HttpClient\Response\MockResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\UnprocessableEntityHttpException;
use Symfony\Component\Validator\Validation;
use Symfony\Component\Validator\Constraints\{
    All,
    Choice,
    Collection,
    Email,
    Length,
};
use Symfony\Contracts\HttpClient\{
    HttpClientInterface,
    ResponseInterface,
};

class ApiConnectionService extends ServiceAbstract implements ServiceInterface
{
    const SETTING_MAINTENANCE = 'maintenance_mode';
    const SETTING_TITLE = 'title';
    const SETTING_CONTACTS = 'contacts';

    /**
     * Для эмитации хранилища подключаемого сервиса
     */
    const STORAGE_FILE = __DIR__.'/../../var/api.service.json';

    protected HttpClientInterface $client;
    protected string $uri;

    /**
     * Использование реального клиента тут для демонстрации
     * В последствии, при запросах к сервисам, будет использован MockHttpClient
     * 
     * @param HttpClientInterface $client
     */
    public function __construct(string $host, HttpClientInterface $client)
    {
        $this->client = $client;
        $this->uri = $host.'api/settings';

        if (!file_exists(self::STORAGE_FILE)) {
            file_put_contents(self::STORAGE_FILE, json_encode(self::defaultValues()));
        }
    }

    /**
     * @return ApiConnectionService
     */
    public function configure(): self
    {
        /* Настройка клиента ... */
        return $this;
    }

    /**
     * @return array
     */
    public function fetchSettings(): array
    {
        /* Эмулируем клиента микросервиса */
        $this->client = new MockHttpClient(fn (): MockResponse => new MockResponse(file_get_contents(self::STORAGE_FILE)));
        
        return json_decode($this
            ->request(Request::METHOD_GET)
            ->getContent(), true);
    }

    /**
     * @param array $settings
     * @return array
     */
    public function saveSettings(array $settings): array
    {
        $this->validate($settings, self::constraints());

        /* Эмулируем клиента микросервиса */
        $this->client = new MockHttpClient(function () use ($settings): MockResponse {
            $data = json_decode(file_get_contents(self::STORAGE_FILE), true);
            $data = array_merge($data, $settings);
            $stream = json_encode($data);

            file_put_contents(self::STORAGE_FILE, $stream);

            return new MockResponse($stream);
        });

        return json_decode($this
            ->request(Request::METHOD_POST, null, $settings)
            ->getContent(), true);
    }

    /**
     * @param string $method
     * @param string|null $uri
     * @param array $options
     * 
     * @return ResponseInterface
     */
    public function request(string $method, string $uri = null, array $options = []): ResponseInterface
    {
        return $this
            ->client
            ->request($method, $uri ? $this->uri.$uri : $this->uri, $options);
    }

    /**
     * @return array
     */
    public static function defaultValues(): array
    {
        return [
            self::SETTING_MAINTENANCE  => 1,
            self::SETTING_TITLE        => 'Hogwarts',
            self::SETTING_CONTACTS     => [
                'harry@potter.com',
                'ron@weasley.com',
            ],
        ];
    }

    /**
     * @return Symfony\Component\Validator\Constraints\Collection
     */
    protected static function constraints(): Collection
    {
        return new Collection([
            self::SETTING_MAINTENANCE => new Choice(['1', '2']),
            self::SETTING_TITLE       => new Length(['min' => 2]),
            self::SETTING_CONTACTS    => new All([
                new Email()
            ]),
        ]);
    }
}
