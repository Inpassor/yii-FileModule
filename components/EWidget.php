<?php
/**
 * EWidget class file.
 * @version 0.0.1 (2015.06.20)
 *
 * @author Inpassor <inpassor@gmail.com>
 * @link https://github.com/Inpassor
 */

class EWidget extends CWidget
{

	public $id=null;
	public $htmlOptions=array();
	public $clientOptions=array();
	public $options=array();
	public $css=array();
	public $js=array();
	public $view='';
	public $title='';
	public $module=null;

	public function filePath($file)
	{
		$className=get_class($this);
		$viewPath=parent::getViewPath();
		if (is_dir($viewPath.DIRECTORY_SEPARATOR.$className))
		{
			$viewPath.=DIRECTORY_SEPARATOR.$className;
		}
		if ($this->module)
		{
			$moduleId=$this->module->id;
			$themeViewPath=Yii::app()->theme->basePath.DIRECTORY_SEPARATOR.'views'.DIRECTORY_SEPARATOR.$moduleId.DIRECTORY_SEPARATOR.'widgets'.DIRECTORY_SEPARATOR.$className;
			if (file_exists($themeViewPath.DIRECTORY_SEPARATOR.$file))
			{
				return $themeViewPath.DIRECTORY_SEPARATOR.$file;
			}
			$themeViewPath=Yii::app()->theme->basePath.DIRECTORY_SEPARATOR.'views'.DIRECTORY_SEPARATOR.$moduleId.DIRECTORY_SEPARATOR.'widgets';
			if (file_exists($themeViewPath.DIRECTORY_SEPARATOR.$file))
			{
				return $themeViewPath.DIRECTORY_SEPARATOR.$file;
			}
		}
		$themeViewPath=Yii::app()->theme->basePath.DIRECTORY_SEPARATOR.'views'.DIRECTORY_SEPARATOR.'widgets'.DIRECTORY_SEPARATOR.$className;
		if (file_exists($themeViewPath.DIRECTORY_SEPARATOR.$file))
		{
			return $themeViewPath.DIRECTORY_SEPARATOR.$file;
		}
		$themeViewPath=Yii::app()->theme->basePath.DIRECTORY_SEPARATOR.'views'.DIRECTORY_SEPARATOR.'widgets';
		if (file_exists($themeViewPath.DIRECTORY_SEPARATOR.$file))
		{
			return $themeViewPath.DIRECTORY_SEPARATOR.$file;
		}
		if (file_exists($viewPath.DIRECTORY_SEPARATOR.$file))
		{
			return $viewPath.DIRECTORY_SEPARATOR.$file;
		}
		if (file_exists(Yii::app()->theme->basePath.DIRECTORY_SEPARATOR.$file))
		{
			return Yii::app()->theme->basePath.DIRECTORY_SEPARATOR.$file;
		}
		if (file_exists(Yii::app()->basePath.DIRECTORY_SEPARATOR.$file))
		{
			return Yii::app()->basePath.DIRECTORY_SEPARATOR.$file;
		}
		if (file_exists($file))
		{
			return $file;
		}
		return '';
	}

	private function _registerFileIfExists($_file,$type='css')
	{
		if ($file=$this->filePath($_file.'.'.$type))
		{
			$asset=CHtml::asset($file);
			switch ($type)
			{
				case 'js': Yii::app()->clientScript->registerScriptFile($asset,CClientScript::POS_HEAD);
					break;
				case 'css': Yii::app()->clientScript->registerCssFile($asset);
					break;
			}
		}
	}

	public function registerFiles()
	{
		$className=get_class($this);
		if (!$this->css)
		{
			$this->css=array();
		}
		elseif (!is_array($this->css))
		{
			$this->css=array($this->css);
		}
		if (!in_array($className,$this->css))
		{
			$this->css[]=$className;
		}
		if (!in_array($this->view,$this->css))
		{
			$this->css[]=$this->view;
		}
		foreach ($this->css as $css)
		{
			$this->_registerFileIfExists($css);
		}
		if (!$this->js)
		{
			$this->js=array();
		}
		elseif (!is_array($this->js))
		{
			$this->js=array($this->js);
		}
		if (!in_array($className,$this->js))
		{
			$this->js[]=$className;
		}
		if (!in_array($this->view,$this->js))
		{
			$this->js[]=$this->view;
		}
		foreach ($this->js as $js)
		{
			$this->_registerFileIfExists($js,'js');
		}
	}

	public function init()
	{
		if (is_string($this->module))
		{
			$this->module=in_array($this->module,array_keys(Yii::app()->modules))?Yii::app()->getModule($this->module):null;
		}
		if (!$this->id)
		{
			$this->id=parent::getId();
		}
		if (!$this->view)
		{
			$this->view=get_class($this);
		}
		$this->registerFiles();
		if ($view=$this->filePath($this->view.'.php'))
		{
			$this->renderFile($view,$this->options);
		}
	}

}

?>
