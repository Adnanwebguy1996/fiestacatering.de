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
            <p>We are excited to inform you that you have received a new inquiry on the Fiesta Catering portal.</p>

            <p><strong>Received a new inquiry from this email:</strong> {{ $inquiry->email }}</p>

            <p><strong>Full Name:</strong> {{ $inquiry->full_name }}</p>
            <p><strong>Phone Number:</strong> {{ $inquiry->phno }}</p>
            <p><strong>City:</strong> {{ $inquiry->city_name }}</p>
            <p><strong>Address:</strong> {{ $inquiry->address }}</p>
            <p><strong>Date (From - To):</strong> {{ $inquiry->from_date }} to {{ $inquiry->to_date }}</p>
            <p><strong>No. of Persons:</strong> {{ $inquiry->no_of_person }}</p>
            <p><strong>Budget per Person:</strong> € {{ $inquiry->budget_per_person }}</p>
            <p><strong>Total Budget:</strong> € {{ $inquiry->total_budget }}</p>


            <p><strong>Meals:</strong> {{ $inquiry->meals_english }}</p>
            <p><strong>Diet:</strong> {{ $inquiry->diet_english }}</p>
            <p><strong>State:</strong> {{ $inquiry->state_english }}</p>
            <p><strong>Additional Notes:</strong> {{ $inquiry->notes ?? 'N/A' }}</p>

            <p>If you received this email by mistake, simply delete it.</p>
            <p style="font-size:0.9em;">Regards,<br />
                <span style="color: #F57C00">Fiesta Catering</span>
            </p>

            <hr style="border:none;border-top:1px solid #eee" />
        </div>
    </div>
</body>

</html>
