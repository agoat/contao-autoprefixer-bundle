# Autoprefixer plugin for Contao 4
___

[![Version](https://img.shields.io/packagist/v/agoat/contao-autoprefixer.svg?style=flat-square)](http://packagist.org/packages/agoat/contao-autoprefixer)
[![License](https://img.shields.io/packagist/l/agoat/contao-autoprefixer.svg?style=flat-square)](http://packagist.org/packages/agoat/contao-autoprefixer)
[![Downloads](https://img.shields.io/packagist/dt/agoat/contao-autoprefixer.svg?style=flat-square)](http://packagist.org/packages/agoat/contao-autoprefixer)

---

## Install
### Contao manager
Search for the package and install it
```bash
agoat/contao-autoprefixer
```

### Managed edition
Add the package
```bash
# Using the composer
composer require agoat/contao-autoprefixer
```
Registration and configuration is done by the manager-plugin automatically.

### Standard edition
Add the package
```bash
# Using the composer
composer require agoat/contao-autoprefixer
```
Register the bundle in the AppKernel
```php
# app/AppKernel.php
class AppKernel
{
    // ...
    public function registerBundles()
    {
        $bundles = [
            // ...
            // after Contao\CoreBundle\ContaoCoreBundle
            new Agoat\AutoPrefixerBundle\AgoatAutoPrefixerBundle(),
        ];
    }
}
```

