<?php declare(strict_types=1);

namespace VitesseCms\Form\Listeners\Admin;

use Phalcon\Events\Event;
use VitesseCms\Admin\Models\AdminMenu;
use VitesseCms\Admin\Models\AdminMenuNavBarChildren;

class AdminMenuListener
{
    public function AddChildren(Event $event, AdminMenu $adminMenu): void
    {
        $children = new AdminMenuNavBarChildren();
        $children->addChild('Form submissions', 'admin/form/adminsubmission/adminList');
        $adminMenu->addDropdown('Communication', $children);
    }
}
