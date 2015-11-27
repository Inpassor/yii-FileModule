<?php
/**
 * EController class file.
 * @version 0.0.23 (2015.09.20)
 *
 * @author Inpassor <inpassor@gmail.com>
 * @link https://github.com/Inpassor
 */

class EController extends CController
{
	public $layout=null;
	public $defaultAction=null;
	public $pageTitle='';
	public $pageSubTitle='';
	public $breadcrumbs=array();
	public $menu=array();
	public $bodyCssClass=null;
	public $themePath=null;

	public function init()
	{
		if (!$this->menu&&$this->module&&property_exists($this->module,'menu')&&$this->module->menu)
		{
			$this->menu=$this->module->menu;
		}
		if (!$this->layout)
		{
			$this->layout=isset(Yii::app()->params['defaultLayout'])?Yii::app()->params['defaultLayout']:'main';
		}
		if (!$this->defaultAction)
		{
			$this->defaultAction=isset(Yii::app()->params['defaultAction'])?Yii::app()->params['defaultAction']:'index';
		}
		return parent::init();
	}

	public function missingAction($actionID)
	{
		if (
			file_exists(Yii::app()->theme->basePath.DIRECTORY_SEPARATOR.'views'.DIRECTORY_SEPARATOR.$this->id.DIRECTORY_SEPARATOR.'pages'.DIRECTORY_SEPARATOR.Yii::app()->language.DIRECTORY_SEPARATOR.$actionID.'.php')
			||file_exists($this->viewPath.DIRECTORY_SEPARATOR.'pages'.DIRECTORY_SEPARATOR.Yii::app()->language.DIRECTORY_SEPARATOR.$actionID.'.php')
		)
		{
			$this->render('pages/'.Yii::app()->language.'/'.$actionID);
		}
		elseif (
			file_exists(Yii::app()->theme->basePath.DIRECTORY_SEPARATOR.'views'.DIRECTORY_SEPARATOR.$this->id.DIRECTORY_SEPARATOR.'pages'.DIRECTORY_SEPARATOR.$actionID.'.php')
			||file_exists($this->viewPath.DIRECTORY_SEPARATOR.'pages'.DIRECTORY_SEPARATOR.$actionID.'.php')
		)
		{
			$this->render('pages/'.$actionID);
		}
		else
		{
			throw new CHttpException(404,Yii::t('common','Запрашиваемая страница не найдена.'));
		}
	}

	protected function beforeAction($action)
	{
		if (property_exists($action,'menu')&&$action->menu)
		{
			$this->menu=$action->menu;
		}
		if (!$this->bodyCssClass)
		{
			$this->bodyCssClass=($this->module?$this->module->id.'-':'').$this->id.'-'.$action->id;
		}
		return parent::beforeAction($action);
	}

}

?>
