# KintMe

A simple module that allows debugging with Kint, an advanced PHP dumper.

## Installation

See general end user documentation for [Installing a module](http://omeka.org/s/docs/user-manual/modules/#installing-modules)

## To Use

Once installed and activated, click the **Configure** button to set the following settings:
- Kint::$enabled_mode: determines what mode Kint will run in.
- Kint::$return: whether to return or echo the output.
- Kint::$depth_limit: the maximum depth to parse. 0 for unlimited. Tweak this to balance performance and verbosity. Default 3.
- Allowed roles: Only users who have one of these roles can see the debug informations. Leave unchecked for no restriction (include anonymous users).
- Enable debug expression: whether to output a predefined expression on each page.
- Debug expression: the expression to pass to the d() function.

## Resources

Explore the [Kint documentation](https://kint-php.github.io/kint/)

## Copyright

Copyright 2025 Christian Morel

Licensed under the Apache License, Version 2.0 (the "License");
you may not use this file except in compliance with the License.
You may obtain a copy of the License at

http://www.apache.org/licenses/LICENSE-2.0

Unless required by applicable law or agreed to in writing, software
distributed under the License is distributed on an "AS IS" BASIS,
WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
See the License for the specific language governing permissions and
limitations under the License.
