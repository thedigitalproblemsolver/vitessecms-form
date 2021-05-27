<?php declare(strict_types=1);

namespace VitesseCms\Form\Listeners;

use VitesseCms\Communication\Repositories\NewsletterRepository;
use VitesseCms\Core\Interfaces\InitiateListenersInterface;
use VitesseCms\Core\Interfaces\InjectableInterface;
use VitesseCms\Datagroup\Repositories\DatagroupRepository;
use VitesseCms\Form\Blocks\FormBuilder;
use VitesseCms\Form\Controllers\AdminsubmissionController;
use VitesseCms\Form\Listeners\Admin\AdminMenuListener;
use VitesseCms\Form\Listeners\Blocks\BlockFormBuilderListener;
use VitesseCms\Form\Listeners\Controllers\AdminsubmissionControllerListener;

class InitiateAdminListeners implements InitiateListenersInterface
{
    public static function setListeners(InjectableInterface $di): void
    {
        $di->eventsManager->attach('adminMenu', new AdminMenuListener());
        $di->eventsManager->attach(AdminsubmissionController::class, new AdminsubmissionControllerListener());
        $di->eventsManager->attach( FormBuilder::class, new BlockFormBuilderListener(
            new DatagroupRepository(),
            new NewsletterRepository()
        ));
    }
}
