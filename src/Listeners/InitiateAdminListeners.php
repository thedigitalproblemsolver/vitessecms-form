<?php declare(strict_types=1);

namespace VitesseCms\Form\Listeners;

use Phalcon\Events\Manager;
use VitesseCms\Admin\Repositories\DatagroupRepository;
use VitesseCms\Communication\Repositories\NewsletterRepository;
use VitesseCms\Form\Blocks\FormBuilder;
use VitesseCms\Form\Controllers\AdminsubmissionController;
use VitesseCms\Form\Listeners\Admin\AdminMenuListener;
use VitesseCms\Form\Listeners\Blocks\BlockFormBuilderListener;
use VitesseCms\Form\Listeners\Controllers\AdminsubmissionControllerListener;

class InitiateAdminListeners
{
    public static function setListeners(Manager $eventsManager): void
    {
        $eventsManager->attach('adminMenu', new AdminMenuListener());
        $eventsManager->attach(AdminsubmissionController::class, new AdminsubmissionControllerListener());
        $eventsManager->attach( FormBuilder::class, new BlockFormBuilderListener(
            new DatagroupRepository(),
            new NewsletterRepository()
        ));
    }
}
