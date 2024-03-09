![Custom error pages Logo](https://jgomes.site/images/cs/custom-error-pages.jpg)

## Introduction

- The goal of this case study is to configure different custom error pages according the http status code.

## How to configure the customize error views
#### Add/change the render function in the file ' app -> Exceptions -> Handles.php ' like this:
```
<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Http\Exceptions\ThrottleRequestsException;
use Throwable;

class Handler extends ExceptionHandler
{
    .
    .
    .
    .
    
    public function render($request, Throwable $exception)
    {
        if ($this->isHttpException($exception))
        {
            if ($exception->getStatusCode() == 404)
            {
                return response()->view('errors.404', [], 404);
            }
            if ($exception->getStatusCode() == 500)
            {
                return response()->view('errors.500', [], 500);
            }
            if ($exception->getStatusCode() == 429)
            {
                return response()->view('errors.429', [], 429);
            }
        }

        return parent::render($request, $exception);
    }
}

```
- Create a dir called ' errors ' inside the dir ' resources -> views ' like this:

  ![Custom error templates dir](https://jgomes.site/images/cs/custom_error_pages/templates_dir.png)

#### Example of the error templates:
- 500

![Error template 500](https://jgomes.site/images/cs/custom_error_pages/error-500.png)

- 404

![Error template 404](https://jgomes.site/images/cs/custom_error_pages/error-404.png)

- 429

![Error template 429](https://jgomes.site/images/cs/custom_error_pages/error-429.png)

#### Example of the CSS used in the example templates above:
```
@font-face {
    font-family: 'Montserrat';
    font-style: normal;
    font-weight: 900;
    font-display: swap;
    src: url(/fonts.gstatic.com/s/montserrat/v25/JTUHjIg1_i6t8kCHKm4532VJOt5-QNFgpCvC73w0aXpsog.woff2) format('woff2');
    unicode-range: U+0460-052F, U+1C80-1C88, U+20B4, U+2DE0-2DFF, U+A640-A69F, U+FE2E-FE2F;
}

* {
    -webkit-box-sizing: border-box;
    box-sizing: border-box
}

body {
    background-image: url('../images/header-background.jpg');
    background-size: cover;
    background-position: center;
    background-repeat: no-repeat;
    margin: 0;
    overflow-x: hidden;
    overflow-y: hidden;
}

button {
    cursor: pointer;
}

#error {
    position: relative;
    height: 100vh
}

#error .error {
    position: absolute;
    left: 50%;
    top: 50%;
    -webkit-transform: translate(-50%, -50%);
    -ms-transform: translate(-50%, -50%);
    transform: translate(-50%, -50%)
}

.error {
    max-width: 520px;
    width: 100%;
    line-height: 1.4;
    text-align: center
}

.error .error-status-code {
    position: relative;
    height: 240px
}

.error .error-status-code h1 {
    font-family: montserrat, sans-serif;
    position: absolute;
    left: 50%;
    top: 50%;
    -webkit-transform: translate(-50%, -50%);
    -ms-transform: translate(-50%, -50%);
    transform: translate(-50%, -50%);
    font-size: 252px;
    font-weight: 900;
    margin: 0;
    color: #262626;
    text-transform: uppercase;
    letter-spacing: -40px;
    margin-left: -20px
}

.error .error-status-code h1>span {
    text-shadow: -8px 0 0 #fff
}

.error h3 {
    font-family: cabin, sans-serif;
    position: relative;
    font-size: 16px;
    font-weight: 700;
    text-transform: uppercase;
    color: #aaaaaa;
    margin: 0;
    letter-spacing: 3px;
    padding-left: 6px
}

.notfound h2 {
    font-family: cabin, sans-serif;
    font-size: 20px;
    font-weight: 400;
    color: #ffffff;
    margin-top: 0;
    margin-bottom: 25px
}

@media only screen and (max-width: 767px) {
    .error .error-status-code {
        height: 200px
    }

    .notfound .error-status-code h1 {
        font-size: 200px
    }
}

@media only screen and (max-width: 480px) {
    .error .error-status-code {
        height: 162px
    }

    .error .error-status-code h1 {
        font-size: 162px;
        height: 150px;
        line-height: 162px
    }

    .error h2 {
        font-size: 16px
    }
}
```

## Demonstration
#### ( Click on the image to watch the demo video )
[![Demonstration video](https://jgomes.site/images/cs/git-branch-protection-video-thumbnail.jpg)](http://www.youtube.com/watch?v=1a8Jm8vdGbo)
