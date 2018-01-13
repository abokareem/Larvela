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
 * \brief Eloquent Model that contains are the CRUD methods for the "stores" table.
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
		$this->BuildData();
	}


	public function getFillable()
	{
		return $this->fillable;
	}





	/**
	 * Return an arry of store names as an array with store ID as the index.
	 *
	 *
	 * @return	mixed	Collection of rows of stores ordered by store name.
	 */
	public function getStoreNames()
	{
		$rows = \DB::table('stores')->orderBy('store_name')->get();
		$stores = array();
		$stores[0]='All Stores';
		foreach($rows as $r)
		{
			$stores[$r->id] = $r->store_name;
		}
		return $stores;
	}





	/**
	 * Get all ACTIVE stores ordered by Store Name (active is where store_status is set to "A").
	 *
	 * @return	mixed	Collection of rows of stores ordered by store name.
	 */
	public function getStores()
	{
		return \DB::table('stores')->where(['store_status'=>'A'])->orderBy('store_name')->get();
	}


	/**
	 * Get a store using the environment code. This code should be defined in the apache vhost file so the correct store is selected.
	 *
	 * @param	string	$code	Store code to select by.
	 * @return	mixed	Collection of rows of stores ordered by store name.
	 */
	public function getByCode($code)
	{
		return \DB::table('stores')->where(['store_env_code'=>$code])->first();
	}




	public function InsertStore($d)
	{
		return \DB::table('stores')->insertGetId( $d );
	}



	public function UpdateStore($d)
	{
		return \DB::table('stores')->where(['id'=>$d['id']])->update( $d );
	}





	public function getData() { return $this->data; }



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
	public function BuildTree(array $elements, $parentId = 0)
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
	
	

	public function Display($array)
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
	 * Return an array of store names indexed by ID
	 *
	 *
	 * @return array
	 */
	public function getArray()
	{
		$stores = array();
		$rows = $this->getStores();
		$stores[0]="Global";
		foreach($rows as $row)
		{
			$stores[$row->id] = $row->store_name;
		}
		return $stores;
	}





	/**
	 * Generic HTML select list of sorted stores.
	 *
	 * @param	string	$name	HTML Name of select list, default is "store_id"
	 * @param	integer	$id		row id to mark as selected, if not specified AND not global then first item is "Please select..."
	 * @param	boolean	$has_global 	first item is "Global - All Stores" if true
	 * @return	string		 HTML select list as a string
	 */
	public function getSelectList($name, $id=0, $has_global=false)
	{
		$rows = $this->getStores();
		if(strlen($name)==0)
		{
			$name="store_id";
		}
		$html = "<select class='form-control' id='".$name."' name='".$name."'>";
		if($has_global == true)
		{
			$html .= "<option value='0'>Global - All Stores</option>";
		}
		else
		{
			if($id==0)
			{
				$html .= "<option value='0' selected>Please Select....</option>";
			}
		}
		foreach($rows as $row)
		{
			if($row->id == $id)
				$html .= "<option value='".$row->id."' selected>".$row->store_name."</option>";
			else
				$html .= "<option value='".$row->id."'>".$row->store_name."</option>";
		}
		$html .="</select>";
		return $html;
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
