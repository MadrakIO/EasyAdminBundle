Creating a List
===============

The CrudView List feature is setup similarly to the Symfony Form Component.

Lists/PostList.php:
```php
<?php

namespace AppBundle\Lists;

use MadrakIO\Bundle\EasyAdminBundle\CrudView\AbstractListType;
use MadrakIO\Bundle\EasyAdminBundle\CrudView\FieldType\ButtonFieldType;

class PostList extends AbstractListType
{
    public function build()
    {
        $this->add('title')
             ->add('dateCreated')
             ->add('editAction', ButtonFieldType::class, ['label' => 'Edit', 'route' => ['name' => 'app_bundle_post_edit']]);
    }
}
```

Service:
```yaml
services:
    appbundle.post_list:
        class:  AppBundle\Lists\PostList
        parent: madrak_io_easy_admin.crud_list_type
        arguments: ['AppBundle\Entity\Post']
        calls:
            - [setPaginator, ['@knp_paginator']]
```

The paginator call is optional and is only necessary if you want to automatically include sorting and paging in your list view.

Creating a Filterable List
==========================

The Filterable List should be called similarly to the AbstractListType.

Lists/PostList.php:
```php
<?php

namespace AppBundle\Lists;

use MadrakIO\Bundle\EasyAdminBundle\CrudView\AbstractFilterableListType;
use MadrakIO\Bundle\EasyAdminBundle\CrudView\FieldType\ButtonFieldType;

class PostList extends AbstractFilterableListType
{
    public function build()
    {
        ...
        $this->configureFilters();
    }

    public function configureFilters()
    {
        $this->addFilter('title')
             ->addFilter('dateCreated');
    }
}
```

Configuration to enable list to Export CSV
==========================================

Lists/PostList.php:
```php
<?php

namespace AppBundle\Lists;

use MadrakIO\Bundle\EasyAdminBundle\CrudView\AbstractListType;
use MadrakIO\Bundle\EasyAdminBundle\CrudView\FieldType\ButtonFieldType;

class PostList extends AbstractListType
{
    public function build()
    {
        ...
        $this->configureCsvFields();
    }

    public function configureCsvFields()
    {
        $this->addToCsv('title')
             ->addToCsv('dateCreated');
    }
}
```

