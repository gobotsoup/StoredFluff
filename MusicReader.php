<?php
require_once("MP3/Id.php");
$results = read_mp3_tags("./samples");

$json='{"songs":[
                ';

// need to somehow remove whitespace at the end of each value of the result.
foreach($results as $result)
{
   $json .= '{';
   foreach($result as $key => $value) {
	$valTrim = trim($value);
        $json .= "\"" . $key . "\"" . " : " . "\"" . $valTrim . "\",";      
    }
    $json .= '},';
}

$json .= ']
          }';

echo $json;
function read_mp3_tags($dir)
{
    static $result = array();
    static $i = 0;
 
    $tag_string = "";
 
    $mp3 = new MP3_Id();
 
    // Tags supported by the MP3_Id class
    $tags = array(
                  "name", "artists", "album",
                  "year", "comment", "track",
                  "genre", "genreno"
                  );
 
 
    // Read the current directory
    $d = dir($dir);
 
    // Loop through all the files in the current directory:
    while (false !== ($file = $d->read()))
    {
        // Skip '.' and '..'
        if (($file == '.') || ($file == '..'))
        {
            continue;
        }
 
        // If this is a directory, then recursively call it
        if (is_dir("{$dir}/{$file}"))
        {
            read_mp3_tags("{$dir}/{$file}");
        }
        else
        {
            // It's a mp3 file so read the tags
            if(strtolower(substr($file, strlen($file) - 3, 3)) == "mp3") 
            {
                $data = $mp3->read("{$dir}/{$file}");
 
                // OOPs, some error occured, just save the filename
                if (PEAR::isError($data))
                { 
                    $result[$i]['filename'] = $file;
                    $result[$i]['directory'] = $dir;		   
                }
                else
                {
                    $result[$i]['filename'] = $file;
                    $result[$i]['directory'] = $dir;
 
                    // Read all the tags of the particular file
                    foreach($tags as $tag)
                    {
						//$thaTag = $mp3->getTag($tag);			
						//$result[$i][$tag] = str_replace('', '_', $thaTag);
                        $result[$i][$tag] = $mp3->getTag($tag);			
                    }
                }
                $i++;
            }
        }
    }
 
    return $result;
}
?>
