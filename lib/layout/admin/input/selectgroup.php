<?php
/**
 * Created by PhpStorm.
 * User: lenovo
 * Date: 12.09.2017
 * Time: 15:52
 *
 * @author Pavel Shulaev (https://rover-it.me)
 */

namespace Rover\Fadmin\Layout\Admin\Input;

/**
 * Class Selectgroup
 *
 * @package Rover\Fadmin\Layout\Admin\Input
 * @author  Pavel Shulaev (https://rover-it.me)
 */
class Selectgroup extends Selectbox
{
    /** @var array */
    protected static $idCache = array();

    /**
     * @return mixed|void
     * @throws \Bitrix\Main\ArgumentNullException
     * @throws \Bitrix\Main\ArgumentOutOfRangeException
     * @throws \Bitrix\Main\SystemException
     * @author Pavel Shulaev (https://rover-it.me)
     */
    public function showInput()
    {
        echo $this->getList();
    }

    /**
     * @return string
     * @throws \Bitrix\Main\ArgumentNullException
     * @throws \Bitrix\Main\ArgumentOutOfRangeException
     * @throws \Bitrix\Main\SystemException
     * @author Pavel Shulaev (https://rover-it.me)
     */
    protected function getList()
    {
        if (!$this->input instanceof \Rover\Fadmin\Inputs\Selectgroup)
            return '';

        $options = $this->input->getOptions();

        if (empty($options))
            return '-';

        $optionsId  = md5(serialize($options));

        $value = $this->input->getValue();
        $value = empty($value) ? array() : $value;

        if (!is_array($value))
            $value = array($value);

        // change group script
        $html = '';

        if(!isset(self::$idCache[$optionsId]))
            $html .= '
			<script type="text/javascript">
                function OnType_'.$optionsId.'_Changed(typeSelect, selectID)
                {
                    var items       = '.\CUtil::PhpToJSObject($this->input->getOptions()).';
                    var selected    = BX(selectID);
                    
                    if(!!selected)
                    {
                        for(var i=selected.length-1; i >= 0; i--){
                            selected.remove(i);
                        }
                            
                        for(var j in items[typeSelect.value]["options"])
                        {
                            var newOption = new Option(items[typeSelect.value]["options"][j], j, false, false);
                            selected.options.add(newOption);
                        }
                    }
                }
			</script>
			';

        $groupValue     = $this->input->getGroupValue() ?: $this->input->calcGroupValue();
        $valueName      = $this->input->getFieldName();
        $valueGroupName = $this->input->getGroupValueName();
        $onChangeGroup  = 'OnType_'.$optionsId.'_Changed(this, \''.\CUtil::JSEscape($valueName).'\');';

        $html .= '<select 
                ' . ($this->input->isDisabled() ? 'disabled="disabled"': '') . '
                name="' . $valueGroupName . '"
                id="' . $valueGroupName . '"
                onchange="'.htmlspecialcharsbx($onChangeGroup).'">'."\n";

        foreach($options as $key => $optionValue)
            $html .= '<option value="' . htmlspecialcharsbx($key) . '"' . ($groupValue == $key? ' selected': '') . '>'
                . htmlspecialcharsEx(isset($optionValue['name']) ? $optionValue['name'] : $key)
                . '</option>'."\n";

        $html .= "</select>\n";
        $html .= "&nbsp;\n";
        $html .= '<select
                    ' . ($this->input->isDisabled() ? ' disabled="disabled" ': '') . ' 
                    ' . ($this->input->isRequired() ? ' required="required" ': '') . ' 
                    name="' . $valueName . ($this->input->isMultiple()
                ? '[]" multiple="multiple" size="' . $this->input->getSize() . '" '
                : '"')
            . ' id="' . $valueName . '">'."\n";

        if (!is_null($groupValue))
            foreach($options[$groupValue]['options'] as $key => $optionValue)
                $html .= '<option value="' . htmlspecialcharsbx($key) . '"' . (in_array($key, $value)? ' selected': '').'>'
                    . htmlspecialcharsEx($optionValue)
                    . '</option>'."\n";

        $html .= "</select>\n";

        return $html;
    }
}