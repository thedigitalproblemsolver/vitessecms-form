<?php

declare(strict_types=1);

namespace VitesseCms\Form;

use Phalcon\Di\DiInterface;
use VitesseCms\Communication\Repositories\NewsletterRepository;
use VitesseCms\Core\AbstractModule;
use VitesseCms\Datagroup\Repositories\DatagroupRepository;
use VitesseCms\Form\Repositories\RepositoryCollection;
use VitesseCms\Language\Models\Language;
use VitesseCms\Language\Repositories\LanguageRepository;

class Module extends AbstractModule
{
    public function registerServices(DiInterface $di, string $string = null)
    {
        parent::registerServices($di, 'Form');
        $di->setShared(
            'repositories',
            new RepositoryCollection(
                new LanguageRepository(Language::class),
                new DatagroupRepository(),
                new NewsletterRepository()
            )
        );
    }
}
