<?php
/**
 * File Module
 * @version 0.0.1 (2015.09.23)
 *
 * @author Inpassor <inpassor@gmail.com>
 * @link https://github.com/Inpassor
 */

class FileModule extends EWebModule
{

	/**
	 * @var string путь для загрузки файлов по умолчанию. Может быть указан в виде алиаса. Если не существует, будет создан.
	 */
	public $uploadPath='webroot.uploads';

	public function init()
	{
		if (!($this->uploadPath=EFileHelper::checkDirectory($this->uploadPath,true,true)))
		{
			$this->uploadPath=EFileHelper::checkDirectory('webroot.uploads',true,true);
		}
		return parent::init();
	}

}

?>
