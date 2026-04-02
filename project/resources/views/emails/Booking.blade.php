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
            <p>We are excited to inform you that you have a new booking with Fiesta Catering!</p>
            <p>Here are the details of your booking:</p>
            <ul>
                <li><strong>Name:</strong> {{ $customer_name }}</li>
                <li><strong>From Date:</strong> {{ $from_date }}</li>
                <li><strong>To Date:</strong> {{ $to_date }}</li>
                <li><strong>Number of Person:</strong> {{ $no_of_person }}</li>
                <li><strong>Budget Per Person:</strong> {{ $budget_per_person }}</li>
                <li><strong>Total Budget:</strong> {{ $total_budget }}</li>
            </ul>


            <p>Please click on below link to login</p>
            <a href="https://fiestacatering.de/" style="text-decoration:none;">
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
