![Webpack mix Logo](https://jgomes.site/images/cs/webpackmix.png)

## Introduction

- Webpack Mix is a tool used in Laravel to simplify asset compilation and versioning, such with JavaScript or CSS in web projects. 

- It's an abstraction layer over Webpack, a module bundler for JavaScript. Laravel Mix streamlines Webpack configuration, allowing developers to easily specify which files to compile, how they should be compiled, and where they should be saved.

- With this tool we can create versions that will avoid the client browsers to use the cached assets. Every new deploy has a new version that forces the browser to create a new cache.

- It does compression and minification

- It compresses all assets in just a single js file and a single css

- It add the version of the result files by adding in the path a parameter called 'id'

- All this actions only happens in production. 

- In the process of cd ( Jenkins ) there´s s step responsible to do this actions by the command 'npm run production'

- In local for dev we keep all the asset seperated and editable

## How it looks like in local env
#### Source code in local env
```
    <head>
        <title>Login</title>

        <!-- CSS + JS
        ================================================== -->
        <!-- CSS -->
        <link rel="stylesheet" href="css/local/private/flashMessages.css">
        <link rel="stylesheet" href="css/local/private/loadingOverlay.css">
        <link rel="stylesheet" href="css/local/private/login.css">
        <link rel="stylesheet" href="css/local/private/general.css">

        <!-- JS -->
        <script src="/js/jquery-3.7.1.js"></script>
        <script src="/js/local/private/serverLessRequests.js"></script>
    </head>
```
- css

![Css assets in local env](https://jgomes.site/images/cs/webpackmix/css-local.png)

- js

![Js assets in local env](https://jgomes.site/images/cs/webpackmix/js-local.png)

## webpack.mix.js file instructions to compile all the assets into a single compressed file css and a single js file
```
const mix = require('laravel-mix');

mix
    .styles([
    'public/css/local/private/flashMessages.css',
    'public/css/local/private/loadingOverlay.css',
    'public/css/local/private/login.css',
    'public/css/local/private/general.css',
], 'public/css/prod/app.css')
    .scripts([
    'public/js/jquery-3.7.1.js',
    'public/js/local/private/serverLessRequests.js',
], 'public/js/prod/app.js')
    .version();
```

- All this assets result in just 2 files: app.css and a app.js

## How it looks like in production env
#### Source code in local env
```
    <head>
        <title>Login</title>

        <!-- CSS + JS
        ================================================== -->
        <script src="/js/prod/app.js?id=9bd46f5c9635c3649fbf84b919a601d8"></script>
        <link rel="stylesheet" href="/css/prod/app.css?id=c831546cef7b1f922f057604b7990238">
    </head>
```
- css

![Css assets in prod env](https://jgomes.site/images/cs/webpackmix/css-prod.png)

- js

![Js assets in prod env](https://jgomes.site/images/cs/webpackmix/js-prod.png)

## In the template
```
@if(app()->environment('prod'))
    <script src="{{ mix('js/prod/app.js') }}"></script>
    <link rel="stylesheet" href="{{ mix('css/prod/app.css') }}">
@else
    <!-- CSS -->
    <link rel="stylesheet" href="css/cookies.css">
    <link rel="stylesheet" href="css/local/private/loadingOverlay.css">
    <link rel="stylesheet" href="css/local/private/flashMessages.css">
    <link rel="stylesheet" href="css/local/private/login.css">
    <link rel="stylesheet" href="css/local/private/general.css">

    <!-- JS -->
    <script src="/js/jquery-3.7.1.js"></script>
    <script src="/js/local/private/serverLessRequests.js"></script>
    <script src="/js/cookies.js"></script>
@endif
```
- In production we just load 2 files: app.css and a app.js
- This 2 files in production has an associated version ( param id ) to avoid browser cache
- In local we load all the js + css assets

## How it runs

- Make sure the file webpack.mix.js is valid and all the asset source paths are correct. Pay attention in the order of the file.
- Run the command 'npm run production'

![Command npm run production](https://jgomes.site/images/cs/webpackmix/cmd.png)

## Demonstration
#### ( Click on the image to watch the video )
[![Demonstration video](https://jgomes.site/images/cs/git-branch-protection-video-thumbnail.jpg)](http://www.youtube.com/watch?v=yzSoEaQDNF8)
