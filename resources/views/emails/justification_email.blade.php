<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $subject }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
        }
        .container {
            margin: 20px auto;
            padding: 20px;
            border: 1px solid #dddddd;
            border-radius: 10px;
            max-width: 600px;
            background-color: #f9f9f9;
        }
        .header {
            background-color: #00008B;
            color: #ffffff;
            padding: 10px;
            font-size: 20px;
            text-align: center;
            border-radius: 10px 10px 0 0;
        }
        .content {
            font-size: 14px;
            color: #333;
            text-align: left;
            padding: 20px;
        }
        .footer {
            font-size: 0.9em;
            color: #777;
            text-align: center;
            margin-top: 20px;
        }
    </style>
</head>
<body>

<header>
</header>

<div class="container">
    <div class="header">
        <h2>{{ $subject }}</h2>
    </div>
    <div class="content">
        {!! $mailMessage !!}
    </div>
    <div class="footer">
        <p>Hospital Tâmega e Sousa</p>
    </div>
</div>
</body>
</html>
