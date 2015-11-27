<?php

class IndexAction extends EAction
{

	/**
	 * Действие для загрузки файла виджетом CAjaxUpload (по умолчанию).
	 */
	public function run()
	{
		$errors=array();
		$files=array();
		$userData=array();
		$user=Yii::app()->user;
		$replacements=array(
			'%ID_USER%'=>$user->isGuest?$user->guestName:sprintf('%014d',$user->id),
		);
		if ($_FILES&&is_array($_FILES))
		{
			foreach ($_FILES as $name=>$props)
			{
				if ($model=@class_exists($name)?new $name():null)
				{
					if ($model->path===false)
					{
						$errors[$name.'_file']=Yii::t('file','Не правильный путь загрузки для типа файла {model}!',array('{model}'=>$name));
						continue;
					}
					elseif ($model->path===null)
					{
						$model->path=Yii::app()->getModule('file')->uploadPath;
					}
					$model->attributes=Yii::app()->request->getParam($name,array());
					$model->file=CUploadedFile::getInstances($model,'file');
					if (!$model->validate())
					{
						$errors[$name.'_file']=$model->getError('file');
					}
					elseif ($model->file&&is_array($model->file))
					{
						foreach ($model->file as $file)
						{
							$pathinfo=pathinfo($file->name);
							$replacements['%NAME%']=$pathinfo['filename'];
							$replacements['%EXTENSION%']=$pathinfo['extension'];
							$model->pathReplacements=array_merge_recursive($replacements,$model->pathReplacements);
							$model->nameReplacements=array_merge_recursive($replacements,$model->nameReplacements);
							$fileInfo=array(
								'model'=>$name,
								'path'=>strtr($model->path,$model->pathReplacements),
								'name'=>$model->name?strtr($model->name,$model->nameReplacements):$pathinfo['filename'],
								'extension'=>$pathinfo['extension'],
							);
							if (($fileInfo['path']=EFileHelper::checkDirectory($fileInfo['path'],true,true))===false)
							{
								$errors[$name.'_file']=Yii::t('file','Не правильный путь загрузки для типа файла {model}!',array('{model}'=>$name));
								continue;
							}
							$fileInfo['url']=str_replace(Yii::getPathOfAlias('webroot'),'',$fileInfo['path']);
							$fileInfo['basename']=$fileInfo['name'].'.'.$fileInfo['extension'];
							$files[]=$fileInfo;
							$file->saveAs($fileInfo['path'].DIRECTORY_SEPARATOR.$fileInfo['name'].'.'.$fileInfo['extension']);
							if (is_array($result=$model->afterUpload($fileInfo)))
							{
								$userData=array_merge_recursive($userData,$result);
							}
						}
					}
				}
				else
				{
					$errors[$name.'_file']=Yii::t('file','Неизвестный тип файла {model}!',array('{model}'=>$name));
				}
			}
		}
		Yii::app()->end(json_encode($errors?array(
			'errors'=>$errors,
		):array_merge_recursive(array(
			'success'=>true,
			'files'=>$files,
		),$userData)));
	}

}

?>
