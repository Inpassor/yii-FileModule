<?php

class MImage extends MFile
{

	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}

	public function tableName()
	{
		return '{{image}}';
	}

	public function rules()
	{
		return array(
			array('file','file',
				'types'=>array(
					'jpg',
					'jpeg',
					'gif',
					'png',
				),
				'maxFiles'=>10,
				'maxSize'=>3145728,
			),
		);
	}

}

?>
