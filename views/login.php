<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Mini-Framework</title>
</head>
<body>

    <title>
        Mini-Framework
    </title>

    <div>

        <h1>LOGIN</h1>

        <form action="/auth" method="POST">
            <?= csrf_field() ?>
            <input type="text" name="name" value="" autofocus>
            <input type="password" name="password" value="">
            <input type="submit" value="Login">
        </form>

    </div>

</body>
</html>
