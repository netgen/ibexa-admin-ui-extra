Netgen's extra bits for Ibexa CMS Admin UI
==========================================

Netgen's extra Admin UI bits for Ibexa CMS implements an enhanced administration
UI for Ibexa DXP that adds some missing features we loved from eZ Publish Legacy
administration interface.

Installation & license
----------------------

Install the package with `composer require netgen/ibexa-admin-ui-extra`. This
will automatically enable the bundle, but will not enable the new interface in
Ibexa Admin UI. To enable the interface, you need to set the design of
`admin_group` siteaccess group to `ngadmin`, e.g.:

```yaml
ibexa:
    system:
        admin_group:
            design: ngadmin
```

Next, import the routes into your project:

```yaml
netgen_ibexa_admin_ui_extra:
    resource: '@NetgenIbexaAdminUIExtraBundle/Resources/config/routing.yaml'
```

Content URLs by Siteaccess
--------------------------

This package enhances the visibility of Content URLs by Siteaccess. URLs can be viewed in the administration interface under the **URL** tab within the Content view.

The package distinguishes between two types of URLs:

1. **Content Tree URLs**: URLs that reside within the configured Siteaccess Content tree.
2. **External URLs**: URLs that exist outside of the configured Siteaccess Content tree.

By default, the overview of External URLs is hidden. To enable the display of these URLs, set the following parameter in your configuration:

```yaml
netgen_ibexa_admin_ui_extra.show_external_siteaccess_urls: true



Licensed under [GPLv2](LICENSE)
