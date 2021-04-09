<?php

namespace App\Tests\Controller;

use App\Services\ApiConnectionService;
use App\Services\HttpConnectionService;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\{
    Request,
    Response
};

class SettingsControllerTest extends WebTestCase
{
    public const BASE_URI = '/settings-of';

    /**
     * @return array
     */
    public function providerServices(): array
    {
        return [
            'Service http' => ['http', HttpConnectionService::defaultValues(), [
                HttpConnectionService::FIELD_MAINTENANCE => 'You are wrong Patric!',
                HttpConnectionService::FIELD_TITLE => 'S',
            ], [
                '['.HttpConnectionService::FIELD_PATRIC_IQ.']' => 'The value you selected is not a valid choice.',
            ], [
                HttpConnectionService::FIELD_MAINTENANCE => '2',
                HttpConnectionService::FIELD_TITLE => 'Mr. Crubs has been kicked',
                HttpConnectionService::FIELD_PATRIC_IQ => '300',
                HttpConnectionService::FIELD_FRIENDS => [
                    'Money',
                ],
            ]],

            'Service api' => ['api', ApiConnectionService::defaultValues(), [
                ApiConnectionService::SETTING_MAINTENANCE => 'I want to get error',
                ApiConnectionService::SETTING_TITLE => 'E',
                ApiConnectionService::SETTING_CONTACTS => [
                    'Error',
                    'Erroooooor',
                ],
            ], [
                '['.ApiConnectionService::SETTING_MAINTENANCE.']' => 'The value you selected is not a valid choice.',
            ], [
                ApiConnectionService::SETTING_MAINTENANCE => '2',
                ApiConnectionService::SETTING_TITLE => 'The New Hogwarts',
                ApiConnectionService::SETTING_CONTACTS => [
                    'new@student.com',
                ],
            ]],
        ];
    }

    /**
     * @dataProvider providerServices
     * 
     * @param string $name
     * @param array $settings
     * @return void
     */
    public function testShowSettings(string $name, array $settings): void
    {
        $client = static::createClient();
        $client->request(Request::METHOD_GET, self::BASE_URI);
        $this->assertResponseStatusCodeSame(Response::HTTP_NOT_FOUND);

        $client->request(Request::METHOD_GET, self::BASE_URI.'?service_name=app.'.$name.'.service');
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
        $this->assertJson(json_encode($settings));
    }

    /**
     * @dataProvider providerServices
     * 
     * @param string $name
     * @param array $settings
     * @return void
     */
    public function testSaveSettings(
        string $name,
        array $defaultSettings,
        array $newInvalidSettings,
        array $errors,
        array $newValidSettings
    ): void
    {
        $client = static::createClient();
        $client->request(Request::METHOD_POST, self::BASE_URI.'/save');
        $this->assertResponseStatusCodeSame(Response::HTTP_NOT_FOUND);

        $client->request(Request::METHOD_POST, self::BASE_URI.'/save?service_name=app.'.$name.'.service', $newInvalidSettings);
        $this->assertResponseStatusCodeSame(Response::HTTP_UNPROCESSABLE_ENTITY);
        $this->assertJson(json_encode($errors));

        $client->request(Request::METHOD_POST, self::BASE_URI.'/save?service_name=app.'.$name.'.service', $newValidSettings);
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
        $this->assertJsonStringEqualsJsonString(json_encode($newValidSettings), $client->getResponse()->getContent());
    }
}
