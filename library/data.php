<?php
function qSELECT($query, $object = NULL){
	$result = mysql_query($query);
	$return = "";
	if($result){
		$num = mysql_num_rows($result);
		for ($i=0; $i<$num; $i++){
			if(!is_null($object)){
				$row = mysql_fetch_object($result);
			}else{
				$row = mysql_fetch_array($result);
			}
			$return[$i]=$row;
		}
	}
	return $return;
}

function counting($table, $what){
	$query = "SELECT COUNT(1) FROM ".$table;
	$result = mysql_query($query);
	$num = mysql_result($result, 0, 0);
	return $num;
}

function getById($table, $id){
	$query = "SELECT * FROM ".$table." WHERE id=".$id." ";
	$result = qSELECT($query);
	if($result) return $result[0];
	else return $result;
}

function getAll($table){
	$query = "SELECT * FROM ".$table;
	$result = qSELECT($query);
	return $result;
}

function queryToSelect($table, $where, $operator, $zerovalue, $key, $value, $id){
	$ul = '<option value="'.$zerovalue.'">Please select</option>';

	$query = "SELECT * FROM ".$table." WHERE `".$where."` ".$operator." ".$zerovalue." ";
	$result = qSELECT($query);
	foreach ($result as $row){
		$ul .= '<option value="'.$row[$key].'" ';
		$ul .= $id == $row[$key] ? "selected" : "" ;
		$ul .= '>'.$row[$value].'</option>';
	}
	return $ul;
}
?>