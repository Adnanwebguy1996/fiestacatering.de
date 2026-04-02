<!DOCTYPE html>
<html>

<head></head>

<body>
    <div style="font-family: Helvetica,Arial,sans-serif;min-width:1000px;overflow:auto;line-height:2">
        <div style="margin:50px auto;width:70%;padding:20px 0">
            <div style="border-bottom:1px solid #eee">
                <a href="" style="font-size:1.4em;color: #F57C00;text-decoration:none;font-weight:600">Fiesta
                    Catering</a>
            </div>
            <p>Hi, Please verify your email.</p>
            <p>To ensure the security of your account, we need to verify your email address. We have received a
                password reset request for your account please confirm your email address <a style="color: #F57C00">
                    {{ $email }}.</a>
            </p>
            <p>Please click on below link to reset your password</p>
            <a href="https://reset-password.fiestacatering.de/{{ $fp_token }}" style="text-decoration:none;">
                <h3 style="background: #F57C00;width: max-content;padding: 0 10px;color: #fff;border-radius: 4px;">
                    Click here
                </h3>
            </a>
            <p>If you received this email by mistake, simply delete it.</p>
            <p style="font-size:0.9em;">Regards,<br />
                <span style="color: #F57C00">Fiesta Catering</span>
            </p>
            <hr style="border:none;border-top:1px solid #eee" />
        </div>
    </div>
</body>

</html>
