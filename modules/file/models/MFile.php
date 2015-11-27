<?php

/**
 * Базовая модель файла. Все модели файлов, загружаемых при помощи виджета CAjaxUpload, должны наследоваться от него.
 */
class MFile extends EActiveRecord
{

	const STATUS_INACTIVE	= 0;
	const STATUS_ACTIVE	= 1;

	/**
	 * @var CUploadedFile экземпляр загруженного(ых) файла(ов). Валидировать, сохранять в базу данных файл(ы) нужно через это свойство.
	 */
	public $file;

	/**
	 * @var string путь для сохранения файла на сервере. По умолчанию - свойство FileModule->uploadPath .
	 */
	public $path=null;

	/**
	 * @var array замены пути в виде пар "что заменять" => "на что заменять".
	 * Замены по умолчанию:
	 * '%ID_USER%' - ID текущего пользователя в виде строки из 14 символов, с ведущими нулями. Т.е. для пользователя с ID 1 замена будет такой: "00000000000001".
	 * '%NAME%' - оригинатльное имя файла (без расширения).
	 * '%EXTENSION%' - оригинательно расшиерние файла.
	 */
	public $pathReplacements=array();

	/**
	 * @var string имя файла для сохранения на сервере. По умолчанию - оригинальное имя файла.
	 */
	public $name=null;

	/**
	 * @var array замены имени файла в виде пар "что заменять" => "на что заменять".
	 * Замены по умолчанию: см. описание свойства $pathReplacements .
	 */
	public $nameReplacements=array();

	/**
	 * Этот метод должен быть точно в таком же виде во всех наследниках этого класса.
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}

	/**
	 * Правила валидации модели.
	 */
	public function rules()
	{
		return array(
			array('file','file', // см. описание CFileValidator .
				'maxFiles'=>1,
				'maxSize'=>3145728,
			),
		);
	}

	public function init()
	{
		if (!$this->path)
		{
			$this->path=Yii::app()->getModule('file')->uploadPath;
		}
	}

	/**
	 * Метод, вызываемый после успешной загрузки файла(ов). Должен быть переопределен.
	 * @param array $fileInfo дополнительная информация о загруженном файле:
	 * 'path' - путь
	 * 'url' - URL
	 * 'name' - имя
	 * 'extension' - расширение
	 * 'basename' - полное имя (имя.расширение)
	 * Основная информация о файле содержится в свойстве модели $file .
	 * @return mixed в случае если метод вернул array, эти данные добавятся к данным, отправляемым в формате JSON виджету CAjaxUpload.
	 * Внимание! Ключи 'success' и 'files' используются системой и могут быть перетерты этими данными.
	 */
	public function afterUpload($fileInfo)
	{
	}

}

?>
