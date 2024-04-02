![phpunit Logo](https://iconape.com/wp-content/png_logo_vector/phpunit.png)

## Introduction

- PHPUnit is a unit testing framework for the PHP programming language. It provides a structure for writing automated tests 
to ensure that PHP code functions as expected. Unit tests are a common practice in software development, where small units 
of code are tested individually to ensure that each one functions correctly.

- The main function of PHPUnit is to automate the testing process, allowing developers to write tests that can be executed 
repeatedly whenever there is a modification to the code. This helps ensure that changes to the code do not break existing functionalities.

- PHPUnit is primarily used to test PHP classes and methods. It allows you to create tests to verify whether a particular 
class or method produces the expected result with different inputs. This is done by defining test cases, where the inputs 
to be provided to the code and the expected results are specified. PHPUnit then executes these test cases and reports whether 
the code is functioning as expected or if there are any issues.

- In addition to unit tests, PHPUnit this project supports integration tests. 
This allows to test not only individual units of code but also the interaction between different components 
and ensures that changes made to the code do not introduce new bugs.

- In summary, PHPUnit is an essential tool for ensuring the quality of PHP code, automating the testing process, 
and allowing to quickly identify and fix issues in the code. It plays a fundamental role in the development 
of high-quality and reliable software.

## Laravel phpunit implementation
#### Phpunit test structure

![Phpunit test structure](https://jgomes.site/images/cs/phpunit/f3.png)

#### Unit tests ( PWD: tests -> Unit -> ExamplesTest.php )
```
<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;

class ExampleTest extends TestCase
{
    /**
     * A basic test example.
     *
     * @return void
     */
    public function test_example()
    {
        $this->assertTrue(true);
    }
}
```

#### Integration tests ( PWD: tests -> Feature -> ExampleTest.php )
```
<?php

namespace Tests\Feature;

use Tests\TestCase;

class ExampleTest extends TestCase
{
    /**
     * A basic test example.
     *
     * @return void
     */
    public function test_example()
    {
        $response = $this->get('/');
        $response->assertOk();
    }
}
```

## Run tests
php artisan test

![Phpunit run tests](https://jgomes.site/images/cs/phpunit/f1.png)

## Generate report
vendor/bin/phpunit --coverage-html storage/coverage-report/

![Phpunit run tests](https://jgomes.site/images/cs/phpunit/f2.png)

## Case we want to expose the coverage to public
- cd /home/jgomes/my/jgomes/site-jgomes/public ( pwd example )
- ln -s ..storage/coverage-report/ coverage-report

## Requirements to run test and generate report / php.init updates
####
How to check if xdebug module is loaded: 
```
    php -m
```

####
How to install xdebug from mac: 
```
    pecl install xdebug
```

####
How to install xdebug:
```
    apt-get -y install php-xdebug
```

####
How to know where is the php.init file:  
```
    php --ini | grep "Loaded Configuration File"
```

####
Edit php.ini file: nano /opt/homebrew/etc/php/{version}/php.ini
```
    zend_extension="xdebug.so"
    xdebug.mode=coverage
```

## Change the title and breadcrumbs of the report ( Usefully case the project is expose with a proxy reverse )
- For mac: sed -i '' 's|<head>|<head><title>coverage-report</title>|' coverage-report/index.html
- For linux: sed -i "s|<head>|<head><title>coverage-report</title>|" "coverage-report/index.html"

## Demonstration
#### ( Click on the image to watch the video )
[![Demonstration video](https://jgomes.site/images/cs/git-branch-protection-video-thumbnail.jpg)](http://www.youtube.com/watch?v=qu3Etw_2Ksw)
