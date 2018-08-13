# RegexReplace Module
[![Build Status](https://scrutinizer-ci.com/g/WildPHP/module-regexreplace/badges/build.png?b=master)](https://scrutinizer-ci.com/g/WildPHP/module-regexreplace/build-status/master)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/WildPHP/module-regexreplace/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/WildPHP/module-regexreplace/?branch=master)
[![Latest Stable Version](https://poser.pugx.org/wildphp/module-regexreplace/v/stable)](https://packagist.org/packages/wildphp/module-regexreplace)
[![Latest Unstable Version](https://poser.pugx.org/wildphp/module-regexreplace/v/unstable)](https://packagist.org/packages/wildphp/module-regexreplace)
[![Total Downloads](https://poser.pugx.org/wildphp/module-regexreplace/downloads)](https://packagist.org/packages/wildphp/module-regexreplace)

Use sed-style (`s/find/replace/`) regular expressions to correct previous messages.

## System Requirements
If your setup can run the main bot, it can run this module as well.

## Installation
To install this module, we will use `composer`:

```composer require wildphp/module-regexreplace```

That will install all required files for the module. In order to activate the module, add the following line to your modules array in `config.neon`:

    - WildPHP\Modules\RegexReplace\RegexReplace

The bot will run the module the next time it is started.

## Usage
The RegexReplace module automatically catches instances of the sed replace pattern (`s/find/replace/`) with optional `g` (global, all occurrences) or `i` (case insensitive) flags

## License
This module is licensed under the MIT license. Please see `LICENSE` to read it.
