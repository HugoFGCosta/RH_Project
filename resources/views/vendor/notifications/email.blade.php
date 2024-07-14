<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <style>
        .button {
            background-color: #4CAF50; /* Green */
            border: none;
            color: white;
            padding: 15px 32px;
            text-align: center;
            text-decoration: none;
            display: inline-block;
            font-size: 16px;
            margin: 4px 2px;
            cursor: pointer;
        }
        .container {
            background-color: #f9f9f9;
            padding: 20px;
            border-radius: 10px;
        }
        .header {
            text-align: center;
            font-size: 24px;
            font-weight: bold;
            color: #333;
        }
        .content {
            font-size: 16px;
            color: #333;
        }
    </style>
</head>
<body>
<div class="container">
    <div class="header">Olá {{ $notifiable->name }}!</div>
    <div class="content">
        <p>Está a receber este e-mail porque recebemos um pedido de redefinição de senha para a sua conta.</p>
        <p style="text-align: center;">
            <a href="{{ $actionUrl }}" class="button">Redefinir Senha</a>
        </p>
        <p>Este link de redefinição de senha expira em 60 minutos.</p>
        <p>Se não solicitou uma redefinição de senha, nenhuma ação adicional é necessária.</p>
        <p>Atenciosamente,<br>{{ config('app.name') }}</p>
    </div>
</div>
</body>
</html>
