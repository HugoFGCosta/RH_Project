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
            padding: 5px 15px;
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
            margin: 10px 0;
        }

        .nota {
            color: red;
        }

        .duvida {
            font-weight: bold;
        }
    </style>

    <div class="container">


        <div class="header">

            <head>
                <title>A sua conta foi registada, seja bem-vindo a equipa do Hospital de Tamega e Sousa</title>
            </head>
        </div>

        <div class="content">

<body>
    <p>Caro(a) {{ $name }},</p>
    <p>Sua conta está agora disponível para utilização com este e-mail.</p>
    <p>Para proceder à ativação da sua conta, por favor, siga os passos abaixo:</p>
    <div class="button-container">
        <p>1° - Aceder à nossa plataforma.
            <a href="http://projetorh-env.eba-ty2tcx33.eu-west-3.elasticbeanstalk.com/" class="button">Clique Aqui</a>
        </p>
    </div>
    <p>2° - Na página de login, clique em "Recuperar Palavra-Passe".</p>
    <p>3° - Insira o seu endereço de e-mail registado.</p>
    <p>4° - Ira receber um e-mail com um link para redefinir a sua palavra-passe. Por favor, clique no link fornecido.
    </p>
    <p>5° - Atribua a uma nova palavra-passe e insira novamente para confirmação.</p>
    <p class="nota">Nota: A palavra-passe deve conter pelo menos 1 letra maiuscula, 1 número e caracter especial.</p>

    <p class="duvida">Se tiver alguma dúvida ou estiver enfrentar problemas no registo, favor contacte um administrador.
    </p>
    <p>Atenciosamente,</p>
    <p>Equipa Hospital de Tâmega e Sousa.</p>


</body>
</div>

</div>

</html>
