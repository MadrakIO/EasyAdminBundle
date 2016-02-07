Creating a List
===============

The CrudView List feature is setup similarly to the Symfony Form Component.

Lists/PostList.php:
```php
<?php

namespace App\Bundle\Lists;

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
    app_bundle.post_list:
        class:  App\Bundle\Lists\PostList     
        parent: madrak_io_easy_admin.crud_type        
        arguments: ['@templating', '@doctrine.orm.default_entity_manager', '@madrak_io_easy_admin.field_type_guesser', 'App\Bundle\Entity\Post']
        calls:
            - [setPaginator, ['@knp_paginator']]
```

The paginator call is optional and is only necessary if you want to automatically include sorting and paging in your list view.