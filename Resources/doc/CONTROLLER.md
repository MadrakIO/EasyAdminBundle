Creating a Controller
=======================

Controller:
```php
<?php

namespace App\Bundle;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use MadrakIO\Bundle\EasyAdminBundle\Controller\AbstractCRUDController;

/**
 * Post controller.
 *
 * @Route("/post/", service="app_bundle.post_controller")
 */
class PostController extends AbstractCRUDController
{
}
```

Service:
```yaml
services:
    app_bundle.post_controller:
        class:     App\Bundle\PostController
        arguments: ['@router', '@doctrine.orm.default_entity_manager', '@app_bundle.post_form', '@app_bundle.post_list', '@app_bundle.post_show', 'App\Bundle\Entity\Post']
        calls:
            - [ setContainer, [ @service_container ]]
```

Routing:
```yaml
post:
    resource: "@AppBundle/Controller/PostController.php"
    type:     annotation
```

