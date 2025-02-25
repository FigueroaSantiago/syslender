<?php
session_start();
if (!isset($_SESSION['user_id']) || !isset($_SESSION['cuentas'])) {
    header("Location: login.php");
    exit();
}

$cuentas = $_SESSION['cuentas'];
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Seleccionar Cuenta</title>
    <link rel="stylesheet" href="../../assets/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        /* Estilos generales */
        body {
            background: linear-gradient(to right, #5271FF, #1F2A5C);
            color: #fff;
            font-family: Arial, sans-serif;
            height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .container {
            max-width: 450px;
            background: #fff;
            padding: 2rem;
            border-radius: 12px;
            box-shadow: 0 6px 12px rgba(0, 0, 0, 0.2);
            text-align: center;
            color: #333;
        }

        h2 {
            font-size: 1.5rem;
            font-weight: bold;
            color: #1F2A5C;
            margin-bottom: 20px;
        }

        .form-group {
            text-align: left;
        }

        .form-control {
            border-radius: 8px;
            padding: 10px;
            font-size: 1rem;
            border: 1px solid #ddd;
        }

        .btn-primary {
            background: #5271FF;
            border: none;
            font-size: 1rem;
            font-weight: bold;
            padding: 12px;
            border-radius: 8px;
            width: 100%;
            transition: all 0.3s ease-in-out;
        }

        .btn-primary:hover {
            background: #4058CC;
        }

        .card {
            background: #F8F9FC;
            border: none;
            border-radius: 12px;
            padding: 1.5rem;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s ease-in-out;
        }

        .card:hover {
            transform: translateY(-5px);
        }

        .icon-container {
            font-size: 2.5rem;
            color: #5271FF;
            margin-bottom: 15px;
        }
    </style>
</head>

<body>

    <div class="container">
        <div class="card">
            <div class="icon-container">
                <i class="fas fa-building"></i>
            </div>
            <h2>Seleccionar Cuenta</h2>
            <form action="asignar_cuenta.php" method="post">
                <div class="form-group">
                    <label><strong>Selecciona una cuenta:</strong></label>
                    <select name="id_cuenta" class="form-control" required>
                        <?php foreach ($cuentas as $cuenta) { ?>
                            <option value="<?= $cuenta['id_cuenta'] ?>"><?= $cuenta['nombre'] ?></option>
                        <?php } ?>
                    </select><br>
                </div>
                <button type="submit" class="btn btn-primary"><i class="fas fa-sign-in-alt"></i> Iniciar con esta Cuenta</button>
            </form>
        </div>
    </div>

</body>
</html>
