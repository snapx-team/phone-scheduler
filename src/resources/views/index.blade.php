<!DOCTYPE html>
<html>
<head>
    <title>Phone Scheduler</title>
    <link href="{{ asset(mix("app.css", 'vendor/phone-scheduler')) }}?v={{config('app.version')}}" rel="stylesheet" type="text/css">
</head>
<body>
<div id="app"></div>
<script src="{{ asset(mix('app.js', 'vendor/phone-scheduler')) }}?v={{config('app.version')}}"></script>
</body>
</html>
