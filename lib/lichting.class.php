<?php

class Lichting{

	public static function getJongsteLichting(){
		$lichting=(int)date('Y');
		if(date('m')<=8){ $lichting--; }
		return $lichting;
	}
	
	public static function getOudsteLichting(){
		$db=MySql::instance();
		$query="
			SELECT MIN(lidjaar) as oud
			FROM lid
			WHERE lidjaar>0";
		$result=$db->query($query);
		while($row=$db->next($result)){
			return (int)$row['oud'];
		}
	}
}
?>
