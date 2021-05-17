<?php declare(strict_types=1);

namespace VitesseCms\Form\Listeners;

use Phalcon\Events\Manager;
use VitesseCms\Form\Blocks\FormBuilder;
use VitesseCms\Form\Controllers\AdminsubmissionController;

class InitiateAdminListeners
{
    public static function setListeners(Manager $eventsManager): void
    {
        $eventsManager->attach('adminMenu', new AdminMenuListener());
        $eventsManager->attach(AdminsubmissionController::class, new AdminsubmissionControllerListener());
        $eventsManager->attach( FormBuilder::class, new BlockFormBuilderListener());
    }
}
