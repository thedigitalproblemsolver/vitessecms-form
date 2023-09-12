<?php declare(strict_types=1);

namespace VitesseCms\Form\Listeners\Controllers;

use Phalcon\Events\Event;
use VitesseCms\Admin\Forms\AdminlistFormInterface;
use VitesseCms\Form\Controllers\AdminsubmissionController;

class AdminsubmissionControllerListener
{
    public function adminListFilter(Event $event, AdminsubmissionController $controller, AdminlistFormInterface $form): void
    {
        $form->addNameField($form);
        $form->addPublishedField($form);
    }
}
