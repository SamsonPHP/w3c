# SamsonPHP html markup W3C validator service

[![Latest Stable Version](https://poser.pugx.org/samsonphp/w3c/v/stable.svg)](https://packagist.org/packages/samsonphp/w3c)
[![Build Status](https://scrutinizer-ci.com/g/SamsonPHP/w3c/badges/build.png?b=master)](https://scrutinizer-ci.com/g/SamsonPHP/w3c/build-status/master)
[![Code Coverage](https://scrutinizer-ci.com/g/samsonphp/w3c/badges/coverage.png?b=master)](https://scrutinizer-ci.com/g/samsonphp/w3c/?branch=master)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/samsonphp/w3c/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/samsonphp/w3c/?branch=master) 
[![Total Downloads](https://poser.pugx.org/samsonphp/w3c/downloads.svg)](https://packagist.org/packages/samsonphp/w3c)
[![Stories in Ready](https://badge.waffle.io/samsonphp/w3c.png?label=ready&title=Ready)](https://waffle.io/samsonphp/w3c)

This module automatically runs [validator.w3c.org](validator.w3c.org) HTTP request and outputs its results
when [SamsonPHP\Resourcer](http://github.com/samsonphp/resourcer) ```resourcer.update``` [event](http://github.com/samsonphp/event) is triggered.

This gives ability for a developer making a HTML markup automatically handle all errors, that significally improves HTML markup quality.
