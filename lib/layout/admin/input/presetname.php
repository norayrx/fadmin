<?php
/**
 * Created by PhpStorm.
 * User: lenovo
 * Date: 12.09.2017
 * Time: 15:43
 *
 * @author Pavel Shulaev (https://rover-it.me)
 */

namespace Rover\Fadmin\Layout\Admin\Input;

class Presetname extends Text
{
    /**
     * @return mixed|void
     * @author Pavel Shulaev (https://rover-it.me)
     */
    public function getType()
    {
        return \Rover\Fadmin\Inputs\Text::getType();
    }
}