# ApiParser
PHP class for using our company API as part of the subscription.

How to install ApiParser library:
1. Put a file named composer.json at the root of your project, containing your project dependencies:
      {
        { "require": { "emailplatform/api_parser": "1.1.*" } 
      }

2. Execute this in your project root.
      php composer.phar install

3. If your packages specify autoloading information, you can autoload all the dependencies by adding this to your code:
      require 'vendor/autoload.php';
