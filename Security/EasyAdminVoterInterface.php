<?php

namespace MadrakIO\Bundle\EasyAdminBundle\Security;

interface EasyAdminVoterInterface
{
    const CREATE = 'CREATE'; // Used by the create action
    const SHOW = 'SHOW'; // Used by the show action and to check items in the list action
    const EDIT = 'EDIT'; // Used by the edit action
    const DELETE = 'DELETE'; // Used by the delete action
    const MENU = 'MENU'; // Used by the menu to determine which items should be shown. PLEASE NOTE that the $subject for the MENU attribute is the class name, not an object
}
