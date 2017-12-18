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

        <h1>MINI FRAMEWORK</h1>

        <?php
            if ( isset($name) && isset($password) ) {
                echo "<p>Usuario: $name </p>
                     <p>Password: $password </p>";
            } else {
                echo "<a href='/login'>Login</a>";
            }
        ?>

    </div>

</body>
</html>
