<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Booking Success</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
    <style>
        /* Basic Reset */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Poppins', sans-serif;
            background-color: #f4f7fc;
            color: #333;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }

        .booking-success {
            background: white;
            padding: 40px;
            border-radius: 10px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 600px;
            text-align: center;
        }

        .booking-success .icon {
            font-size: 50px;
            color: #28a745;
            margin-bottom: 20px;
        }

        .booking-success h1 {
            font-size: 24px;
            font-weight: 600;
            margin-bottom: 10px;
        }

        .booking-success p {
            font-size: 16px;
            color: #666;
            margin-bottom: 30px;
        }

        .details {
            text-align: left;
            margin-bottom: 30px;
        }

        .details h3 {
            font-size: 20px;
            color: #333;
            margin-bottom: 10px;
        }

        .details ul {
            list-style-type: none;
            padding: 0;
        }

        .details ul li {
            font-size: 16px;
            color: #666;
            margin-bottom: 8px;
        }

        .btn {
            background-color: #28a745;
            color: white;
            padding: 12px 20px;
            border-radius: 5px;
            text-decoration: none;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: background-color 0.3s;
        }

        .btn:hover {
            background-color: #218838;
        }

        .btn-secondary {
            background-color: #007bff;
            margin-top: 10px;
        }

        .btn-secondary:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>

    <div class="booking-success">
        <div class="icon">
            <i class="fas fa-check-circle"></i>
        </div>
        <h1>Booking Successful!</h1>
        <p>Your booking has been confirmed.</p>

        <!--<div class="details">
            <h3>Booking Details:</h3>
            <ul>
                <li><strong>Booking ID:</strong> #123456789</li>
                <li><strong>Hotel Name:</strong> Grand Resort</li>
                <li><strong>Check-in:</strong> 15th February 2025</li>
                <li><strong>Check-out:</strong> 20th February 2025</li>
                <li><strong>Guests:</strong> 2 Adults, 1 Child</li>
                <li><strong>Total Cost:</strong> $1,250.00</li>
            </ul>
        </div>-->

        <a href="../../index.php" class="btn">Go to Homepage</a>
        <br>
        <!--<a href="booking-details.html" class="btn btn-secondary">View Booking Details</a>-->
    </div>

</body>
</html>
