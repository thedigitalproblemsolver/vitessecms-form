<?php declare(strict_types=1);

namespace VitesseCms\Form\Enums;

use VitesseCms\Core\AbstractEnum;

enum FormEnum: string
{
    case SERVICE_LISTENER = 'FormListener';
    case AFTER_SUBMIT = 'FormListener:afterSubmit';
}
