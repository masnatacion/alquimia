<?
//https://github.com/Peekmo/JsonPath
require("jsonpath/JsonStore.php");


    $input      = json_decode(file_get_contents("data.js"),true); // results.titulo
    $template   = json_decode(file_get_contents("template.js"),true); //records.items


    $store = new JsonStore();


    $paths = [
        [
          "path"  => "category[*].program[*]",
          "child" => [
          				[
				            "key"   => "nameprog",
				            "value" => "title"  
          				],
          				[
				            "key"   => "description",
				            "value" => "description"  
          				]
         			 ]

        ],

        [
          "path"  => "category[*].program[*].videos[*]",
          "child" => [
          				[
				            "key"   => "urls.app_iphone",
				            "value" => "data.iphone"  
          				]
         			 ]
        ]
    ];


function extract_data($record,$string) {

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

    function node($paths,$id,$j,$input,$_input,$template,$output)
    {
      // foreach ($paths as $path) {

		$node = $paths[$id];

		if($id > 0)
		{
			$apath = explode(".",$node["path"]);
			$path  = $apath[count($apath)-1];
		}else
			$path = $node["path"];

				
			$store = new JsonStore();
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

						$return = extract_data($record,$akey);

						$avalue = explode(".",$child["value"]);
						// $value  = '["'.implode('"]["',$avalue).'"]';
						//  if($id > 0)
							$value  = '["'.implode('"]["',$avalue).'"][]';


						if(!empty($return))
						{	
							
							//$isset = @extract_data($output[$i],$avalue);
						
							
								if($j == 0)
									eval("\$output[$i]$value = \"$return\";");	
								else
									eval("\$output[$j]$value = \"$return\";");

						}
					}
					$output = node($paths,$id,$i,$record,$inputs,$template,$output);
				
				}

         return $output;


    }




    $nodes = node($paths,0,0,$input,$input,$template,$output=[]);


	// Recursively traverses a multi-dimensional array.
	function fix_keys($array) {
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

    //echo json_encode($nodes);
	$fixed = fix_keys($nodes);
	print_r($nodes);
    //print_r($fixed);
    //echo json_encode($fixed);
    


