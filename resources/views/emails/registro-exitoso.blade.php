<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro completado</title>
</head>
<body style="font-family: Arial, sans-serif; color: #1f2937; line-height: 1.6;">
    <h2>¡Registro completado con éxito!</h2>

    <p>Hola {{ $user->name }},</p>

    <p>Te confirmamos que tu cuenta ha sido registrada correctamente en StoryBook.</p>

    <p>Ya puedes iniciar sesión con tu correo: <strong>{{ $user->email }}</strong>.</p>

    <p>Gracias por registrarte.</p>
</body>
</html>
