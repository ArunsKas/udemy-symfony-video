<?php

namespace App\Tests\Controllers\Admin;

use App\Tests\RoleUser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class AdminControllerTranslationTest extends WebTestCase
{

    use RoleUser;

    public function testTranslations(): void
    {
        $this->client->request('GET', '/lt/admin/');

        $this->assertStringContainsString(
            'Mano profilis',
            $this->client->getResponse()->getContent()
        );
        $this->assertStringContainsString(
            'video-sarasas',
            $this->client->getResponse()->getContent()
        );
    }
}
