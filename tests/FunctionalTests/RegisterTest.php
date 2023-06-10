<?php

namespace App\Tests\FunctionalTests;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class RegisterTest extends WebTestCase
{
    public function testRegisterCorrectData(): void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/register');

        $form = $crawler->selectButton('Register')->form();
        $form->setValues([
            'registration_form[email]' => 'tester@test.org',
            'registration_form[plainPassword]' => 'test1234',
            'registration_form[displayName]' => 'Tester1',
            'registration_form[agreeTerms]' => true,
        ]);
        $client->submit($form);

        $response = $client->getResponse();
        $this->assertEquals(302, $response->getStatusCode());
    }

    public function testRegisterIncorrectEmail(): void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/register');

        $form = $crawler->selectButton('Register')->form();
        $form->setValues([
            'registration_form[email]' => 'tester.test.org',
            'registration_form[plainPassword]' => 'test1234',
            'registration_form[displayName]' => 'Tester2',
            'registration_form[agreeTerms]' => true,
        ]);
        $client->submit($form);

        $response = $client->getResponse();
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertStringContainsString('Please enter a valid email address.', $response->getContent());
    }

    public function testRegisterIncorrectDisplayName(): void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/register');

        $form = $crawler->selectButton('Register')->form();
        $form->setValues([
            'registration_form[email]' => 'tester@test.org',
            'registration_form[plainPassword]' => 'test1234',
            'registration_form[displayName]' => '',
            'registration_form[agreeTerms]' => true,
        ]);
        $client->submit($form);

        $response = $client->getResponse();
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertStringContainsString('Please enter a display name.', $response->getContent());
    }

    public function testRegisterIncorrectPasswordTooShort(): void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/register');

        $form = $crawler->selectButton('Register')->form();
        $form->setValues([
            'registration_form[email]' => 'tester@test.org',
            'registration_form[plainPassword]' => 'test',
            'registration_form[displayName]' => 'Tester3',
            'registration_form[agreeTerms]' => true,
        ]);
        $client->submit($form);

        $response = $client->getResponse();
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertStringContainsString('Your password should be at least 6 characters.', $response->getContent());
    }

    public function testRegisterIncorrectPasswordNoPassword(): void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/register');

        $form = $crawler->selectButton('Register')->form();
        $form->setValues([
            'registration_form[email]' => 'tester@test.org',
            'registration_form[plainPassword]' => '',
            'registration_form[displayName]' => 'Tester4',
            'registration_form[agreeTerms]' => true,
        ]);
        $client->submit($form);

        $response = $client->getResponse();
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertStringContainsString('Please enter a password.', $response->getContent());
    }
}
