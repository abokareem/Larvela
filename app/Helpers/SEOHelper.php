<?php
/**
 * \class	SEOHelper
 * @author	Sid Young <sid@off-grid-engineering.com>
 * @date	2016-08-18
 *
 *
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
