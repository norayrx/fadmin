#Модуль для Битрикс «Конструктор административной части»
##Оглавление
* [Описание](#Описание)	
* [Использование](#Использование)
	* [Файл options.php](#Файл-optionsphp)
	* [Файл с вашими настройками](#Файл-с-вашими-настройками)
* [Структура массива, возвращаемого getConfig()](#Структура-массива-возвращаемого-getconfig)
	* [Структура 'settings'](#Структура-settings)
	* [Структура 'tabs'](#Структура-tabs)
	* [Конфигурация инпутов на вкладке](#Конфигурация-инпутов-на-вкладке)
* [Получение значений опций](#Получение-значений-опций)
* [Работа с пресетами](#Работа-с-пресетами)
* [События](#События)
* [Сообщения](#Сообщения)
* [Требования](#Требования)
* [Контакты](#Контакты)

##Описание
«Конструктор административной части» - это фреймворк,  позволяющий быстро настроить и запустить административную часть для модуля Битрикс. Отрисовку административной части и работу с опциями фреймворк берет на себя. Вам необходимо создать файл, унаследованный от `\Rover\Fadmin\Options` и переопределить в нем метод `getConfig()`, в котором следует указать расположение полей и дать им уникальные имена.

Модуль доступен на [Маркетплейсе Битрикса](http://marketplace.1c-bitrix.ru/solutions/rover.fadmin/).

«Конструктор» поддерживает:
* многосайтовость;
* создание пресетов;
* множественные вкладки;
* настройку внешнего вида инпутов;
* события;
* вывод сообщени;

##Использование
Чтобы подключить «Конструктор» к вашему модулю, необходимо создать 2 файла.
 
В решении уже есть демо-файл `lib/testoptions.php`, в котором находится пример такого класса. Страницу настроек, которая получается на основе настроек в этом файле, вы можете найти в административной части сайта в разделе «Настройки» -> «Настройки продукта» -> «Настройки модулей» -> «Констуктор администативной части».
###Файл `options.php`
Файл `options.php` входит в стандартный набор файлов любого модуля, в нём обычно находится логика, отвечающая за административную часть и работу с её настройками. Для подключения «Констуктора» этот файл должен иметь подобное содержание:
	
	use \Bitrix\Main\Localization\Loc;
	use \Bitrix\Main\SystemException;
	use \Bitrix\Main\Loader;
	use \Rover\Fadmin\Admin\Panel;
	/**
	 * Имя вашего класса, унаследованного от \Rover\Fadmin\Options
	 */
	use \Rover\Fadmin\TestOptions;
	
	if (!Loader::includeModule($mid)
	   || !Loader::includeModule('rover.fadmin))
	   throw new SystemException('module "' . $mid . '" not found!');
	
	Loc::loadMessages(__FILE__);
	Loc::loadMessages($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/options.php");
	
	(new Panel(TestOptions::getInstance($mid)))->show();
###Файл с вашими настройками
В папке `lib` вашего модуля вам следует создать файл с классом, унаследованным от `\Rover\Fadmin\Options`. Он будет отвечать за настройки вашего модуля. Ваш класс должен реализовывать публичный метод `getConfig()`.
##Структура массива, возвращаемого `getConfig()`
	
    public function getConfig()
	{
	    return [
            'tabs' => [
                ...
			],
			'settings' => [
				...
			]
		];	
    }	
`getConfig()` возвращает ассоциативный массив с 2мя ключами. В `'tabs'` находится массивы с конфигурацией вкладок и полей страницы настроек модуля, в `'settings'` – другие общие настройки. 
###Структура `'settings'`
На данный момент в `'settings'` может находиться лишь один параметр `\Rover\Fadmin\Options::SETTINGS__CHECKBOX_BOOLEAN`. Он отвечает за то, как будет возвращаться значение опции типа `Input::TYPE__CHECKBOX` (чекбокс). Если параметр не указан, либо равен `false`, то возвращаемое значение опции будет `'Y'` или `'N'`, а если параметр равен `true`, то, соответственно, – `true` или `false`.
###Структура `'tabs'`
Для примера возьмём структуру, которая находится в демо-классе `Rover\Fadmin\TestOptions`:

	'tabs' => [
		[
            'name'          => 'test_tab',
            'label'         => 'test tab',
            'description'   => 'test tab description',
            'siteId'        => 's1',
            'inputs'        => [
            ...
			] 
		]
		...	
    ],
Каждый массив, находящийся в `'tabs'` описывает одну вкладку либо шаблон вкладки для пресета. Массив содержит следующие ключи:
* `name` – уникальное имя вкладки в системе, обязательное;
* `label` – название вкладки, обязательное;
* `description` – описание вкладки, не обязательное;
* `siteId` – идентификатор сайта, к которому относятся настройки на вкладке, не обязательное, если не указан, то настройки используются для всех сайтов;
* `preset` – если указан и равен true, то это говорит о том, что данная вкладка является шаблоном пресета.
* `inputs` – массив с конфигурацией инпутов вкладки;

Инпуты могут быть различных типов (`namespace Rover\Fadmin\Inputs\Input`). Простые типы:
* `Input::TYPE__CHECKBOX` – чекбокс;
* `Input::TYPE__CLOCK` – выбор времени;
* `Input::TYPE__COLOR` – выбор цвета;
* `Input::TYPE__FILE` – загрузка файла (в т.ч. изображения);
* `Input::TYPE__HEADER` – заголовок, не может иметь значение;
* `Input::TYPE__HIDDEN` – скрытое поле;
* `Input::TYPE__IBLOCK` – выбор инфоблока;
* `Input::TYPE__LABEL` – вывод текста, не может иметь значение;
* `Input::TYPE__NUMBER` – строка для ввода числа;
* `Input::TYPE__SELECTBOX` – выпадающий список;
* `Input::TYPE__SUBMIT` – отправка формы;
* `Input::TYPE__TEXT` – строка для ввода текста;
* `Input::TYPE__TEXTAREA` – поле для ввода текста.

Специальные типы:
* `Input::TYPE__ADD_PRESET` – добавление пресета;
* `Input::TYPE__REMOVE_PRESET` – удаление пресета;
* `Input::TYPE__CUSTOM` – пользовательский элемент.

###Конфигурация инпутов на вкладке

	'inputs' => [
		[
			'type'      => Input::TYPE__HEADER,
			'name'      => 'input_header',
			'label'     => 'First tab header',
		],
		[
			'type'      => Input::TYPE__TEXT,
			'name'      => 'text',
			'label'     => 'text input (maxlength 50)',
			'default'   => 'default text',
			'maxLength' => 50
		],
		...	
	],
Конфигурация каждого инпута имеет обязательные и не обязательные параметры.  Обязательные:
* `type` – тип инпута 
* `name` – уникальное имя инпута в системе, не обязательное для элементов, которые не могут иметь значение (`Input::TYPE__HEADER` и `Input::TYPE__LABEL`)
* `label` – название инпута, которое будет отображено рядом с ним на вкладке.

Необязательные параметры зависят от типа инпута и если они не указаны явно, то для них берется значение по умолчанию. Общими необязательными параметрами являются:
* `id` – id инпута, если не указан, то берется из параметра name;
* `default` – значение по умолчанию;
* `help` – подпись под инпутом со справочной информацией.

Со всеми параметрами вы можете ознакомиться, изучив классы инпутов в «Конструкторе».

##Получение значений опций
При сохранении значений в административной части, значения инпутов записываются в опции модуля. Для получения этих значений в любой части сайта доступны 2 метода:
* `getNormalValue($inputName, $siteId = '', $reload = false)` - для получения значения обычной опции
* `getPresetValue($inputName, $presetId, $siteId = '', $reload = false)` - для поличения значения опции из пресета

Аргументы:
* `$inputName` – имя инпута (равное атрибуту `name` инпута)
* `$presetId` – номер пресета
* `$siteId` – идентификатор сайта
* `$reload` – при первом обращении значение опции попадает в кеш и при следующих обращениях по умолчанию берется из него. Если аргумент равен true, то значение повторно берется из базы.

На практике бывает удобно сделать обёртку над этими методами, например, как это сделано в демо-классе. Для получения значения обычной опции:
	
	public function getTextareaValueS1($reload = false)
	{
	    return $this->getNormalValue('input_textarea', 's1', $reload);
	}
Для получения значения опции из пресета:

	public function getS1PresetColor($presetId, $reload = false)
	{
	    return $this->getPresetValue('preset_color', $presetId, 's1', $reload);
	}
##Работа с пресетами
Для создания пресета необходимо указать параметры его вкладки в конфигурации. В параметрах вкладки ключ `'preset'` должен быть равен `true`. Рассмотрим пример конфигурации кладки пресета из демо-класса для сайта `s1`:

	'tabs' => 
		...
		[
			'name'          => 'presetTab',
			'label'         => 'Preset',
			'preset'        => true,
			'description'   => 'This is a description of preset tab',
			'siteId'        => 's1',
			'inputs'        => [
			[
				'type'      => Input::TYPE__HEADER,
				'name'      => 'preset_header',
				'label'     => 'Preset header',
			],
			[
				'type'      => Input::TYPE__COLOR,
				'name'      => 'preset_color',
				'label'     => 'preset color',
				'default'   => '#FFAA00',
				'help'      => 'color help',
			],
			[
				'type'      => Input::TYPE__REMOVE_PRESET,
				'name'      => 'remove_preset',
				'label'     => 'remove_preset',
				'popup'     => 'Are you sure?',
			],
		],
	...
Для возможности удаления пресета на его вкладке нужно разместить и настроить элемент типа `Input::TYPE__REMOVE_PRESET`. Для возможности создавать пресет на обычной вкладке нужно разместить и настроить элемент типа `Input::TYPE__ADD_PRESET`.

Для каждого сайта может быть использован только один шаблон пресета. Если в конфигурации их несколько, то все последующие, кроме первого, будут игнорироваться. При создании пресета будет браться шаблон для того сайта, на вкладке которого была нажата кнопка `Input::TYPE__ADD_PRESET`. Пока для сайта не создан ни один пресет, шаблон его вкладки в настройках отображен не будет.
##События
Для возможности более гибкой работы с настройками в «Конструкторе» предусмотрена система событий. Название всех событий определены в классе `\Rover\Fadmin\Options` в константах, начинающихся на `EVENT__`.
 
Чтобы использовать событие в своём модуле, вам необходимо создать соответствующий метод в вашем классе, унаследованном от `\Rover\Fadmin\Options`. Если метод, определённый в константе, начинающейся с `EVENT__BEFORE_`, вернет false, то действие, обычно совершаемое после этого события, выполнено не будет. Это не относится к событию `'beforeGetTabInfo'`. Оно позволяет только изменить информацию о вкладке, но не отменить ее отображение.
##Сообщения
Для информирования администратора о непредвиденных ситуациях, либо об успешном выполнении операции, в классе, унаследованном от `\Rover\Fadmin\Options` доступен метод `public addMessage($message, $type = 'OK')`. Параметр `$type` может принимать значения `'OK'` и `'ERROR'`.

Сообщения выводятся над вкладками настроек вашего модуля.
##Требования
Для работы модуля необходим установленный на хостинге php версии 5.4 или выше.
##Контакты
По всем вопросам вы можете связаться со мной по email: rover.webdev@gmail.com, либо через форму на сайте http://rover-it.me. Гитхаб проекта: https://github.com/pavelshulaev/fadmin