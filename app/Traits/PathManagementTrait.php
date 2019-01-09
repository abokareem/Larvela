<?php
/**
 *
 *
 * \addtogroup Internal
 * PathManagementTrait - Code for path managment.
 */
namespace App\Traits;





/**
 * \brief Code for parsing and creating paths.
 */
trait PathManagementTrait
{
protected $MEDIADIR= '/media';
protected $GUIDDIR= '/download';




	/**
	 * Create the path needed to store product images and
	 * return the full filesystem path to place file.
	 *
	 * @pre		none
	 * @post	creates directory structure as needed
	 *
	 * @param	integer	$id - the product ID
	 * @return	string 
	 */
	public function getStoragePath( $id )
	{
		$this->LogFunction("getStoragePath(".$id.")");
		$path="";
		$length = strlen($id);
		$id = "".$id."";
		for($i=0 ; $i < $length ; $i++)
		{
			$path.="/".$id[$i];
		}
		$finalpath = public_path()."/media".$path;
		if(is_dir($finalpath))
		{
			$this->LogMsg("PATH [".$finalpath."]");
			return $finalpath;
		}
		else
		{	
			$this->CreatePath($finalpath);
			return $finalpath;
		}
	}



	/**
	 * Create the subpath needed to store product images and
	 * return a partial filesystem path.
	 *
	 * @pre none
	 * @post none
	 *
	 * @param	integer	$id - the product ID
	 * @return	string
	 */
	public function getStorageSubPath($id)
	{
		$this->LogFunction("getStorageSubPath(".$id.")");

		$path="media";
		$length = strlen($id);
		$id = "".$id."";
		for($i=0 ; $i < $length ; $i++)
		{
			$path.="/".$id[$i];
		}
		$this->LogMsg("PATH [".$path."]");
		return $path;
	}



	/**
	 * Create the subpath needed to store virtual product downloaded file
	 * - Return a partial filesystem path.
	 *
	 * @pre none
	 * @post none
	 *
	 * @param	integer	$id - the product ID
	 * @return	string
	 */
	public function getDownloadPath($id)
	{
		$this->LogFunction("getDownloadPath(".$id.")");

		$path= base_path()."/storage/app/download";
		$id = "".$id."";
		$length = strlen($id);
		for($i=0 ; $i < $length ; $i++)
		{
			$path.="/".$id[$i];
		}
		$this->LogMsg("PATH [".$path."]");
		return $path;
	}



	/**
	 * Create the required path
	 *
	 * @param	string	$finalpath
	 * @return	string
	 */
	public function CreatePath($finalpath)
	{
		$this->LogMsg("Create Path [".$finalpath."]");
		try { mkdir($finalpath,0775,true); }
		catch(Exception $e)
		{
			$this->LogError("Failed to create Path [".$finalpath."]");
		}
		$this->LogMsg("PATH [".$finalpath."]");
		return $finalpath;
	}
}
