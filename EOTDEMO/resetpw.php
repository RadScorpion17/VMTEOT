<?php
include 'header.php';
require_once "conexion.php";

if (isset($_POST['btn_cambiar'])) {
	$mensaje = null;
	$oldpassword = $_POST['oldpassword'];
	$newpassword = $_POST['newpassword'];
	$confirmar_newpassword = $_POST['confirmar_newpassword'];
	try {
		$select_stmt = $con->prepare('SELECT * FROM users WHERE username=:id');
		$select_stmt->bindParam(':id', $username);
		$select_stmt->execute();
		$row = $select_stmt->fetch(PDO::FETCH_ASSOC);
		extract($row);

		if (strlen($newpassword) >= 8) {
			if ((password_verify($oldpassword, $row['password'])) || password_verify($row['password'], password_hash($oldpassword, PASSWORD_DEFAULT))) {
				if ($newpassword == $confirmar_newpassword) {

					$new_password = password_hash($newpassword, PASSWORD_DEFAULT);

					$stmt = $con->prepare("UPDATE users SET password = :new_password WHERE username=:username");
					$stmt->execute([
						':new_password' => $new_password,
						':username' => $username
					]);

					$mensajecampo = null;
					$mensajeextension = null;
					$mensajecontraseña = null;
					$_SESSION['success'] = '';
					$exitomensaje = 'Contraseña cambiada exitosamente!';
				} else {
					$mensajecampo = 'Los campos de contraseña no coinciden!';
				}
			} else {
				$_SESSION['failed'] = 'La contraseña es incorrecta!';
				$mensajecontraseña = 'La contraseña es incorrecta!';
			}
		} else {
			$_SESSION['failed'] = 'La contraseña debe tener al menos 8 carácteres';
			$mensajeextension = 'La contraseña debe tener al menos 8 caracteres';
		}
	} catch (PDOException $e) {
		echo $e->getMessage();
	}
}

?>
<!DOCTYPE html>
<html>

<head>
	<title>VMT</title>
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/2.2.0/jquery.min.js"></script>
	<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-rbsA2VBKQhggwzxH7pPCaAqO46MgnOM80zW1RWuH61DGLwZJEdK2Kadq2F9CUG65" crossorigin="anonymous">
</head>

<body>
	<div class="vh-100 d-flex justify-content-center align-items-center">
		<div class="container">

			<link rel="StyleSheet" href="newstyles.css" type="text/css">


			<div class="login-wrap">
				<div class="login-html">
					<input id="tab-1" type="radio" name="tab" class="sign-in" checked><label for="tab-1" class="tab">Cambiar contraseña</label>

					<div class="login-form">
						<form method="POST">

							<div class="group">
								<label for="pass" class="label">Contraseña anterior</label>
								<input name="oldpassword" type="password" class="input" data-type="password">
							</div>
							<?php if (isset($mensajecontraseña)) {
								echo '<label class="text-success">' . $mensajecontraseña . '</label>';
							}
							?>

							<div class="group">
								<label for="pass" class="label">Contraseña nueva</label>
								<input name="newpassword" type="password" class="input" data-type="password">
							</div>

							<div class="group">
								<label for="pass" class="label">Vuelva a introducir la contraseña nueva</label>
								<input name="confirmar_newpassword" type="password" class="input" data-type="password">
							</div>

							<?php if (isset($exitomensaje)) {
								echo '<label class="text-success">' . $exitomensaje . '</label>';
							} else if (isset($mensajecampo)) {
								echo '<label class="text-danger">' . $mensaje . '</label>';
							} else if (isset($mensajeextension)) {
								echo '<label class="text-danger">' . $mensajeextension . '</label>';
							}
							?>

							<div class="group">
								<input type="submit" name="btn_cambiar" class="button" value="Cambiar">
							</div>

						</form>

					</div>
				</div>
			</div>
		</div>
	</div>
	</div>
</body>

</html>