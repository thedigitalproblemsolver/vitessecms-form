<?php declare(strict_types=1);

namespace VitesseCms\Form\Helpers;

use Phalcon\Forms\Form;

class FormHelper
{
    //TODO move to mustache
    public static function buildTableFromElements(Form $form): string
    {
        $html = '<table>';
        foreach ($form->getElements() as $element) :
            $html .= '<tr>
                <td>' . $element->getName() . '</td>
                <td width="15px"></td>
                <td>' . $element->getValue() . '</td>
            ';
        endforeach;
        $html .= '</table>';

        return $html;
    }
}
