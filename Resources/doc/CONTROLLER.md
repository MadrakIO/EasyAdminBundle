Creating a Controller
=======================

Controller:
```php
<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use MadrakIO\Bundle\EasyAdminBundle\Controller\AbstractCRUDController;

/**
 * Post controller.
 *
 * @Route("/post/", service="appbundle.post_controller")
 */
class PostController extends AbstractCRUDController
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
```

Routing:
```yaml
post:
    resource: "@AppBundle/Controller/PostController.php"
    type:     annotation
    tags:
        -  { name: madrak_io_easy_admin.crud_controller }            
```