<?php declare(strict_types=1);

namespace VitesseCms\Form\Services;

use VitesseCms\Form\Factories\ElementFactory;

class FormService
{
    /**
     * @var ElementFactory
     */
    public $elementFactory;

    public function __construct(ElementFactory $elementFactory){
        $this->elementFactory = $elementFactory;
    }
}