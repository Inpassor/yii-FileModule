<?php
/**
 * EFileHelper class file.
 * @version 0.0.2 (2015.09.24)
 *
 * @author Inpassor <inpassor@gmail.com>
 * @link https://github.com/Inpassor
 */

class EFileHelper extends CFileHelper
{

	/**
	 * Метод для создания каталога.
	 * @param string $dir путь к каталогу.
	 * @param integer $mode права, которые бдут присовены каталогу. По умолчанию 0777.
	 * @param boolean $recursive если true, каталог будет создан рекурсивно. По умолчанию false.
	 * @param boolean $makeIndex если true - создаст внутри каталога файл index.php, переадресовывающий в корень сайта (/). По умолчанию false.
	 * @return boolean true в случае успешной операции. В противном случае - false.
	 */
	public static function createDirectory($dir,$mode=null,$recursive=false,$makeIndex=false)
	{
		if ($mode===null)
		{
			$mode=0777;
		}
		$prevDir=dirname($dir);
		if ($recursive&&!is_dir($dir)&&!is_dir($prevDir))
		{
			self::createDirectory(dirname($dir),$mode,true,$makeIndex);
		}
		$result=@mkdir($dir,$mode);
		@chmod($dir,$mode);
		if ($result&&$makeIndex)
		{
			file_put_contents($dir.DIRECTORY_SEPARATOR.'index.php',"<?php header('Location:/'); ?>");
		}
		return $result;
	}

	/**
	 * Метод для проверки существования каталога. Ищет каталог (в т.ч. по алиасу), может попытаться его рекурсивно создать.
	 * @param string $dir путь к каталогу.
	 * @param boolean $create если true - попытается рекурсивно создать каталог, в случае, если он не найден. По умолчанию false.
	 * @param boolean $makeIndex если true - создаст внутри каталога файл index.php, переадресовывающий в корень сайта (/). По умолчанию false.
	 * @return mixed путь к существующему каталогу. В противном случае - false.
	 */
	public static function checkDirectory($dir,$create=false,$makeIndex=false)
	{
		if (($alias=Yii::getPathOfAlias($dir)))
		{
			$dir=$alias;
		}
		if (!file_exists($dir)&&$create)
		{
			self::createDirectory($dir,0777,true,$makeIndex);
		}
		if (is_dir($dir))
		{
			return $dir;
		}
		return false;
	}

	/**
	 * Метод для удаления каталога.
	 * @param string $path путь к каталогу.
	 * @param boolean $removeSelf если false - оставляет каталог, удаляя только его содержимое. По умолчанию - false.
	 * @param array $except список файлов и каталогов, которые удалять не нужно.
	 * @return boolean true.
	 */
	public static function removeDirectory($path,$removeSelf=true,$except=array())
	{
		if ($path=self::checkDirectory($path))
		{
			$dirHandle=opendir($path);
			while (false!==($file=readdir($dirHandle)))
			{
				if ($file!='.'&&$file!='..')
				{
					$tmpPath=$path.DIRECTORY_SEPARATOR.$file;
					if (!in_array($tmpPath,$except))
					{
						@chmod($tmpPath,0777);
						if (is_dir($tmpPath))
			  			{
							self::removeDirectory($tmpPath,true,$except);
					   	}
			  			elseif (file_exists($tmpPath))
						{
		  					unlink($tmpPath);
			  			}
					}
				}
			}
			closedir($dirHandle);
			if ($removeSelf&&is_dir($path))
			{
				rmdir($path);
			}
			return true;
		}
		return false;
	}

	/**
	 * Метод для создания уменьшенной копии изображения.
	 * @param array $options параметры:
	 * string 'file' - исходный файл изображения.
	 * integer 'maxWidth' - максимальная ширина создаваемой копии.
	 * integer 'maxHeight' - максимальная высота создаваемой копии.
	 * string 'imageFormat' - формат создаваемого изображения (без точки в начале!).
	 * array 'replace' - замены для имени файла в виде пар "что заменять" => "на что заменять".
	 * string 'addPostfix' - если не пустая строка, будет добавлено в конец имени файла.
	 */
	public static function makeThumbnail($options)
	{
		$options=array_merge(array(
			'file'=>'',
			'maxWidth'=>0,
			'maxHeight'=>0,
			'imageFormat'=>'jpg',
			'replace'=>array(),
			'addPostfix'=>'',
		),$options);
		try
		{
			$im=new Imagick($options['file']);
		}
		catch(Exception $e)
		{
			return array($e->getMessage());
		}
		$d=$im->getImageGeometry();
		$im->setImageFormat($options['imageFormat']);
		$im->setImageBackgroundColor('white');
		$im=$im->flattenImages();
		if ($options['maxHeight']&&($d['height']>$options['maxHeight']))
		{
			$im->thumbnailImage(0,$options['maxHeight']);
		}
		$d=$im->getImageGeometry();
		if ($options['maxWidth']&&($d['width']>$options['maxWidth']))
		{
			$im->thumbnailImage($options['maxWidth'],0);
		}
		$pathinfo=pathinfo($options['file']);
		$newfile=$pathinfo['dirname'].DIRECTORY_SEPARATOR.strtr($pathinfo['filename'],$options['replace']).$options['addPostfix'];
		if ($im->writeImage($newfile.'.'.$options['imageFormat']))
		{
			return true;
		}
		else
		{
			return array('Imagick can not write file.');
		}
	}

	/**
	 * Метод для рекурсивного копирования каталога.
	 * @param string $source путь к исходному каталогу.
	 * @param string $dest путь, куда будет сделана копия.
	 * @param array $options дополнительные параметры. Поддерживаются 2 параметра:
	 * integer 'folderPermission' - права для каталогов. По умолчанию 0755.
	 * integer 'filePermission' - права для файлов. По умолчанию 0755.
	 * @return boolean true.
	 */
	public static function copy($source,$dest,$options=array('folderPermission'=>0755,'filePermission'=>0755))
	{
		$result=false;
		if (is_file($source))
		{
			if ($dest[strlen($dest)-1]=='/')
			{
				if (!file_exists($dest))
				{
					self::createDirectory($dest,$options['folderPermission'],true);
				}
				$__dest=$dest.'/'.basename($source);
			}
			else
			{
				$__dest=$dest;
			}
			$result=copy($source,$__dest);
			@chmod($__dest,$options['filePermission']);
		}
		elseif (is_dir($source))
		{
			if ($dest[strlen($dest)-1]=='/')
			{
				if ($source[strlen($source)-1]=='/')
				{
					// Copy only contents
				}
				else
				{
					// Change parent itself and its contents
					$dest=$dest.basename($source);
					self::createDirectory($dest,$options['folderPermission']);
				}
			}
			else
			{
				// Copy parent directory with new name and all its content
				self::createDirectory($dest,$options['folderPermission']);
			}
			$dirHandle=opendir($source);
			while ($file=readdir($dirHandle))
			{
				if ($file!='.'&&$file!='..')
				{
					if (!is_dir($source.'/'.$file))
					{
						$__dest=$dest.'/'.$file;
					}
					else
					{
						$__dest=$dest.'/'.$file;
					}
					$result=self::copy($source.'/'.$file,$__dest,$options);
				}
			}
			closedir($dirHandle);
		}
		else
		{
			$result=false;
		}
		return $result;
	}

}

?>
