# KintMe

A simple module that allows debugging with Kint, an advanced PHP dumper.

## Installation

See general end user documentation for [Installing a module](http://omeka.org/s/docs/user-manual/modules/#installing-modules)

## To Use

Once installed and activated, click the **Configure** button to set the following settings:
- Kint::$enabled_mode: determines what mode Kint will run in.
- Kint::$return: whether to return or echo the output.
- Kint::$depth_limit: the maximum depth to parse. 0 for unlimited. Tweak this to balance performance and verbosity. Default 7.
- Enable debug expression: whether to output a predefined expression on each page.
- Debug expression: the expression to pass to the d() function.

## Resources

Explore the [Kint documentation](https://kint-php.github.io/kint/)

## Copyright

The Kint module is free to re-use for any purpose according to the MIT license.
