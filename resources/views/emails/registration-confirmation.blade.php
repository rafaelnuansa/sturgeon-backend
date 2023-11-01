<!-- resources/views/emails/registration-confirmation.blade.php -->

<p>Hello {{ $user->name }},</p>
<p>Thank you for registering with our site. Please click the link below to confirm your email address:</p>
<a href="{{ route('verification.verify', $user->id) }}">Verify Email</a>
