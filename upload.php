<?php

class MYNT_UPLOAD{

	public $dir_picture = "data/picture/";

	function __construct(){

		if($_REQUEST["mode"] === "picture"){
			$this->setUpload_picture();
		}

	}

	public function setUpload_picture(){
		// print_r($_FILES);return;

		// make-dir
		$this->setPictureDir();

		// upload-file
		$this->setUploadFiles();


	}

	public function setPictureDir(){
		$path = $this->dir_picture;
		if(!is_dir($path)){
			mkdir($path,0777,true);
		}
	}

	public function setUploadFiles(){
		if(!isset($_FILES["data"]["name"]) || !count($_FILES["data"]["name"])){return;}

		for($i=0,$c=count($_FILES["data"]["tmp_name"]); $i<$c; $i++){
			$baseFile = $_FILES["data"]["tmp_name"][$i];
			$sentFile = $this->dir_picture .$_FILES["data"]["name"][$i];
			move_uploaded_file($baseFile , $sentFile);
		}
		echo "finished.(".date("YmdHis").")";
		exit();
	}



}

new MYNT_UPLOAD();
