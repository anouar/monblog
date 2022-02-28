<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

class PageControllerTest extends WebTestCase
{
    public function testHomePage()
    {
        $client = static::createClient();
        $client->request('GET', '/');
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
        $this->assertSelectorTextContains('h1', 'Bienvenue - Welcome - Welkom - Bonvenon - Benvenuto');
    }

    public function testH1BlogPage()
    {
        $client = static::createClient();
        $client->request('GET', '/blog');
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
    }

}
