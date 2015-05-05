<?php

function CKHTMLContent($content){
	
	$html = $content;
	
	$html = str_replace("&", '&amp;', $html);
	$html = str_replace('"', '&auot;', $html);
	$html = str_replace(">", '&gt;', $html);
	$html = str_replace("<", '&lt;', $html);
	$html = str_replace("\r", '', $html);
	$html = str_replace("\n", "\\n", $html);
	$html = str_replace(' ', '&nbsp;', $html);
	return $html;
}

function CKHTMLContent2($content){
	
	$html = $content;
	
	$html = str_replace("&", '&amp;', $html);
	$html = str_replace('"', '&auot;', $html);
	$html = str_replace(">", '&gt;', $html);
	$html = str_replace("<", '&lt;', $html);
	$html = str_replace("\r", '', $html);
	$html = str_replace("\n", "<br />", $html);
	$html = str_replace(' ', '&nbsp;', $html);
	return $html;
}

function CKURL($url,$width=null){

	global $library;
	
	$resUrl = require("$library/org.hailong.configs/resource_url.php");
	
	if($width != null){
		$url = str_replace("res:///images", $resUrl."/thumb/$width/images", $url);
	}
	
	return str_replace("res://", $resUrl, $url);
}

function CKSplit($string){
	if($string){
		$string = str_replace("，", ",", $string);
		$string = str_replace("；", ",", $string);
		$string = str_replace(" ", ",", $string);
		$string = str_replace("。", ",", $string);
		$string = str_replace(".", ",", $string);
		return preg_split("/[,]/s", $string);
	}
	return array();
}

function COObjectValueForKey($object,$key){
	if(isset($object[$key])){
		return $object[$key];
	}
	if(isset($object->$key)){
		return $object->$key;
	}
	return null;
}

function COObjectValueForKeyPath($object,$keyPath){
	
	$keys = preg_split("/[\.]/s", $keyPath);
	
	$index = 0;
	
	$v = $object;
	
	while($index < count($keys)){
		
		$key  = $keys[$index];
		
		$v = COObjectValueForKey($v,$key);
		
		if($v !== null){
			$index ++;
		}
		else {
			break;
		}
	}
	
	return $v;
}

function COObjectStringValueForKeyPath($object, $keyPath,$defaultValue=''){
	$v = COObjectValueForKeyPath($object,$keyPath);
	if($v === null){
		return $defaultValue;
	}
	return ''.$v;
}

function COObjectStringValueForKey($object, $key,$defaultValue=''){
	$v = COObjectValueForKey($object,$key);
	if($v === null){
		return $defaultValue;
	}
	return ''.$v;
}

function COObjectIntValueForKeyPath($object,$keyPath,$defaultValue=0){
	$v = COObjectValueForKeyPath($object,$keyPath);
	if($v === null){
		return $defaultValue;
	}
	return intval($v);
}

function COObjectIntValueForKey($object,$key,$defaultValue=0){
	$v = COObjectValueForKey($object,$key);
	if($v === null){
		return $defaultValue;
	}
	return intval($v);
}

function COObjectBooleanValueForKeyPath($object,$keyPath,$defaultValue=false){
	$v = COObjectValueForKeyPath($object,$key);
	if($v === null){
		return $defaultValue;
	}
	return $v ? true : false;
}

function COObjectBooleanValueForKey($object,$key,$defaultValue=false){
	$v = COObjectValueForKey($object,$key);
	if($v === null){
		return $defaultValue;
	}
	return $v ? true : false;
}

function CODateString($time){
	$now  = time();

	$y0 = date("Y",$now);
	$m0 = date("m",$now);
	$d0 = date("d",$now);
	$H0 = date("H",$now);
	$i0 = date("i",$now);

	$y = date("Y",$time);
	$m = date("m",$time);
	$d = date("d",$time);
	$H = date("H",$time);
	$i = date("i",$time);

	if($y0 == $y){

		if($m0 == $m){
			if($d0 == $d){

				if($H0 == $H){
						
					if($m0 == $m){
						return '1分钟前';
					}
					else{
						return ($m - $m0).'分钟前';
					}
				}

				return '今天 '.$H.':'.$i;
			}
		}

		return $m.'-'.$d.' '.$H.':'.$i;
	}

	return $y.'-'.$m.'-'.$d.' '.$H.':'.$i;
}
