<?php declare(strict_types=1);

namespace VitesseCms\Form\Utils;

use Phalcon\Forms\Element\ElementInterface;

class ElementUiUtil
{
    public static function setTemplate(ElementInterface $element, string $template): ElementInterface
    {
        if (empty($element->getAttribute('template'))) :
            $element->setAttribute('template', $template);
        endif;

        return $element;
    }
}
