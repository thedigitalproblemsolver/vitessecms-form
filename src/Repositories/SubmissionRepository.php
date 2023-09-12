<?php declare(strict_types=1);

namespace VitesseCms\Form\Repositories;

use VitesseCms\Database\Models\FindOrder;
use VitesseCms\Database\Models\FindOrderIterator;
use VitesseCms\Database\Models\FindValueIterator;
use VitesseCms\Form\Models\Submission;
use VitesseCms\Form\Models\SubmissionIterator;

class SubmissionRepository
{
    public function getById(string $id, bool $hideUnpublished = true): ?Submission
    {
        Submission::setFindPublished($hideUnpublished);

        /** @var Submission $submission */
        $submission = Submission::findById($id);
        if (is_object($submission)):
            return $submission;
        endif;

        return null;
    }

    public function findAll(
        ?FindValueIterator $findValues = null,
        bool $hideUnpublished = true,
        ?int $limit = null,
        ?FindOrderIterator $findOrders = null
    ): SubmissionIterator
    {
        Submission::setFindPublished($hideUnpublished);
        if ($limit !== null) :
            Submission::setFindLimit($limit);
        endif;
        if ($findOrders === null):
            $findOrders = new FindOrderIterator([new FindOrder('name', 1)]);
        endif;

        $this->parseFindValues($findValues);
        $this->parseFindOrders($findOrders);

        return new SubmissionIterator(Submission::findAll());
    }

    protected function parseFindValues(?FindValueIterator $findValues = null): void
    {
        if ($findValues !== null) :
            while ($findValues->valid()) :
                $findValue = $findValues->current();
                Submission::setFindValue(
                    $findValue->getKey(),
                    $findValue->getValue(),
                    $findValue->getType()
                );
                $findValues->next();
            endwhile;
        endif;
    }

    protected function parseFindOrders(?FindOrderIterator $findOrders = null): void
    {
        if ($findOrders !== null) :
            while ($findOrders->valid()) :
                $findOrder = $findOrders->current();
                Submission::addFindOrder(
                    $findOrder->getKey(),
                    $findOrder->getOrder()
                );
                $findOrders->next();
            endwhile;
        endif;
    }
}