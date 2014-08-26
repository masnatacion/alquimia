<?
//https://github.com/Peekmo/JsonPath
require("jsonpath/JsonStore.php");


    $input      = json_decode(file_get_contents("data.js"),true); // results.titulo
    $template   = json_decode(file_get_contents("template.js"),true); //records.items


    $store = new JsonStore();


    $paths = [
        [
          "path"  => "category[*].program[*]",
            "key"   => "nameprog",
            "value" => "title"  
        ],
        [
          "path"  => "category[*].program[*].videos[*]",
            "key"   => "urls.app_iphone",
            "value" => "data.iphone" 
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

    function node($paths,$id,$j,$input,$template,$output)
    {
      // foreach ($paths as $path) {

		$node = $paths[$id];

		if($id > 0)
		{
			$apath = explode(".",$node["path"]);
			$path  = $apath[count($apath)-1];
		}else
			$path = $node["path"];


		$akey = explode(".",$node["key"]);
		$key  = '["'.implode('"]["',$akey).'"]';



		$avalue = explode(".",$node["value"]);
		$value  = '["'.implode('"]["',$avalue).'"]';

		if($id > 0)
			$value  = '["'.implode('"]["',$avalue).'"][]';

		$store = new JsonStore();
		$inputs = $store->get($input, "$.".$path);

		$tpath = count($paths)-1;



		// if($path === "videos[*]")
		// {
		// 	print_r($input);
		// 	echo $path."\n";
		// 	echo "-----\n";
		// }

		if($id < $tpath)
			$id++;

		foreach ($inputs as $i => $record) {


			if (!@array_key_exists($i, $output) and $j == 0)
			 	$output[$i] = $template;
			    


				$return = extract_data($record,$akey);
				
				
				if(!empty($return))
				{	
					
					if($j == 0)
						eval("\$output[$i]$value = \"$return\";");	
					else
						eval("\$output[$j]$value = \"$return\";");
				}

			    $output = node($paths,$id,$i,$record,$template,$output);

		}




         return $output;

      // }
    }




    $nodes = node($paths,0,0,$input,$template,$output=[]);




    print_r($nodes);

    


