<?php

namespace Tests\Feature;

use Tests\TestCase;

class NewsApiTest extends TestCase
{
    public function test()
    {
        $response = $this->get('/api/news');
        $response->assertStatus(200);
        $response->assertSee('<div class="item">');
    }
}
