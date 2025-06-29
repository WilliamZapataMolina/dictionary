<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Iniciar sesión</title>
    <link rel="stylesheet" href="thirdparty/bootstrap-5.3.3-dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="css/styles.css" />

</head>

<body>
    <div class="login-container">
        <h2>Iniciar sesión</h2>

        <!-- Puedes usar el envío clásico con action o quitarlo y usar solo JavaScript -->

        <form id="loginForm" method="POST" action="../server/controller/Controller.php?action=login" class="p-4 border rounded bg-light" style="max-width: 400px; margin: auto;">

            <h2 class="mb-4 text-center">Iniciar sesión</h2>

            <div class="mb-3">
                <label for="email" class="form-label">Correo electrónico</label>
                <input id="email" type="email" name="email" class="form-control" placeholder="Correo electrónico" required />
            </div>

            <div class="mb-3">
                <label for="password" class="form-label">Contraseña</label>
                <input id="password" type="password" name="password" class="form-control" placeholder="Contraseña" required />
            </div>

            <div class="form-check mb-3">
                <input id="remember" type="checkbox" name="remember" value="1" class="form-check-input" />
                <label for="remember" class="form-check-label">Recordarme</label>
            </div>

            <input type="submit" value="Entrar" class="btn btn-primary w-100" />
        </form>

    </div>

    <!-- JavaScript para envío opcional con fetch -->
    <script>
        document.getElementById("loginForm").addEventListener("submit", async function(e) {
            e.preventDefault(); // Evita que se envíe de forma tradicional

            const form = e.target;
            const formData = new FormData(form);

            try {
                const response = await fetch(form.action, {
                    method: "POST",
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest' // 👈 necesario para que el servidor sepa que es AJAX
                    },
                    body: formData
                });

                const result = await response.text();


                try {
                    const json = JSON.parse(result);
                    if (json.redirect) {
                        window.location.href = json.redirect;
                    } else {
                        alert("Respuesta inesperada:\n" + result);
                    }
                } catch (e) {
                    alert("Error al iniciar sesión:\n" + result);
                }
            } catch (error) {
                console.error("Error en el login:", error);
                alert("Error al conectar con el servidor.");
            }
        });
    </script>

</body>

</html>