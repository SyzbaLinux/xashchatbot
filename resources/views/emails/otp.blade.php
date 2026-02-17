<x-mail::message>
# Your ErrandRunner Verification Code

Hello,

Thank you for using ErrandRunner. Here is your One-Time Password (OTP) for verification.

<x-mail::panel>
## {{ $otp }}
</x-mail::panel>

**This code will expire in 10 minutes.**

**Security Note:** Never share this code with anyone. ErrandRunner staff will never ask for your OTP.

If you didn't request this code, please ignore this message. Your account remains secure.

---

Best regards,<br>
{{ config('app.name') }} Team

<x-slot:footer>
<x-mail::footer>
© {{ date('Y') }} {{ config('app.name') }}. All rights reserved.
</x-slot:footer>
</x-mail::slot>
</x-mail::message>
