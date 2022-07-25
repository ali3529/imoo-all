@php
$config = [
    'appName' => config('app.name'),
    'locale' => $locale = app()->getLocale(),
    'locales' => config('app.locales'),
    'githubAuth' => config('services.github.client_id'),
    'appleAuth' => config('services.apple.client_id'),
    'facebookAuth' => config('services.facebook.client_id'),
    'googleAuth' => config('services.google.client_id'),
    'twitterAuth' => config('services.twitter.client_id'),
    'baseUrl' => config('app.apis'),
    'googleApiKey' => env('GOOGLE_API_KEY'),
    'recaptchaKey' => config('services.recaptcha.key'),
    'recaptchaSecret' => config('services.recaptcha.secret'),
];
$appJs = mix('dist/js/app.js');
$appCss = mix('dist/css/app.css')
@endphp
<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

  <title>{{ config('app.name') }}</title>

  <link rel="stylesheet" href="{{ (str_starts_with($appCss, '//') ? 'http:' : '').$appCss }}">
</head>
<body>
  <div id="app"></div>

  <script>
    window.config = @json($config);
  </script>

  <script src="{{ (str_starts_with($appJs, '//') ? 'http:' : '').$appJs }}"></script>
</body>
</html>
