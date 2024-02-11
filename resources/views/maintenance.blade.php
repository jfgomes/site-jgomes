<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Maintenance</title>

    <style id="" media="all">
        /* cyrillic-ext */
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
        }

        #notfound {
            position: relative;
            height: 100vh
        }

        #notfound .notfound {
            position: absolute;
            left: 50%;
            top: 50%;
            -webkit-transform: translate(-50%, -50%);
            -ms-transform: translate(-50%, -50%);
            transform: translate(-50%, -50%)
        }

        .notfound {
            max-width: 520px;
            width: 100%;
            line-height: 1.4;
            text-align: center
        }

        .notfound .notfound-404 {
            position: relative;
            height: 240px
        }

        .notfound .notfound-404 h1 {
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

        .notfound .notfound-404 h1>span {
            text-shadow: -8px 0 0 #fff
        }

        .notfound h3 {
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
            .notfound .notfound-404 {
                height: 200px
            }

            .notfound .notfound-404 h1 {
                font-size: 200px
            }
        }

        @media only screen and (max-width: 480px) {
            .notfound .notfound-404 {
                height: 162px
            }

            .notfound .notfound-404 h1 {
                font-size: 162px;
                height: 150px;
                line-height: 162px
            }

            .notfound h2 {
                font-size: 16px
            }
        }ß
    </style>
    <meta name="robots" content="noindex, follow">
</head>

<body>
<div id="notfound">
    <div class="notfound">
        <h3>🚧 {{ $message }} 🚧</h3><br>
        <img style="width: 100%; height: 100%" src="../images/personal/maintenance.png" alt=""><br><br>
        <h3>We will be back soon!</h3>
    </div>

</div>
</body>

</html>
