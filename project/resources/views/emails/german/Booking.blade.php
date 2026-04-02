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
            <p>Hi,</p>
            <p>Wir freuen uns, Ihnen mitteilen zu können, dass Sie eine neue Buchung bei Fiesta Catering haben!</p>
            <p>Hier sind die Details Ihrer Buchung:</p>
            <ul>
                <li><strong>Name:</strong> {{ $customer_name }}</li>
                <li><strong>Von Datum:</strong> {{ $from_date }}</li>
                <li><strong>Miteinander ausgehen:</strong> {{ $to_date }}</li>
                <li><strong>Anzahl der Personen:</strong> {{ $no_of_person }}</li>
                <li><strong>Budget pro Person:</strong> {{ $budget_per_person }}</li>
                <li><strong>Gesamtbudget:</strong> {{ $total_budget }}</li>
            </ul>


            <p>Bitte klicken Sie auf den untenstehenden Link, um sich anzumelden</p>
            <a href="https://fiestacatering.de/" style="text-decoration:none;">
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
