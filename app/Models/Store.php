<?php
/**
 * \class	 Store
 * \author	 Sid Young <sid@off-grid-engineering.com>
 * \date	 2016-08-23
 *
 *
 *
 * [CC]
 */
namespace App\Models;


use Illuminate\Database\Eloquent\Model;


/**
 * \brief Eloquent Model for the "stores" table.
 */
class Store extends Model
{

/**
 * The table name for Eloquent calls
 * @var string $table
 */
protected $table = "stores";


public $timestamps = false;

/**
 * The items that are mass assignable
 *
 * @var array $fillable
 */
protected $fillable = array(
		'store_env_code',
		'store_name',
		'store_url',
		'store_currency',
		'store_hours',
		'store_logo_filename',
		'store_logo_alt_text',
		'store_logo_thumb',
		'store_logo_invoice',
		'store_logo_email',
		'store_parent_id',
		'store_status',
		'store_sales_email',
		'store_address',
		'store_address2',
		'store_contact',
		'store_bg_image'
		);

protected $data = array();


	function __construct()
	{
#		$this->BuildData();
	}






	/**
	 * Get a store using the environment code. This code should be defined in the apache vhost file so the correct store is selected.
	 *
	 * @param	string	$code	Store code to select by.
	 * @return	mixed	Collection of rows of stores ordered by store name.
	 */
	private function getByCode($code)
	{
		return \DB::table('stores')->where(['store_env_code'=>$code])->first();
	}






	private  function getData() { return $this->data; }



	/**
	 * Build initial array from the Database
	 * at object creation time.
	 */
	protected function BuildData()
	{
		$rows = \DB::table('stores')->where(['store_status'=>'A'])->orderBy('id')->get();

		foreach($rows as $row)
		{
			$d = array(
				'id'=>$row->id,
				'name'=>$row->store_name,
				'parent'=>$row->store_parent_id
				);
			array_push($this->data, $d);
		}
	}


	/**
	 * Recursive Tree Builder of a list of stores.
	 *
	 * usage: $tree = BuildTree($this->getData());
	 * or
	 *        Display(BuildTree($this->getData()));
	 */
	private function BuildTree(array $elements, $parentId = 0)
	{
		$branch = array();
		foreach ($elements as $element)
		{
			if ($element['parent'] == $parentId)
			{
				$children = $this->BuildTree($elements, $element['id']);
				if($children)
				{
					$element['children'] = $children;
				}
				$branch[] = $element;
			}
		}
		return $branch;
	}
	
	

	private function Display($array)
	{
		echo "<ul class='ul-store'>\n";
		foreach($array as $key => $value)	# key is numeric index  value is array
		{
			$name = $value['name'];
			echo "<li class='li-store'>".$name."</li>\n";
			if(array_key_exists('children', $value))
			{
				echo "<ul>\n";
				foreach($value['children'] as $cv)
				{
					echo "<li class='li-sub-store'>".$name." - ".$cv['name']."</li>";
				}
				echo "</ul>\n";
			}
		}
		echo "</ul>\n";
	}


	
	/**
	 * Given a text string containing code, and a Store object containg row data
	 * return a text string with the codes translated into data and return.
	 *
	 * @param	string	$text
	 * @param	mixed	$store
	 * @return	string
	 */
	public function translate($text,$store)
	{
		$translations = array( 
			"{STORE_ID}"=>$store->id,
			"{STORE_ENV_CODE}"=>$store->store_env_code,
			"{STORE_NAME}"=>$store->store_name,
			"{STORE_URL}"=>$store->store_url,
			"{STORE_CURRENCY}"=>$store->store_currency,
			"{STORE_HOURS}"=>$store->store_hours,
			"{STORE_SALES_EMAIL}"=>$store->store_sales_email,
			"{STORE_CONTACT}"=>$store->store_contact,
			"{STORE_ADDRESS}"=>$store->store_address,
			"{STORE_ADDRESS2}"=>$store->store_address2
			);
		return str_replace(array_keys($translations), array_values($translations), $text);
	}

}
