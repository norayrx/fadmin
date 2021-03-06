# Конфигурация
Чтобы использовать возможности «Конструктора» в пользовательском модуле, необходимо:
* в папке `lib` пользовательтского модуля создать файл с классом, унаследованным от `\Rover\Fadmin\Options`. Этот класс должен реализовывать публичный метод `getConfig()`. 
В решении есть демо-файл [`lib/testoptions.php`](../lib/testoptions.php) с примером такого класса. Страницу настроек, получаемую на основе конфигурации, возвращаемой `getConfig()` демо-класса, можно найти в административной части в разделе «Настройки» -> «Настройки продукта» -> «Настройки модулей» -> «Констуктор администативной части».
* в файле `options.php` подключаемого модуля вызвать метод подключения «Конструктора административной части». Примерную структуру этого файла можно взять из файла [`options.php`](../options.php) самого «Конструктора».

## Структура массива настроек, возвращаемого `getConfig()`
	
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
`getConfig()` возвращает ассоциативный массив с 2мя ключами. В `'tabs'` находится массивы с конфигурацией вкладок и полей страницы настроек модуля, в `'settings'` — другие общие настройки. 

Если ключ `'tabs'` пуст, то табы с настройками модуля не выводятся. Если ключ `'settings'` пуст, то берутся настройки по умолчанию. 
### Структура `'settings'`
На данный момент в `'settings'` можно передать следующие параметры (в скобках — значение по умолчанию)
* `\Rover\Fadmin\Options\Settings::BOOL_CHECKBOX (false)`. Формат возвращаемого значения инпута типа `checkbox`: `'Y'`, `'N'` или `true`, `false`.
* `\Rover\Fadmin\Options\Settings::LOG_ERRORS (false)`. Логировать ошибки с помощью метода Bitrix\Main\Application::getInstance()->getExceptionHandler()->writeToLog(). (в разработке)
* `\Rover\Fadmin\Options\Settings::USE_SORT (false)`. Выводить инпуты на табе в порядке, заданном их параметром `sort` (по возрастанию). Для корректной работы необходимо задать этот инпутам параметр при инициализации.
* `\Rover\Fadmin\Options\Settings::PRESET_CLASS ('\Rover\Fadmin\Preset')`. Переопредеение класса пресета. Новый класс должен наследовать стандартный. Даёт возможность сохранять в пресетах доп. параметры и т.п.
* `\Rover\Fadmin\Options\Settings::SHOW_ADMIN_PRESETS (true)`. Выодить вкладки пресетов в разделе настроек модуля.
### Структура `'tabs'`
Для примера возьмём структуру, которую генеирирует демо-класс [`Rover\Fadmin\TestOptions`](../lib/testoptions.php):

	'tabs' => [
		[
            'name'      => 'test_tab',
            'label'     => 'test tab',
            'default'   => 'test tab description',
            'siteId'    => 's1',
            'inputs'    => [
            ...
			] 
		]
		...	
    ],
    ...
Каждый массив, находящийся в `'tabs'` описывает одну вкладку, либо шаблон вкладки для пресета. Массив содержит следующие ключи:
* `name` – уникальное имя вкладки в системе, обязательное;
* `label` – название вкладки, обязательное;
* `default` – описание вкладки, не обязательное;
* `siteId` – идентификатор сайта, к которому относятся настройки на вкладке, не обязательное, если не указан, то настройки используются для всех сайтов;
* `preset` – если указан и равен true, то это говорит о том, что данная вкладка является шаблоном пресета.
* `inputs` – массив с конфигурацией инпутов вкладки;

Инпуты могут быть различных типов. Тип можно получить, вызвав подобный код:
    
    use Rover\Fadmin\Inputs\Input;
 
    echo Input\Radio::getType();        // radio
    echo Input\Remoepreset::getType();  // removepreset
    ...

### Конфигурация инпутов на вкладке

    use Rover\Fadmin\Inputs\Input;

	'inputs' => [
		[
			'type'      => Input\Header::getType(),
			'label'     => 'First tab header',	
		],
		[
			'type'      => Input\Text::getType(),
			'name'      => 'text',
			'label'     => 'text input (maxlength 50)',
			'default'   => 'default text',
			'maxLength' => 50
		],
		...	
	],
	...
Конфигурация каждого инпута имеет обязательные и не обязательные параметры.  Обязательные:
* `type` – тип инпута 
* `name` – уникальное имя инпута в системе, не обязательное для элементов, которые не могут иметь значение (`Input\Header` и `Input\Label`)
* `label` – название инпута, которое будет отображено рядом с ним на вкладке.

Необязательные параметры зависят от типа инпута и если они не указаны явно, то для них берется значение по умолчанию. Общими необязательными параметрами являются:
* `id` – id инпута, если не указан, то берется из параметра `name`;
* `default` – значение по умолчанию  (по умолч. `''`);
* `multiple` – множественное значение или нет  (по умолч. `false`);
* `disabled` – активен или нет (по умолч. `true`);
* `sort` – значение для сортировки инпута на табе, актуально, если в настройках параметр `\Rover\Fadmin\Options\Settings::USE_SORT` равен `true` (по умолч. 500);
* `help` – подпись под инпутом со справочной информацией (по умолч. `''`);
* `hidden` – скрытый или нет (по умолч. `false`);
* `required` – обязательный или нет  (по умолч. `false`);
* `preInput` - текст, котрый выводится непосредственно перед инпутом (по умолч. `''`);
* `postInput` - текст, котрый выводится непосредственно после инпута (по умолч. `''`);

---
Со всеми параметрами вы можете ознакомиться в разделе [api, посвященному инпутам](./api/inputs/input.md), либо самостоятельно, изучив классы инпутов в «Конструкторе».

[на главную](../README.md)