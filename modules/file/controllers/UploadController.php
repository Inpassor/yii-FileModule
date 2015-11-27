<?php

class UploadController extends EController
{

	public function actions()
	{
		return array(
			'index'	=>'file.controllers.upload.IndexAction',
		);
	}

}

?>
