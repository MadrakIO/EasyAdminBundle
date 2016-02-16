[![Code Climate](https://codeclimate.com/github/MadrakIO/easy-admin-bundle/badges/gpa.svg)](https://codeclimate.com/github/MadrakIO/easy-admin-bundle)
[![Packagist](https://img.shields.io/packagist/v/MadrakIO/easy-admin-bundle.svg)]()
[![Packagist](https://img.shields.io/packagist/dt/MadrakIO/easy-admin-bundle.svg)]()
[![Packagist](https://img.shields.io/packagist/l/MadrakIO/easy-admin-bundle.svg)]()

Installation
============

Step 1: Download the Bundle
---------------------------

Open a command console, enter your project directory and execute the
following command to download the latest stable version of this bundle:

```bash
$ composer require madrakio/easy-admin-bundle
```

This command requires you to have Composer installed globally, as explained
in the [installation chapter](https://getcomposer.org/doc/00-intro.md)
of the Composer documentation.

Step 2: Enable the Bundle
-------------------------

Then, enable the bundle by adding it to the list of registered bundles
in the `app/AppKernel.php` file of your project:

```php
<?php
// app/AppKernel.php

// ...
class AppKernel extends Kernel
{
    public function registerBundles()
    {
        $bundles = array(
            // ...

            new MadrakIO\Bundle\EasyAdminBundle\MadrakIOEasyAdminBundle(),
        );

        // ...
    }

    // ...
}
```

Step 3: Use the bundle
-------------------------

[Configuration Options](https://github.com/MadrakIO/easy-admin-bundle/blob/master/Resources/doc/CONFIGURE.md)

[Creating a List View](https://github.com/MadrakIO/easy-admin-bundle/blob/master/Resources/doc/LIST.md)

[Creating a Show View](https://github.com/MadrakIO/easy-admin-bundle/blob/master/Resources/doc/SHOW.md)

[Creating a Controller](https://github.com/MadrakIO/easy-admin-bundle/blob/master/Resources/doc/CONTROLLER.md)

[Using the Dashboard Controller](https://github.com/MadrakIO/easy-admin-bundle/blob/master/Resources/doc/DASHBOARD.md)