<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Product Card</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f0f8ff;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }
        .card {
            background-color: #00bfff;
            border-radius: 10px;
            padding: 20px;
            width: 300px;
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
            text-align: center;
        }
        .card img {
            width: 100%;
            border-radius: 10px;
        }
        .card .details {
            display: flex;
            justify-content: space-between;
            margin: 10px 0;
        }
        .card .details span {
            background-color: white;
            padding: 5px 10px;
            border-radius: 5px;
            font-weight: bold;
        }
        .card .description {
            font-size: 1.5em;
            color: white;
        }
    </style>
</head>
<body>
    <div class="card">
        <img src="./images/pic.jpg" alt="Product Image">
        <div class="details">

        </div>
        <div class="description">
            Are you a fan of having .....
        </div>
    </div>
</body>
</html>