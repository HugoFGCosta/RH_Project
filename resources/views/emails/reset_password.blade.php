<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <style>
        body {
            font-family: Arial, sans-serif;
        }
        .button {
            background-color: #00008B;
            color: white !important;
            padding: 10px 20px;
            text-align: center;
            text-decoration: none;
            display: inline-block;
            font-size: 14px;
            cursor: pointer;
            border-radius: 5px;
            border: none;
        }
        .container {
            background-color: #f9f9f9;
            padding: 10px;
            border-radius: 10px;
            max-width: 600px;
            margin: 0 auto;
        }
        .header {
            text-align: center;
            font-size: 20px;
            font-weight: bold;
            color: #333;
        }
        .content {
            font-size: 14px;
            color: #333;
            text-align: left;
        }
        .footer {
            font-size: 12px;
            color: #777;
            text-align: left;
            margin-top: 20px;
        }
        .button-container {
            text-align: center;
            margin: 20px 0;
        }
    </style>
</head>
<body>
<div class="container">
    <div class="header">Olá {{ $notifiable->name }}!</div>
    <div class="content">
        <p>Está a receber este e-mail porque recebemos um pedido de redefinição de senha para a sua conta.</p>
        <div class="button-container">
            <a href="{{ $actionUrl }}" class="button">Redefinir Senha</a>
        </div>
        <p>Este link de redefinição de senha expira em 60 minutos.</p>
        <p>Se não solicitou uma redefinição de senha, nenhuma ação adicional é necessária.</p>
        <p>Atenciosamente,<br>Qualitask.</p>
    </div>
    <div class="footer">
        <p>Se estiver a ter problemas ao clicar no botão "Redefinir Senha", copie e cole o URL abaixo no seu navegador web:<br>
            <a href="{{ $actionUrl }}">{{ $actionUrl }}</a></p>
        <p>© 2024 Qualitask. Todos os direitos reservados.</p>
    </div>
</div>
</body>
</html>
