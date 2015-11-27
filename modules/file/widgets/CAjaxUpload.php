<?php
/**
 * CAjaxUpload class file.
 * @version 0.0.1 (2015.09.23)
 *
 * @author Inpassor <inpassor@gmail.com>
 * @link https://github.com/Inpassor
 */

class CAjaxUpload extends EWidget
{

	public $module='file';
	public $action='';
	public $method='post';
	public $model=null;
	public $maxFiles=1;
	public $maxSize=3145728;
	public $types=array();
	public $inputHidden=true;
	public $inputOptions=array();

	public function init()
	{
		if (!isset($this->htmlOptions['id']))
		{
			$this->htmlOptions['id']=$this->id?$this->id:$this->getId();
		}
		else
		{
			$this->id=$this->htmlOptions['id'];
		}
		parent::init();
		if (is_string($this->model))
		{
			$this->model=EActiveRecord::model($this->model);
		}
		if (!isset($this->inputOptions['multiple']))
		{
			$this->inputOptions['multiple']=$this->maxFiles?($this->maxFiles>1?true:false):false;
		}
		if ($this->maxSize)
		{
			$this->clientOptions['maxSize']=$this->maxSize;
		}
		elseif ($this->model&&($maxSize=$this->model->getRuleProperty('file','maxSize')))
		{
			$this->clientOptions['maxSize']=$maxSize;
		}
		if ($this->maxFiles)
		{
			$this->clientOptions['maxFiles']=$this->maxFiles;
		}
		elseif ($this->model&&($maxFiles=$this->model->getRuleProperty('file','maxFiles')))
		{
			$this->clientOptions['maxFiles']=$maxFiles;
		}
		if ($this->types)
		{
			$this->clientOptions['types']=$this->types;
		}
		elseif ($this->model&&($types=$this->model->getRuleProperty('file','types')))
		{
			$this->clientOptions['types']=$types;
		}
		if ($this->inputHidden)
		{
			if (!isset($this->inputOptions['style']))
			{
				$this->inputOptions['style']='';
			}
			$this->inputOptions['style']=trim($this->inputOptions['style'],' ;');
			$this->inputOptions['style'].=($this->inputOptions['style']?';':'').'position:fixed;bottom:-200px;';
			$this->clientOptions['inputHidden']=true;
		}
		if (!$this->action)
		{
			$this->action=Yii::app()->createUrl('file/upload');
		}
		$this->htmlOptions['enctype']='multipart/form-data';
		echo CHtml::beginForm($this->action,$this->method,$this->htmlOptions);
		echo $this->model?CHtml::activeFileField($this->model,'file',$this->inputOptions):CHtml::fileField('file','',$this->inputOptions);
	}

	public function run()
	{
		echo CHtml::endForm();
		$cs=Yii::app()->clientScript;
		$cs->registerCoreScript('jquery');
		$cs->registerScript(__CLASS__.'#'.$this->id,"$('#".$this->id."').CAjaxUpload(".CJavaScript::encode($this->clientOptions).");");
	}
}

?>
