<?
require("jsonpath/JsonStore.php");
/**
*  Miguel Martinez <natacion@gmail.com>
*  v 1.0
*/

class Node
{
	
	var $URL_INPUT 		= null;
	var $INPUT 		    = null;

	var $URL_TEMPLATE 	= null;
	var $TEMPLATE 		= null;

	var $ORIGIN_PATHS 	= [];
	var $PATHS 			= [];

	var $STORE 			= null;
	
	function __construct($arguments = [])
	{
		 $this->URL_INPUT 	 = $arguments["input"];
		 $this->URL_TEMPLATE = $arguments["template"];
		 $this->ORIGIN_PATHS = $arguments["paths"];

		 $this->PATHS 		 = $this->_setChildPath();
		 
		 $this->_decodeDataURLS();

		 $this->STORE = new JsonStore();
	}

	private function _getINPUT()
	{
		return $this->INPUT;
	}

	private function _getTEMPLATE()
	{
		return $this->TEMPLATE;
	}

	private function _sortBycolumn(&$arr, $col, $dir = SORT_ASC) {
	    $sort_col = array();
	    foreach ($arr as $key=> $row) {
	        $sort_col[$key] = $row[$col];
	    }

	    array_multisort($sort_col, $dir, $arr);
	}


	private function _decodeDataURL($url)
	{
		return json_decode(file_get_contents($url),true);
	}

	private function _decodeDataURLS()
	{
		$this->INPUT 	= $this->_decodeDataURL($this->URL_INPUT);
		$this->TEMPLATE = $this->_decodeDataURL($this->URL_TEMPLATE);
	}


	private function _groupBy($array, $key) {
	    $return = array();
	    foreach($array as $val) 
	        $return[$val[$key]][] = $val;
	    
	    return $return;
	}

	private function _setChild($array) {
	    $return = array();
	    foreach($array as $val) 
	    	$return[] = [ "path"=>$val[0]["path"],"order"=>count($val[0]["path"]), "child" => $val ];
	    
	    return $return;
	}


	private function _removeRoot($path)
	{
		return preg_replace('/^tree./', '', $path);
	}

	private function _generatePath($path)
	{
		$path = $this->_removeRoot($path);
		$afeed = explode("[*].",$path);

		$keyFeed = $afeed[count($afeed)-1];
		$_afeed = $afeed;
		array_pop($_afeed);
		$pathFeed = implode("[*].",$_afeed)."[*]";

		return ["path" => $pathFeed, "key" => $keyFeed];
	}



	private function _parsePATHS()
	{
		$result = [];

		if(!is_array($this->ORIGIN_PATHS))
			$this->ORIGIN_PATHS = json_encode($this->ORIGIN_PATHS,true);

		//$this->_sortBycolumn($this->ORIGIN_PATHS,"feed1");

		//print_r($this->ORIGIN_PATHS);
		foreach ($this->ORIGIN_PATHS as $path) {
			$pathFeed = $this->_generatePath($path["feed1"]);

			$value = $this->_removeRoot($path["feed2"]);

			$result[] = ["path"=>$pathFeed["path"],"key"=>$pathFeed["key"], "value" =>$value];
		}
		return $result;
	}
	
	private function _setChildPath()
	{

		$paths = $this->_parsePATHS();	
		$paths = $this->_groupBy($paths,"path");
		$paths = $this->_setChild($paths);
		$this->_sortBycolumn($paths,"order");

		return $paths;
	}


	private function _getPaths()
	{
		return $this->PATHS;
	}



	private function _extractData($record,$string) {

	    $current_data = $record;

	    foreach ($string as $name) {

	            if (key_exists($name, $current_data)) {
	                    $current_data = $current_data[$name];
	            } else {
	                    return null;
	            }
	    }

	    return $current_data;
	} 


	private function _fixkeys($array) {
	    $numberCheck = false;
	    foreach ($array as $k => $val) {

	    	// echo count($val)."\n";
	    	// print_r($val); 
	    	// echo "key =".$k."\n";
	    	// echo "\n\n\n\n";

	    	if(!is_numeric($k) and is_array($val) and array_key_exists(0, $val))
	    	{
	    		//$numberCheck = array("k" => $k,"v"=>$val);
	    		//print_r($val);
	        	$array[$k] = $val[0];
	        	//return $array;
	    	}

	        if (is_array($val) and count($val) > 1) $array[$k] = fix_keys($val); //recurse

	    }


	    return $array;
	}

    private function _do($paths,$id,$j,$input,$_input,$template,$output)
    {
      // foreach ($paths as $path) {

		$node = $paths[$id];

		if($id > 0)
		{
			$apath = explode(".",$node["path"]);
			$path  = $apath[count($apath)-1];
		}else
			$path = $node["path"];

				
			$store = $this->STORE;
			$inputs = $store->get($input, "$.".$path);

			$tpath = count($paths)-1;


			if($id < $tpath)
				$id++;




			foreach ($inputs as $i => $record) {


				if (!@array_key_exists($i, $output) and $j == 0)
				 	$output[$i] = $template;
				    


					foreach ($node["child"] as $child) {


						$akey = explode(".",$child["key"]);
						$key  = '["'.implode('"]["',$akey).'"]';

						$return = $this->_extractData($record,$akey);

						$avalue = explode(".",$child["value"]);
						// $value  = '["'.implode('"]["',$avalue).'"]';
						//  if($id > 0)
							$value  = '["'.implode('"]["',$avalue).'"][]';


						if(!empty($return))
						{	
							
							
								if($j == 0)
									eval("\$output[$i]$value = \"$return\";");	
								else
									eval("\$output[$j]$value = \"$return\";");

						}
					}
					$output = $this->_do($paths,$id,$i,$record,$inputs,$template,$output);
				
				}

         return $output;


    }

    public function getData()
    {
    	$paths = $this->_getPaths();
    	

    	$input    = $this->_getINPUT();
    	$template = $this->_getTEMPLATE();

    	return $this->_do($paths,0,0,$input,$input,$template,$output=[]);
    }

    public function getDataFixed()
    {
    	return $this->_fixkeys($this->getData());
    }

}




/*
	Example
*/

$paths = [
			["feed1"=>"tree.category[*].program[*].imagelogo","feed2"=>"tree.media:thumbnail"],
			 ["feed1"=>"tree.link","feed2"=>"tree.link"],
			 ["feed1"=>"tree.name","feed2"=>"tree.title"]
		];


$input = "http://middleware.estrategasdigitales.net/nucleo/feed_service_content?url=aHR0cDovL2ZlZWRzLmVzbWFzLmNvbS9kYXRhLWZlZWRzLWVzbWFzL2lwYWQvZGVwb3J0ZXMuanM%3D";
$template = "http://middleware.estrategasdigitales.net/nucleo/feed_service_specific?url=aHR0cDovL2ZlZWRzLmVzbWFzLmNvbS9kYXRhLWZlZWRzLWVzbWFzL2lwYWQvMDEyOTI0MDEwMS54bWw=";

// $paths = [
// 	["feed1"=>"tree.category[*].program[*].videos[*].urls.app_iphone","feed2"=>"tree.data.iphone"],
// 	["feed1"=>"tree.category[*].program[*].nameprog","feed2"=>"tree.title"],
	
// 	["feed1"=>"tree.category[*].program[*].description","feed2"=>"tree.description"],
// 	["feed1"=>"tree.category[*].program[*].videos[*].urls.app_ipad","feed2"=>"tree.data.ipad"],
// ];
$node = new Node(["input" => $input, "template" => $template, "paths" =>$paths]);

print_r($node->getData());


