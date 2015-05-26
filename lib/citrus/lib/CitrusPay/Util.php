<?php

class CitrusPay_Util
{
	
	public static function isList($array)
	{
		if (!is_array($array))
			return false;
		// TODO: this isn't actually correct in general, but it's correct given CitrusPay's responses
		foreach (array_keys($array) as $k) {
			if (!is_numeric($k))
				return false;
		}
		return true;
	}
	
	public static function convertToCitrusPayObject($array, $apiKey, $class)
  	{
  	  if (self::isList($array)) {
  		$mapped = array();
  		foreach ($array as $i)
  			array_push($mapped, self::convertToCitrusPayObject($i, $apiKey, $class));
  		return $mapped;
  	  }
  	  
  	  if (is_array($array)) {
	      $object = new stdClass();
	      foreach ($array as $name=>$value) {
	      	$name = strtolower(trim($name));
	      	if (!empty($name)) {
	      		$object->$name = self::convertToCitrusPayObject($value, $apiKey, $class);
	      	}
	      }
	      return $object;
      } 
      else 
      {
      	return $array;
      }
  }
}
