<?php

namespace Tests\Feature;

use App\Traits\GuidTrait;


use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class CheckGuid extends TestCase
{
    /**
     * GuidTrait test size and random
	 * string size is: 8-4-4-4-12
     * @return void
     */
    public function testGuidTrait()
    {
		$guidone = $this->getGuid();
		$guidtwo = $this->getGuid();
		$this->assertEquals(sizeof($guidone), 36);
		$this->assertEquals(sizeof($guidtwo), 36);
		$this->assertNotEqual($guidone,$guidtwo);
		$this->assertTrue(true);
	}
}
