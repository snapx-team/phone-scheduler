<!DOCTYPE html>
<html>
<head>
    <title>Phone Scheduler</title>
    <link href="{{ secure_asset(mix("app.css", 'vendor/phone-scheduler')) }}?v={{config('phone_scheduler.version')}}" rel="stylesheet" type="text/css">
</head>
<body>
<div id="app"></div>
<script src="{{ secure_asset(mix('app.js', 'vendor/phone-scheduler')) }}?v={{config('phone_scheduler.version')}}"></script>
</body>
</html>
