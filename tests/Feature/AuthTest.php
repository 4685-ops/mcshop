<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthTest extends TestCase
{
    // 引入数据代码块 这里操作的数据 不会写道数据库
    use DatabaseTransactions;

    /**
     * A basic test example.
     *
     * @return void
     */
    public function test_example()
    {
        $response = $this->get('/');

        $response->assertStatus(200);
    }

    public function testRegister()
    {
        $response = $this->post('/wx/auth/register', [
            'username' => 'lhy111333',
            'password' => '123456',
            'mobile' => '13382322046',
            'code' => '123456'
        ]);
        // 断言
        $response->assertStatus(200);
        $ret = $response->getOriginalContent();
        $this->assertEquals(0, $ret['errno']);
        $this->assertNotEmpty($ret['data']);
    }

    public function testRegisterMobile()
    {
        $response = $this->post('/wx/auth/register', [
            'username' => 'lhy111333',
            'password' => '123456',
            'mobile' => '1338232204',
            'code' => '123456'
        ]);
        // 断言
        $response->assertStatus(200);
        $ret = $response->getOriginalContent();
        $this->assertEquals(707, $ret['errno']);
    }
}
