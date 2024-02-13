<?php

declare(strict_types=1);

namespace VitesseCms\Form\Listeners;

use VitesseCms\Communication\Repositories\NewsletterRepository;
use VitesseCms\Core\Interfaces\InitiateListenersInterface;
use VitesseCms\Core\Interfaces\InjectableInterface;
use VitesseCms\Datagroup\Repositories\DatagroupRepository;
use VitesseCms\Form\Blocks\FormBuilder;
use VitesseCms\Form\Controllers\AdminsubmissionController;
use VitesseCms\Form\Enums\SubmissionEnum;
use VitesseCms\Form\Listeners\Admin\AdminMenuListener;
use VitesseCms\Form\Listeners\Blocks\BlockFormBuilderListener;
use VitesseCms\Form\Listeners\Controllers\AdminsubmissionControllerListener;
use VitesseCms\Form\Listeners\Models\SubmissionListener;
use VitesseCms\Form\Listeners\Models\UserListener;
use VitesseCms\Form\Repositories\SubmissionRepository;
use VitesseCms\User\Models\User;

class InitiateAdminListeners implements InitiateListenersInterface
{
    public static function setListeners(InjectableInterface $injectable): void
    {
        $injectable->eventsManager->attach('adminMenu', new AdminMenuListener());
        $injectable->eventsManager->attach(AdminsubmissionController::class, new AdminsubmissionControllerListener());
        $injectable->eventsManager->attach(
            FormBuilder::class,
            new BlockFormBuilderListener(
                new DatagroupRepository(),
                new NewsletterRepository()
            )
        );
        $injectable->eventsManager->attach(
            SubmissionEnum::LISTENER->value,
            new SubmissionListener(new SubmissionRepository())
        );
        $injectable->eventsManager->attach(
            User::class,
            new UserListener(
                $injectable->log,
                new SubmissionRepository()
            )
        );
    }
}
