<?php
/**
 * EWebModule class file.
 * @version 0.0.1 (2015.06.20)
 *
 * @author Inpassor <inpassor@gmail.com>
 * @link https://github.com/Inpassor
 */

class EWebModule extends CWebModule
{

	public $defaultController=null;
	public $menu=array();

	public function init()
	{
		if (!$this->defaultController)
		{
			$this->defaultController=isset(Yii::app()->params['defaultController'])?Yii::app()->params['defaultController']:'index';
		}
		$this->layoutPath=Yii::app()->theme->basePath.'/views/layouts';
	}

}

?>
