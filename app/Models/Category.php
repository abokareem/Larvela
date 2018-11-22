<?php
/**
 * \class	Category
 * \author	Sid Young <sid@off-grid-engineering.com>
 * \date	2016-08-15
 * \version	1.0.0
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
protected $fillable = array('id','category_title','category_description','category_url','category_parent_id','category_status','category_visible','category_store_id');


/**
 * Internal data array for buidling a tree of category
 * @var array $data
 */
protected $data = array();




	/**
	 * Get Active category ordered by Date
	 *
	 * @pre none
	 * @post none
	 * @return Collection of rows from the category table
	 */
	private function getCategory()
	{
		return \DB::table('category')->where(['category_status'=>'A'])->orderBy('category_title')->get();
	}



	private function getCategories()
	{
		return $this->getCategory();
	}



	/**
	 * Get all rows and return names as an array index on id
	 *
	 * @return	array
	 */
	private  function getArray()
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
	 * Build an array of ordered parent-child relationships
	 *
	 * @pre 	Data must be valid
	 * @post	array is filled with items
	 * @return	void
	 */
	private  function BuildData()
	{
		$rows = \DB::table('category')
			->where(['category_status'=>'A'])
			->where(['category_visible'=>'Y'])
			->orderBy('id')->get();
		$this->AssembleData($rows);
	}




	private  function BuildStoreData($store_id)
	{
		$rows = \DB::table('category')
			->where(['category_store_id'=>$store_id])
			->where(['category_status'=>'A'])
			->where(['category_visible'=>'Y'])
			->orderBy('id')->get();
		$this->AssembleData($rows);
	}



	private function AssembleData($rows)
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
#	public function getData() { return $this->data; }



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
	private function buildTree(array $elements, $parentId = 0)
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
	protected function Display($array)
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
	private function getHTML()
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
	 * Returns all relevant images as a Collection of Image objects
	 *
	 * @return  mixed
	 */
	public function images()
	{
		return $this->belongsToMany('App\Models\Image','category_image','category_id','image_id');
	}
}
