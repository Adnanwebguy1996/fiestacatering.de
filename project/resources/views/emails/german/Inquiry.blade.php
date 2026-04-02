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
            <p>Wir freuen uns, Ihnen mitteilen zu können, dass Sie eine neue Anfrage auf dem Fiesta-Catering-Portal
                erhalten haben.</p>

            <p><strong>Habe eine neue Anfrage von dieser E-Mail erhalten:</strong> {{ $inquiry->email }}</p>

            <p><strong>Vollständiger Name:</strong> {{ $inquiry->full_name }}</p>
            <p><strong>Telefonnummer:</strong> {{ $inquiry->phno }}</p>
            <p><strong>Stadt:</strong> {{ $inquiry->city_name }}</p>
            <p><strong>Adresse:</strong> {{ $inquiry->address }}</p>
            <p><strong>Datum (von - bis):</strong> {{ $inquiry->from_date }} bis {{ $inquiry->to_date }}</p>
            <p><strong>Anzahl Personen:</strong> {{ $inquiry->no_of_person }}</p>
            <p><strong>Budget pro Person:</strong> € {{ $inquiry->budget_per_person }}</p>
            <p><strong>Gesamtbudget:</strong> € {{ $inquiry->total_budget }}</p>


            <p><strong>Mahlzeiten:</strong> {{ $inquiry->meals_german }}</p>
            <p><strong>Diät:</strong> {{ $inquiry->diet_german }}</p>
            <p><strong>Bundesland:</strong> {{ $inquiry->state_german }}</p>
            <p><strong>Zusätzliche Hinweise:</strong> {{ $inquiry->notes ?? 'N/A' }}</p>

            <p>Wenn Sie diese E-Mail versehentlich erhalten haben, löschen Sie sie einfach.</p>
            <p style="font-size:0.9em;">Mit kulinarischen Grüßen,<br />
                <span style="color: #F57C00">Dein Team von Fiesta Catering</span>
            </p>

            <hr style="border:none;border-top:1px solid #eee" />
        </div>
    </div>
</body>

</html>
