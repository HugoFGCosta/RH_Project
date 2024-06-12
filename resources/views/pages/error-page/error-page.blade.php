<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Acesso Negado</title>
    <style>
        body {
            background-color: #333;
            color: #fff;
            font-family: Arial, sans-serif;
            text-align: center;
            padding: 50px;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }

        h1 {
            color: red;
            font-size: 24px;
        }

        button {
            background-color: #007bff;
            color: #fff;
            border-radius: 15px;
            padding: 10px 20px;
            border: solid;
            border-color: #fff;
            cursor: pointer;
        }

        div {
            border: solid;
            border-radius: 15px;
            border-color: red;
            padding: 25px;
        }
    </style>
</head>

<body>
    <div>
        <h1>Acesso Negado</h1>
        <p>Você não tem permissão para acessar esta página.</p>
        <button onclick="window.location.href = '/menu';">Voltar ao Menu</button>
    </div>
</body>

</html>
