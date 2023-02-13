<?php declare(strict_types=1);

namespace VitesseCms\Form\Repositories;

use VitesseCms\Communication\Repositories\NewsletterRepository;
use VitesseCms\Datagroup\Repositories\DatagroupRepository;
use VitesseCms\Form\Interfaces\RepositoryInterface;
use VitesseCms\Language\Repositories\LanguageRepository;

class RepositoryCollection implements RepositoryInterface
{
    /**
     * @var LanguageRepository
     */
    public $language;

    /**
     * @var DatagroupRepository
     */
    public $datagroup;

    /**
     * @var NewsletterRepository
     */
    public $newsletter;

    public function __construct(
        LanguageRepository $languageRepository,
        DatagroupRepository $datagroupRepository,
        NewsletterRepository $newsletterRepository
    )
    {
        $this->language = $languageRepository;
        $this->datagroup = $datagroupRepository;
        $this->newsletter = $newsletterRepository;
    }
}
