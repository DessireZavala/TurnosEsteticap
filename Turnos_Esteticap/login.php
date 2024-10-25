<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <title>Login</title>
    <link rel="stylesheet" type="text/css" href="styles/styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body class="body-log">
    <div class="cont-princ">
        <section>
            <form method="POST" action="controllers/controladorLogin.php" name="formLogin" id="formLogin" class="form-login">
                <h1>Login</h1>
                <div class="contenedor-controles">
                    <label class="usuario">Usuario</label>
                    <input class="inp" type="text" name="usuario" id="usuario" required placeholder="Ingrese su usuario">
                    <br>
                    <label>Contraseña</label>
                    <input class="inp" type="password" name="password" id="password" required placeholder="Ingrese su contraseña">
                    <br>
                    <input type="submit" class="btn" id="login" value="Login">
                </div>
            </form>
        </section>
    </div>
</body>
</html>
