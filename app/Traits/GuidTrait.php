<?php
/**
 * Larvela
 * Copyright (C) 2017
 * by Present & Future Holdings Pty Ltd Trading as Off Grid Engineering
 * https://off-grid-engineering.com
 *
 * @date	2017-07-10
 * @author	Sid Young	<sid@off-grid-engineering.com>
 */
namespace App\Traits;



/**
 * Trait to generate a GUID
 */
trait GuidTrait
{
	/**
	 * Generate a guid string, uses random number generator and should repeat.
	 *
	 * @return	string
	 */
	 public function getGuid()
	 {
		return sprintf('%04X%04X-%04X-%04X-%04X-%04X%04X%04X', mt_rand(0, 65535), mt_rand(0, 65535), mt_rand(0, 65535), mt_rand(16384, 20479), mt_rand(32768, 49151), mt_rand(0, 65535), mt_rand(0, 65535), mt_rand(0, 65535));
	}
}
