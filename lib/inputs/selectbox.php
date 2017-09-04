<?php
/**
 * Created by PhpStorm.
 * User: lenovo
 * Date: 11.01.2016
 * Time: 17:33
 *
 * @author Pavel Shulaev (http://rover-it.me)
 */

namespace Rover\Fadmin\Inputs;

use Rover\Fadmin\Tab;
use Bitrix\Main\Event;

/**
 * Class Selectbox
 *
 * @package Rover\Fadmin\Inputs
 * @author  Pavel Shulaev (http://rover-it.me)
 */
class Selectbox extends Input
{
	public static $type = self::TYPE__SELECTBOX;

	/**
	 * @var array
	 */
	protected $options = [];

	/**
	 * multiple selectbox size
	 * @var int
	 */
	protected $size = 7;

	/**
	 * @param array $params
	 * @param Tab   $tab
	 * @throws \Bitrix\Main\ArgumentNullException
	 */
	public function __construct(array $params, Tab $tab)
	{
		parent::__construct($params, $tab);

		if (isset($params['options']))
			$this->options = $params['options'];

		if (isset($params['size']) && intval($params['size']))
			$this->size = intval($params['size']);
		elseif ($params['multiple'])
			$this->size = count($this->options) > $this->size
				? $this->size
				: count($this->options);
		else
			$this->size = 1;
	}

    /**
     * @param bool $empty
     * @author Pavel Shulaev (https://rover-it.me)
     */
    public function showLabel($empty = false)
    {
        if ($this->multiple)
            parent::showMultiLabel();
        else
            parent::showLabel($empty);
    }

    /**
     * @author Pavel Shulaev (https://rover-it.me)
     */
	public function showInput()
    {
        $valueId    = $this->getValueId();
        $valueName  = $this->getValueName();

        ?><select
            <?=$this->disabled ? 'disabled="disabled"': '';?>
            name="<?=$valueName . ($this->multiple ? '[]' : '')?>"
            id="<?=$valueId?>"
            size="<?=$this->size?>"
            <?=$this->multiple ? ' multiple="multiple" ' : ''?>>
        <?php
            foreach($this->options as $v => $k){
                if ($this->multiple) {
                    $selected = is_array($this->value) && in_array($v, $this->value)
                        ? true
                        : false;
                } else {
                    $selected = $this->value==$v ? true : false;
                }

                ?><option value="<?=$v?>"<?=$selected ? " selected=\"selected\" ": ''?>><?=$k?></option><?php
            }
        ?>
        </select>
        <?php
    }

	/**
	 * @param array $options
	 * @author Pavel Shulaev (http://rover-it.me)
	 */
	public function setOptions(array $options)
	{
		$this->options = $options;
	}

	/**
	 * @return array
	 * @author Pavel Shulaev (http://rover-it.me)
	 */
	public function getOptions()
	{
		return $this->options;
	}
}