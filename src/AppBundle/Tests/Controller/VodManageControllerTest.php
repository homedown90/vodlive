<?php

namespace AppBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class VodManageControllerTest extends WebTestCase
{
    public function testShowlist()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/showList');
    }

}
