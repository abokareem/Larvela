<?php

namespace Tests\Feature;

use App\User;


use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class CheckAdminUser extends TestCase
{
    /**
     * A basic test example.
     *
     * @return void
     */
    public function testExample()
    {
		$user = User::find(1);
		if(is_null($user))
		{
        	$this->assertTrue(false);
		}
		else
		{
			$this->assertTrue(true);
		}
	}
}
