<?php

declare(strict_types=1);

namespace VitesseCms\Form\Controllers;

use ArrayIterator;
use stdClass;
use VitesseCms\Admin\Interfaces\AdminModelDeletableInterface;
use VitesseCms\Admin\Interfaces\AdminModelListInterface;
use VitesseCms\Admin\Interfaces\AdminModelPublishableInterface;
use VitesseCms\Admin\Interfaces\AdminModelReadOnlyInterface;
use VitesseCms\Admin\Traits\TraitAdminModelDeletable;
use VitesseCms\Admin\Traits\TraitAdminModelList;
use VitesseCms\Admin\Traits\TraitAdminModelPublishable;
use VitesseCms\Admin\Traits\TraitAdminModelReadOnly;
use VitesseCms\Admin\Traits\TraitAdminModelSave;
use VitesseCms\Core\AbstractControllerAdmin;
use VitesseCms\Database\AbstractCollection;
use VitesseCms\Database\Models\FindOrder;
use VitesseCms\Database\Models\FindOrderIterator;
use VitesseCms\Database\Models\FindValueIterator;
use VitesseCms\Form\Enums\SubmissionEnum;
use VitesseCms\Form\Models\Submission;
use VitesseCms\Form\Repositories\SubmissionRepository;

class AdminsubmissionController extends AbstractControllerAdmin implements
    AdminModelPublishableInterface,
    AdminModelListInterface,
    AdminModelDeletableInterface,
    AdminModelReadOnlyInterface
{
    use TraitAdminModelDeletable;
    use TraitAdminModelList;
    use TraitAdminModelPublishable;
    use TraitAdminModelReadOnly;
    use TraitAdminModelSave;

    private readonly SubmissionRepository $submissionRepository;

    public function onConstruct()
    {
        parent::onConstruct();

        $this->submissionRepository = $this->eventsManager->fire(SubmissionEnum::GET_REPOSITORY->value, new stdClass());
    }

    public function getModel(string $id): ?AbstractCollection
    {
        return match ($id) {
            'new' => new Submission(),
            default => $this->submissionRepository->getById($id, false)
        };
    }

    public function getModelList(?FindValueIterator $findValueIterator): ArrayIterator
    {
        return $this->submissionRepository->findAll(
            $findValueIterator,
            false,
            99999,
            new FindOrderIterator([new FindOrder('createdAt', -1)])
        );
    }
}
