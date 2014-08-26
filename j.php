<?
//https://github.com/Peekmo/JsonPath
require("jsonpath/JsonStore.php");

    $input      = json_decode(file_get_contents("data.js"),true); // results.titulo
    $template   = json_decode(file_get_contents("template.js"),true); //records.items


    $store = new JsonStore();


    $paths = [
        [
            "key" => "category[*].program[*].nameprog",
            "value" =>"title"  
        ],
        [
            "key" => "category[*].program[0].videos[*].urls.app_iphone",
            "value" =>"data.iphone" 
        ]
    ];






    function node($paths,$input,&$output,$template)
    {
	    foreach ($paths as $path) {


	        // $akey = explode(".",$path["key"]);
	        // $key  = implode("[*].",$akey);
	        $key = $path["key"];

	        $avalue = explode(".",$path["value"]);
	        $value  = '["'.implode('"]["',$avalue).'"]';

	        $store = new JsonStore();
	        $inputs = $store->get($input, "$.".$key);

	        foreach ($inputs as $i => $record) {

	        	

	        	if (!array_key_exists($i, $output))
	            	$output[$i] = $template;


	            	//print_r($input);

	                eval("\$output[$i]$value = '$record';");


	                node([$paths[1]],$output[$i],$output,$template);
	                //print_r($output);
	        }

	        break;

	    }
    }

    node($paths,$input,$output,$template);

    print_r($output);
    


