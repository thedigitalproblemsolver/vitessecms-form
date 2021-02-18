<?php declare(strict_types=1);

namespace VitesseCms\Form\Listeners;

use Phalcon\Events\Event;
use VitesseCms\Admin\Forms\AdminlistFormInterface;
use VitesseCms\Form\Controllers\AdminsubmissionController;
use VitesseCms\Form\Helpers\SubmissionHelper;
use VitesseCms\Form\Models\Submission;

class AdminsubmissionControllerListener
{
    public function beforeEdit(Event $event, AdminsubmissionController $controller, Submission $submission): void {
        $controller->addRenderParam('adminEditForm', SubmissionHelper::getHtmlAdminTable($submission, true));
    }

    public function adminListFilter(
        Event $event,
        AdminsubmissionController $controller,
        AdminlistFormInterface $form
    ): string
    {
        $form->addNameField($form);
        $form->addPublishedField($form);

        return $form->renderForm(
            $controller->getLink() . '/' . $controller->router->getActionName(),
            'adminFilter'
        );
    }
}
