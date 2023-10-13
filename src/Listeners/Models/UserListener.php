<?php

declare(strict_types=1);

namespace VitesseCms\Form\Listeners\Models;

use ArrayIterator;
use Phalcon\Events\Event;
use VitesseCms\Database\Models\FindValue;
use VitesseCms\Database\Models\FindValueIterator;
use VitesseCms\Form\Repositories\SubmissionRepository;
use VitesseCms\Log\Services\LogService;
use VitesseCms\User\Models\User;

class UserListener
{
    public function __construct(
        private readonly LogService $logService,
        private readonly SubmissionRepository $submissionRepository
    ) {
    }

    public function beforeDelete(Event $event, User $user): bool
    {
        $this->performDeletion(
            $this->submissionRepository->findAll(
                new FindValueIterator([new FindValue('user._id', $user->getId())]),
                false
            ),
            'Submission by UserId',
            'Submissions by UserId'
        );

        $this->performDeletion(
            $this->submissionRepository->findAll(
                new FindValueIterator([new FindValue('email', $user->getString('email'))]),
                false
            ),
            'Submission by Email',
            'Submissions by Email'
        );

        return true;
    }

    private function performDeletion(ArrayIterator $models, string $type, string $types): void
    {
        if ($models->count() > 0) {
            $this->logService->message('Start deleting ' . $types);
            while ($models->valid()) {
                if ($models->current()->delete()) {
                    $this->logService->message('Deleted a ' . $type);
                } else {
                    $this->logService->message('Failed to delete a ' . $type);
                }

                $models->next();
            }
            $this->logService->message('Finished deleting ' . $types);
        } else {
            $this->logService->message('No ' . $types . ' found to delete');
        }
    }
}