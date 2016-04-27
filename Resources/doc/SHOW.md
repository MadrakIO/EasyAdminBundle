Creating a Show
===============

The CrudView Show feature is setup similarly to the Symfony Form Component.

Show/PostShow.php
```php
<?php

namespace AppBundle\Show;

use MadrakIO\Bundle\EasyAdminBundle\CrudView\AbstractShowType;

class PostShow extends AbstractShowType
{
    public function build()
    {
        $this->add('title')
             ->add('dateCreated');
    }
}
```

Service:
```yaml
services:
    appbundle.post_show:
        class:  AppBundle\Show\PostShow
        parent: madrak_io_easy_admin.crud_show_type
```
