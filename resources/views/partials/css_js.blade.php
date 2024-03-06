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
