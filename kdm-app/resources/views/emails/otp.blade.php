<x-mail::message>
# Secure Login Verification

You are attempting to log into the KDM Stratus network. Please use the following 6-digit verification code to complete your login. 

This code will expire in 10 minutes.

<div style="background-color: #18191c; border: 1px solid #374151; border-radius: 8px; padding: 20px; text-align: center; margin: 20px 0;">
    <h2 style="color: #60a5fa; font-size: 32px; letter-spacing: 5px; margin: 0;">{{ $otpCode }}</h2>
</div>

If you did not request this login, please change your password immediately.

Thanks,<br>
KDM Security Team
</x-mail::message>