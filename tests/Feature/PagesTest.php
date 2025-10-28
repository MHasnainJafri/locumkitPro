<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class PagesTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function test_example()
    {
        $this->get('/')->assertStatus(200);
        $this->get('/register')->assertStatus(200);
        $this->get('/login')->assertStatus(200);
        $this->get('/about')->assertStatus(200);
        $this->get('/contact')->assertStatus(200);
        $this->get('/term-condition')->assertStatus(200);
        $this->get('/privacy-policy')->assertStatus(200);
        $this->get('/blogs')->assertStatus(200);
        $this->get('/blogs/recent-posts')->assertStatus(200);
        $this->get('/package')->assertStatus(200);
        $this->get('/locums')->assertStatus(200);
        $this->get('/employer')->assertStatus(200);
        $this->get('/accountancy')->assertStatus(200);
        $this->get('/dbs')->assertStatus(200);
        $this->get('/sitemap')->assertStatus(200);
        $this->get('/maps')->assertStatus(200);
        $this->get('/benefits')->assertStatus(200);
    }
}
