# Autoprefixer plugin for Contao 4

[![Version](https://img.shields.io/packagist/v/agoat/contao-autoprefixer.svg?style=flat-square)](http://packagist.org/packages/agoat/contao-autoprefixer)
[![License](https://img.shields.io/packagist/l/agoat/contao-autoprefixer.svg?style=flat-square)](http://packagist.org/packages/agoat/contao-autoprefixer)
[![Downloads](https://img.shields.io/packagist/dt/agoat/contao-autoprefixer.svg?style=flat-square)](http://packagist.org/packages/agoat/contao-autoprefixer)

#### Now compatible with Contao 4.6 !!

## About
Forget about vendor prefixes. Just write pure (S)CSS. This plugin adds vendor prefixes (depending on your settings) to CSS rules using values from [Can I Use].

Write your CSS rules without vendor prefixes:
```css
:fullscreen a {
    display: flex
}
```

Autoprefixer will use the data based on current browser popularity and property
support to apply prefixes for you. You can try the [interactive demo]
of Autoprefixer.

```css
:-webkit-full-screen a {
    display: -webkit-box;
    display: flex
}
:-moz-full-screen a {
    display: flex
}
:-ms-fullscreen a {
    display: -ms-flexbox;
    display: flex
}
:fullscreen a {
    display: -webkit-box;
    display: -ms-flexbox;
    display: flex
}
```

Autoprefixer uses [Browserslist], so you can specify the browsers
you want to target in your project by queries like `last 2 versions`
or `> 5%`.

See [Browserslist docs] for queries, browser names, config format,
and default value.

Or visit the [Autoprefixer] project page for more informations.

[Can I Use]: http://caniuse.com/
[interactive demo]: http://autoprefixer.github.io/
[Browserslist]: https://github.com/ai/browserslist
[Browserslist docs]: https://github.com/ai/browserslist#queries
[Autoprefixer]: https://github.com/postcss/autoprefixer

## Requirements
The autoprefixer tool used by this plugin is written in Javascript and called via node.
Therefore, `Node.js` has to be installed on the server.

Visit https://nodejs.org to see how to installing Node.js.

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

