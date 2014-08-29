<?php

$data		= json_decode(file_get_contents("data.js"),true); // results.titulo
$template 	= json_decode(file_get_contents("template.js"),true); //records.items




$haystack = array(
    'root_trrreeee1' => array(
        'path1' => array(
            'description' => 'etc',
            'child_of_path_1' => array(
                array('name2' => '2'),
                array('name2' => '2')
            )
        ),
        'path1' => array(
            'description' => 'etc',
            'child_of_path_1' => array(
                array('name2' => '1'),
                array('name2' => '1')
            )
        ),
    ),
    'name' => '1',
    1 => array('name' => '1'),
    'another_leaf' => '1'
);


$needle = array(
    "name2"
);



function multidimensional_preserv_key_search($haystack, $needle, $path = array(), &$true_path = array())
{
    if (empty($needle) || empty($haystack)) {
        return false;
    }

    foreach ($haystack as $key => $value) {

        foreach ($needle as $skey => $svalue) {



            if (is_array($value)) {
                $path = multidimensional_preserv_key_search($value, $needle, array($key => $path), $true_path);
            }

            echo $key."\n";
            if($key == $svalue){

            //if (($value === $svalue) && ($key === $skey)) {
            	print_r($value);
                //$true_path = $path;

                //return $true_path;
            }
        }

    }

    //if (is_array($true_path)) { return array_reverse(($true_path)); }
    return $path;
}


function flatten_keys($array)
{
    $result = array();

    foreach($array as $key => $value) {
        if(is_array($value)) {
            $result[] = $key;
            $result = array_merge($result, self::flatten_keys($value));
        } else {
            $result[] = $key;
        }
    }

    return $result;
}



$r = multidimensional_preserv_key_search($haystack, $needle);

// print_r($r);

die;


// function array_get($arr, $path)
// {
//     if (!$path)
//         return null;

//     $segments = is_array($path) ? $path : explode('/', $path);
//     $cur =& $arr;
//     foreach ($segments as $segment) {
//         if (!isset($cur[$segment]))
//             return null;

//         $cur = $cur[$segment];
//     }

//     return $cur;
// }

function array_set(&$arr, $path, $value)
{
    if (!$path)
        $segments = [];
    else
    	$segments = is_array($path) ? $path : explode('/', $path);

    $cur =& $arr;
    foreach ($segments as $segment) {
        if (!isset($cur[$segment]))
            $cur[$segment] = array();
        $cur =& $cur[$segment];
    }
    


    // foreach ($value as $kvalue => $vvalue) {

    // 	foreach ($vvalue as $k => $v) {
    // 			foreach ($arr as $karr => $varr) {
    // 				if($karr == $k)
    // 					$records[$karr] = $v;
    // 				else
    // 					$records[$karr] = $varr;
    // 			}
    // 			$record[] = $records;
    // 	}	

    // }
    //print_r($record);

    $cur = $value;


}




function    isAssociativeArray( &$arr ) {
    return  (bool)( preg_match( '/\D/', implode( array_keys( $arr ) ) ) );
}


function array_get($data, $path, &$node = [], $newKey = "",&$result = []){
 
    $found = true;
 
    $path = explode("/", $path);


    for ($x=0; ($x < count($path) and $found); $x++){
 
    	$key = $path[$x];

 		if(isAssociativeArray($data))
 		{
 			
	        if (isset($data[$key])){
	            $data = $data[$key];

	            if($x == count($path) -1)
	            	$node = $data;
	        }        
	        // else { $found = false; }

 		}else
 		{

 			
 			
 			foreach ($data as $record) {
		        

		        if (isset($record[$key])){
		            //$data= $record[$key];


		            //echo count($path);

		            if($x == count($path) -2)
		            {
		            	$is_assoc = $record[$key];

		            	if(isAssociativeArray($is_assoc))
		            		$node[] =  [ $newKey =>$is_assoc[$path[count($path)-1]] ];
		            	else
		            		$data= $record[$key];
		            }else
		            	$data= $record[$key];

		            if($x == count($path) -1)
		            {
		            	//$node[] = $record[$key];
		            	if(empty($newKey))
		            		$node[] = $record[$key];
		            	else
		            		$node[] = [$newKey => $record[$key]];
		            }


		        }        
		        //else { $found = false; }

 			}
 			
 		}

    }
 	
    $result = $data;
 
    return $found;
}


/*
feed.category.program.nameprog output.title
feed.category.program.description output.data
feed.category.program.imagestage	output.image
feed.category.program.videos[0].urls.app_ipad	output.video
*/

$paths = [
	// [
	// 	"idData" => "category/program/nameprog",
	// 	"idTemplate" =>"data/title"	
	// ],
	[
		"idData" => "category/program/videos/urls/app_ipad",
		"idTemplate" =>"data/iphone"	
	]
];


// function doSomething(&$complex_array,$index)
// {
//     foreach ($complex_array as $n => $v)
//     {
//         if (is_array($v))
//             doSomething($v);
//         else
//             do whatever you want to do with a single node
//     }
// }







	function node($tree,$path)
	{
		$node 	 = $tree;
		$records = [];

		for ($x=0; ($x < count($path)); $x++){

			$key  = $path[$x];
			

			if(isAssociativeArray($tree))
			{
				$records[$key] = [];
				$tree = $tree[$key];
			}
			else
			{
				foreach ($tree as $record) {
					$records[$key][] = [];
				}
				$tree = $tree[0][$key];
			}


		}

		return $records;
	}



foreach ($paths as $path) {


	$idData 	= $path["idData"];
	$idTemplate = $path["idTemplate"];

	$path = $pathData 		= explode("/",$idData);
	$pathTemplate 	= explode("/",$idTemplate);


	//seekValue($data,);

	//print_r($data["category"][0]["program"]);
	 $records = node($data,$path);
	  print_r($records);

	// if(array_get($data, $idData ,$node,end($pathTemplate)))
	// {
		
	// 	print_r($node);
		

	// 	// array_set($template, implode("/",$pathTemplate), $node);
	// 	unset($node);
	 	
	 
	// }	


}

print_r($template);







