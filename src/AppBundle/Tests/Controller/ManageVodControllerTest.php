<?php

namespace AppBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class ManageVodControllerTest extends WebTestCase
{
    public function testShowlist()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/showList');
    }

}
