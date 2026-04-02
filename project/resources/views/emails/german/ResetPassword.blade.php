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
            <p>Hi, Bitte bestätigen Sie Ihre E-Mail-Adresse.</p>
            <p>Um die Sicherheit Ihres Kontos zu gewährleisten, müssen wir Ihre E-Mail-Adresse überprüfen. Wir haben
                eine Anfrage zum Zurücksetzen des Passworts für Ihr Konto erhalten. Bitte bestätigen Sie Ihre
                E-Mail-Adresse <a style="color: #F57C00">
                    {{ $email }}.</a>
            </p>
            <p>Bitte klicken Sie auf den folgenden Link, um Ihr Passwort zurückzusetzen</p>
            <a href="https://reset-password.fiestacatering.de/{{ $fp_token }}" style="text-decoration:none;">
                <h3 style="background: #F57C00;width: max-content;padding: 0 10px;color: #fff;border-radius: 4px;">
                    klicken Sie hier
                </h3>
            </a>

            <p>Wenn Sie diese E-Mail versehentlich erhalten haben, löschen Sie sie einfach.</p>
            <p style="font-size:0.9em;">Mit kulinarischen Grüßen,<br />
                <span style="color: #F57C00">Dein Team von Fiesta Catering</span>
            </p>
            <hr style="border:none;border-top:1px solid #eee" />
        </div>
    </div>
</body>

</html>
