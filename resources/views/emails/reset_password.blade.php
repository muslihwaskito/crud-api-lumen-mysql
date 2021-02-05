<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<meta http-equiv="X-UA-Compatible" content="ie=edge">
	<title>Reset Password</title>
</head>
<body>
	<h1>Hai {{ $user->name }}</h1>
	<p>Kamu sedang melakukan permintaan reset password, silahkan konfirmasi melalui <a href="{{ env('URL_APPS').'reset-password?token='.$user->reset_token }}">link ini</a></p>
</body>
</html>