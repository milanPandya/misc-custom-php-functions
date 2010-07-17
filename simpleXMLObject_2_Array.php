<?php
// to see simpleXMLObject array, please browse simpleXMLObject.txt

// this api will call amazon and will make a search for books
require_once('myamazon.php');

/**
* this function will convert simpleXML object to associative array
* @param simpleXMLObject $sObj a simple XML object
* @return array $array an associative array
* @author Jason Sheets <jsheets at shadonet dot com>
*/

function XMLToArray($xml) 
{ 
  if ($xml instanceof SimpleXMLElement) { 
    $children = $xml->children(); 
    $return = null; 
  } 

  foreach ($children as $element => $value) { 
    if ($value instanceof SimpleXMLElement) { 
      $values = (array)$value->children(); 
      
      if (count($values) > 0) { 
        $return[$element] = XMLToArray($value); 
      } else { 
        if (!isset($return[$element])) { 
          $return[$element] = (string)$value; 
        } else { 
          if (!is_array($return[$element])) { 
            $return[$element] = array($return[$element], (string)$value); 
          } else { 
            $return[$element][] = (string)$value; 
          } 
        } 
      } 
    } 
  } 
  
  if (is_array($return)) { 
    return $return; 
  } else { 
    return $false; 
  } 
}

/*
$obj = new amazon();
print_r(XMLToArray($obj->getResults('oil')));	// searching for oil
*/
?>
