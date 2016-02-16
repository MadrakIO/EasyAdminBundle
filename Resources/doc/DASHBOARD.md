Using the Dashboard Controller
=======================

The Dashboard Controller allows you to easily create an admin landing page that will display each LIST and CREATE buttons for each of your CRUD Controllers

Controller:
```php
<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use MadrakIO\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;

/**
 * Post controller.
 *
 * @Route("/dashboard/", service="appbundle.dashboard_controller")
 */
class DashboardController extends AbstractDashboardController
{
}
```

Service:
```yaml
services:
    appbundle.dashboard_controller:
        parent: madrak_io_easy_admin.dashboard_controller
        class: AppBundle\Controller\DashboardController
```

Routing:
```yaml
post:
    resource: "@AppBundle/Controller/DashboardController.php"
    type:     annotation
```