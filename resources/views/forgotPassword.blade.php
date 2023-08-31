
<!DOCTYPE html>
<html lang="en">
<body>
<p>Dear {{ $user->name }}</p>
<p>You are receiving this email because we received a password reset request for your account.</p>
<p>Below is your password reset pin.</p>
<h3><strong>{{ $pin }}</strong></h3>
<p>If you did not request a password reset, no further action is required.Thanks</p>
</body>

</html>
