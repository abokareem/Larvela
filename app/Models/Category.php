<?php
/**
 * \class	Category
 * \author	Sid Young <sid@off-grid-engineering.com>
 * \date	2016-08-15
 *
 *
 *
 * [CC]
 */
namespace App\Models;


use Illuminate\Database\Eloquent\Model;

/**
 * \brief MVC Model to provide support for the "category" table.
 *
 * {FIX_2016-09-06} removed some query builder code and replaced with Eloquent calls.
 * {FIX_2016-09-22} Changed table name to category
 */
class Category extends Model
{
/**
 * Tell framework the table is singular.
 * @var	string	$table
 */
public $table = "category";


/**
 * Indicates if the model should be timestamped.
 * @var bool $timestamps
 */
public $timestamps = false;


/**
 * Items that are mass assignable
 * @var array $fillable
 */
protected $fillable = array('category_title','category_description','category_url','category_parent_id','category_status','category_visible','category_store_id');


/**
 * Internal data array for buidling a tree of category
 * @var array $data
 */
protected $data = array();


	/**
	 * Constructor to build the tree on creation
	 *
	 * @pre Categories should be defined
	 * @post internal data array created
	 * @return void
	 */
	function __construct()
	{
		$this->BuildData();
	}





	/**
	 * Get ALL category ordered by status and parent ID
	 *
	 * @pre none
	 * @post none
	 * @return	mixed	Collection of rows from the category table
	 */
	protected function getAllCategories()
	{
		return \DB::table('category')->orderBy('category_store_id')->orderBy('category_parent_id')->get();
	}







	/**
	 * Get Active category ordered by Date
	 *
	 * @pre none
	 * @post none
	 * @return Collection of rows from the category table
	 */
	protected function getCategory()
	{
		return \DB::table('category')->where(['category_status'=>'A'])->orderBy('category_title')->get();
	}
	public function getCategories()
	{
		return $this->getCategory();
	}



	/**
	 * Get all rows and return names as an array index on id
	 *
	 * @return	array
	 */
	protected function getArray()
	{
		$all_categories = \DB::table('category')->orderBy('category_title')->get();
		$category_list = array();
		$category_list[0]="Assigned as Parent";
		foreach($all_categories as $ac)
		{
			$category_list[$ac->id] = $ac->category_title;
		}
		return $category_list;
	}



	/**
	 * Given the store ID, return a collection of rows from the category table
	 * (single row in collection zero indexed).
	 *
	 * @param	integer	$store_id	row ID
	 * @return	mixed	
	 */
	protected function getByStoreID($store_id)
	{
		return \DB::table('category')->where(['category_store_id'=>$store_id])->get();
	}



	/**
	 * Build an array of ordered parent-child relationships
	 *
	 * @pre 	Data must be valid
	 * @post	array is filled with items
	 * @return	void
	 */
	public function BuildData()
	{
		$rows = \DB::table('category')
			->where(['category_status'=>'A'])
			->where(['category_visible'=>'Y'])
			->orderBy('id')->get();
		$this->AssembleData($rows);
	}




	public function BuildStoreData($store_id)
	{
		$rows = \DB::table('category')
			->where(['category_store_id'=>$store_id])
			->where(['category_status'=>'A'])
			->where(['category_visible'=>'Y'])
			->orderBy('id')->get();
		$this->AssembleData($rows);
	}



	protected function AssembleData($rows)
	{
		$this->data = array();
		foreach($rows as $row)
		{
			$d = array(
				'id'=>$row->id,
				'title'=>$row->category_title,
				'parent'=>$row->category_parent_id
				);
			array_push($this->data, $d);
		}
		$this->data = $this->buildTree($this->data);
	}




	/**
	 * Return an array of ordered parent-child relationships
	 *
	 * @pre		BuildData() already called
	 * @post	none
	 * @return	array
	 */
	public function getData() { return $this->data; }



	/**
	 * Recusive function to build the tree of relationships
	 * @usage $tree = buildTree($rows);
	 *
	 * @pre		category data must be in table
	 * @post	array is filled with items
	 *
	 * @param	array	$elements array of items already built.
	 * @param	integer	$parentId integer row ID of parent category.
	 * @return array
	 */
	public function buildTree(array $elements, $parentId = 0)
	{
		$branch = array();
		foreach ($elements as $element)
		{
			if ($element['parent'] == $parentId)
			{
				$children = $this->buildTree($elements, $element['id']);
				if($children)
				{
					$element['children'] = $children;
				}
				$branch[] = $element;
			}
		}
		return $branch;
	}
	
	

	/**
	 * Display array as a HTML un-ordered list of ordered parent-child relationships
	 *
	 * @pre Array data is prefilled
	 * @post none
	 * @param	array	$array	Array of category
	 * @return void
	 */
	public function Display($array)
	{
		echo "<ul id='menu-bar'>\n";
		foreach($array as $key => $value)	# key is numeric index  value is array
		{
			$title = $value['title'];
			echo "<li class='li-category'>".$title."</li>\n";
			if(array_key_exists('children', $value))
			{
				echo "<ul>\n";
				foreach($value['children'] as $cv)
				{
					echo "<li class='li-sub-category'>".$title." - ".$cv['title']."</li>";
				}
				echo "</ul>\n";
			}
		}
		echo "</ul>\n";
	}





	/**
	 * IN PRODUCTION USE IN STOREFRONT CONTROLLER
	 *
	 * Return the HTML UL List as clickable items
	 *
	 * @pre		Categories table has valid data
	 * @post	None
	 * @return	string HTML un-ordered list
	 */
	public function getHTML()
	{
		$array =  $this->data;
		$html = "<ul id='menu-bar'>\n";
		$active=0;
		foreach($array as $key => $value)	# key is numeric index  value is array
		{
			$title = $value['title'];
			$id = $value['id'];
			if(array_key_exists('children', $value))
			{
				$html .= "<li class='menu-item'><a href='/category/$id'>".$title."</a>";
				$html .= "<ul class='dropdown-menu'>";
				foreach($value['children'] as $cv)
				{
					$id = $cv['id'];
					$html .= "<li class='menu-item'><a href='/category/$id'>".$cv['title']."</a></li>\n";
				}
				$html .= "</ul>\n";
			}
			else
			{
				$html .= "<li class='menu-item'><a href='/category/$id'>".$title."</a>";
			}
			$html .= "</li>\n";
		}
		$html .= "</ul>\n";
		return $html;
	}



	/**
	 * Return the HTML SELECT List as clickable items
	 *
	 * @param 	string	$form_name "name=" attribute to use in seelct list
	 * @param	integer	$id "id=" attribute label
	 * @pre 	Categories table has valid data
	 * @post	None
	 * @return	string HTML select list
	 */
	public function getSelectList($form_name, $id=0)
	{
		$rows = $this->getCategories();
		$html = "<select class='form-control' id='".$form_name."' name='".$form_name."'>";
		$html .= "<option value='0'>No Parent</option>";
		foreach($rows as $row)
		{
			if($row->id == $id)
				$html .= "<option value='".$row->id."' selected>".$row->category_title."</option>";
			else
				$html .= "<option value='".$row->id."'>".$row->category_title."</option>";
		}
		$html .="</select>";
		return $html;
	}
}
