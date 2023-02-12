<?php declare(strict_types=1);

namespace VitesseCms\Form\Controllers;

use VitesseCms\Block\Enum\BlockFormBuilderEnum;
use VitesseCms\Block\Repositories\BlockFormBuilderRepository;
use VitesseCms\Communication\Helpers\NewsletterHelper;
use VitesseCms\Configuration\Enums\ConfigurationEnum;
use VitesseCms\Configuration\Services\ConfigService;
use VitesseCms\Core\AbstractController;
use VitesseCms\Core\AbstractControllerFrontend;
use VitesseCms\Core\Services\FlashService;
use VitesseCms\Core\Utils\DirectoryUtil;
use VitesseCms\Core\Utils\FileUtil;
use VitesseCms\Datagroup\Enums\DatagroupEnum;
use VitesseCms\Datagroup\Repositories\DatagroupRepository;
use VitesseCms\Form\Blocks\FormBuilder;
use VitesseCms\Form\DTO\AfterSubmitDTO;
use VitesseCms\Form\Enums\FormEnum;
use VitesseCms\Form\Enums\TranslationEnum;
use VitesseCms\Form\Factories\SubmissionFactory;
use VitesseCms\Form\Forms\BaseForm;
use VitesseCms\Form\Helpers\SubmissionHelper;
use VitesseCms\Form\Interfaces\RepositoriesInterface;
use VitesseCms\Form\Models\Submission;
use VitesseCms\Form\Utils\FormUtil;
use \stdClass;
use VitesseCms\Language\Enums\LanguageEnum;
use VitesseCms\Language\Repositories\LanguageRepository;

class IndexController extends AbstractControllerFrontend
{
    private BlockFormBuilderRepository $blockFormBuilderRepository;
    private LanguageRepository $languageRepository;
    private DatagroupRepository $datagroupRepository;
    private ConfigService $configService;

    public function onConstruct()
    {
        parent::onConstruct();

        $this->blockFormBuilderRepository = $this->eventsManager->fire(BlockFormBuilderEnum::LISTENER_GET_REPOSITORY->value, new stdClass());
        $this->languageRepository = $this->eventsManager->fire(LanguageEnum::GET_REPOSITORY->value, new stdClass());
        $this->datagroupRepository = $this->eventsManager->fire(DatagroupEnum::GET_REPOSITORY->value, new stdClass());
        $this->configService  = $this->eventsManager->fire(ConfigurationEnum::ATTACH_SERVICE_LISTENER->value, new stdClass());
    }

    public function submitAction(): void
    {
        $hasErrors = true;
        if ($this->request->hasPost('block')) :
            $post = $this->request->getPost();
            $blockFormBuilder = $this->blockFormBuilderRepository->getById($this->request->getPost('block', 'string'));
            if ($blockFormBuilder !== null) :
                if ($blockFormBuilder->isUseRecaptcha() && !FormUtil::hasValidRecaptcha($post)) :
                    $hasErrors = true;
                else :
                    unset($post['password'], $post['password2'], $post['csrf']);
                    $languages = $this->languageRepository->findAll(null, false);
                    while ($languages->valid()):
                        $language = $languages->current();
                        $post['name'][$language->getShortCode()] = date('Y-m-d H:i:s') . ' : ' . $blockFormBuilder->getNameField();
                        $languages->next();
                    endwhile;

                    $datagroup = $this->datagroupRepository->getById($blockFormBuilder->getDatagroup());
                    if ($datagroup !== null):
                        $form = new BaseForm();
                        $datagroup->buildItemForm($form);
                        if ($form->isValid($post)) :
                            $submission = SubmissionFactory::createSubmit();
                            SubmissionHelper::bindFormPost($submission, $post);
                            $submission->save();

                            $this->parseSubmittedFiles($submission);
                            $submission->save();
                            $hasErrors = false;

                            $this->eventsManager->fire(
                                FormEnum::AFTER_SUBMIT->value,
                                new AfterSubmitDTO($submission, $blockFormBuilder)
                            );
                            //$this->parseNewsletters($blockFormBuilder);

                            if ($submission->hasEmail()) :
                                $this->viewService->set('systemEmailToAddress', $submission->getEmail());
                            endif;
                            $this->viewService->set('formData', SubmissionHelper::getHtmlTable($submission));
                            $this->viewService->set('formAdminData', SubmissionHelper::getHtmlAdminTable($submission, true));
                            $this->session->set('blockSubmittedId', $this->request->getPost('block'));

                            if ($blockFormBuilder->hasSystemThankyouByShortCode($this->configuration->getLanguageShort())) :
                                $this->flashService->setSucces($blockFormBuilder->getSystemThankyou());
                            else :
                                $this->flashService->setSucces('');
                            endif;
                        else :
                            foreach ($form->getMessages() as $message) :
                                $this->flashService->setError($message->getMessage());
                            endforeach;
                        endif;
                    endif;
                endif;
            endif;
        endif;

        if ($hasErrors) :
            $this->flashService->setError(TranslationEnum::FORM_NOT_SAVED->name);
        endif;

        $this->redirect($this->request->getServer('HTTP_REFERER'));
    }

    protected function parseSubmittedFiles(Submission $submission): Submission
    {
        if ($this->request->hasFiles() === true) :
            DirectoryUtil::exists($this->configService->getUploadDir(), true);

            foreach ($this->request->getUploadedFiles() as $file) :
                if (!empty($file->getName())) :
                    $name = $submission->getId() . '_' . FileUtil::sanatize($file->getName());
                    if ($file->moveTo($this->configService->getUploadDir() . $name)) :
                        $key = $file->getKey();
                        if (substr_count($key, '.') > 0) :
                            $tmp = explode('.', $key);
                            $valueName = $tmp[0];
                            if (!is_array($submission->$valueName)) :
                                $submission->$valueName = [];
                            endif;
                            $submission->$valueName[$tmp[1]] = $name;
                        else :
                            $submission->$key = $name;
                            $submission->addFieldname($key);
                        endif;
                    else :
                        $this->flashService->setError(TranslationEnum::FORM_FILE_UPLOAD_FAILED->name, [$file->getName()]);
                    endif;
                endif;
            endforeach;
        endif;

        return $submission;
    }

    //TODO move to communication package and listener
    protected function parseNewsletters(FormBuilder $blockFormBuilder): void
    {
        $newsletters = $blockFormBuilder->getNewsletters();
        if ($this->request->hasPost('email')) :
            foreach ($newsletters as $newsletterId) :
                $newsletter = $this->repositories->newsletter->getById($newsletterId);
                if ($newsletter) :
                    NewsletterHelper::addMemberByEmail($newsletter, $this->request->get('email'));
                endif;
            endforeach;
        endif;
    }
}
