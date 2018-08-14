<?php
/**
 * \class	SEOHelper
 * \author	Sid Young <sid@off-grid-engineering.com>
 * \date	2016-08-18
 * \version 1.0.0
 *
 *
 * Copyright 2018 Sid Young, Present & Future Holdings Pty Ltd
 *
 * Permission is hereby granted, free of charge, to any person obtaining a
 * copy of this software and associated documentation files (the "Software"),
 * to deal in the Software without restriction, including without limitation
 * the rights to use, copy, modify, merge, publish, distribute, sublicense,
 * and/or sell copies of the Software, and to permit persons to whom the 
 * Software is furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be
 * included in all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, 
 * EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF 
 * MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT.
 * IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY
 * CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT, 
 * TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE 
 * SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
 *
 * \addtogroup Helpers
 * SEOHelper - Provides a static method of retrieving SEO text from the DB.
 */
namespace App\Helpers; 



use App\Models\Seo;
use App\Models\Store;


/**
 * \brief Static class to render text from the DB where needed in eithr Jobs or views.
 */
class SEOHelper
{

	/**
	 * Given a specific token, render the text from the seo table
	 * for this store, fetch the store id using the store code.
	 *
	 * If no data is available, try for a default offering using store ID 0.
	 *
	 * @pre    None - if no text in DB using tag then returns empty string.
	 * @post   None
	 * @return string Text from DB table SEO, else empty string.
	 */
	public static function getText($token)
	{
		$store = app('store');
		$seo_data = Seo::where('seo_store_id',$store->id)->where('seo_token',$token)->get();
		if((sizeof($seo_data)>0)&&($seo_data[0]->seo_status=="A"))
		{
			return $seo_data[0]->seo_html_data;
		}
		else
		{
			$seo_data = Seo::where('seo_store_id',0)->where('seo_token',$token)->get();
			if((sizeof($seo_data)>0)&&($seo_data[0]->seo_status=="A"))
			{
				return $seo_data[0]->seo_html_data;
			}
		}
	}
}
