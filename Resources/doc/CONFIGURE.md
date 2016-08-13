Configuration Options
=======================

```yaml
madrak_io_easy_admin:
    parent_template: 'layout.html.twig'
    grants:
        check: false
        attributes:
            create: 'CREATE'
            show: 'SHOW'
            edit: 'EDIT'
            delete: 'DELETE'
            menu: 'MENU'
```

* ```parent_template```: The template that will be extended. The only requirement for the parent template is that it has a content block that can be overriden.
* ```grants.check```: If this is true, EasyAdminBundle will use isGranted on all objects in CREATE, UPDATE, DELETE, SHOW and LIST. It will also use a special attribute (MENU) if KNP Menu Bundle is installed.
* ```display_ras_alerts```: If this is set to true and RasFlashAlertBundle is installed the AbstractCoreCRUDController will display success and error alerts using RasFlashAlertBundle.

Optional Bundles
=======================

* If ```knplabs/knp-paginator-bundle``` is installed, you can use the paginator for the List View page.
* If ```knplabs/knp-menu-bundle``` is installed, EasyAdminBundle will automatically generate ```madrak_io_easy_admin_crud_menu``` which will link to each of your list pages.
* If ```ras/flash-alert-bundle``` is installed, EasyAdminBundle will add Success and Error messages to various pages (ie when an entity is created/updated).
* If ```whiteoctober/breadcrumbs-bundle``` is installed, you can use the breadcrumbs for the CRUD pages.

Enabling Check Grants
=======================

If you've decided to enable ```grants.check```, there are a few things you should know:

* Make sure your Voter implements ```MadrakIO\Bundle\EasyAdminBundle\Security\EasyAdminVoterInterface``` and supports the attribute constants specified in that class
* Make sure your Voters support both an instance of the object and the object's class for both CREATE and MENU (if you're using KNP Menu Bundle).
