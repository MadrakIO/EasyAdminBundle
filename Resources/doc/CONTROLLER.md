Creating a Controller
=======================

Controller:
```php
<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use MadrakIO\Bundle\EasyAdminBundle\Controller\AbstractSimpleCRUDController;

/**
 * Post controller.
 *
 * @Route("/post/", service="appbundle.post_controller")
 */
class PostController extends AbstractSimpleCRUDController
{
}
```

Service:
```yaml
services:
    appbundle.post_controller:
        parent: madrak_io_easy_admin.crud_controller
        class: AppBundle\Controller\PostController
        arguments: ['@appbundle.post_form', '@appbundle.post_list', '@appbundle.post_show', 'AppBundle\Entity\Post']
        tags:
            -  { name: madrak_io_easy_admin.crud_controller }
        calls:
            - ['setCrudView', [ 'show', 'AppBundle:Posts:CRUD/show.html.twig' ] ]
            - ['setCrudView', [ 'edit', 'AppBundle:Posts:CRUD/edit.html.twig' ] ]
            - [ setMenuIcon, [ "fa-post" ]]
```

Routing:
```yaml
post:
    resource: "@AppBundle/Controller/PostController.php"
    type:     annotation
```

Additional Notes:
* You can also extend AbstractCoreCRUDController if you want control over which CRUD actions are available
* setCrudView and setMenuIcon are optional in the service config and only exist for greater customization
