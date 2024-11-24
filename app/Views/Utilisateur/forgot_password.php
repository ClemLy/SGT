<!DOCTYPE html>
<html lang="fr">
	<head>
		<meta charset="UTF-8">
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
		<title>Mot de Passe Oublié</title>
		<link href="/assets/css/style.css" rel="stylesheet" type="text/css">
	</head>

	<body>
		<div class="container">
			<h2>Mot de Passe Oublié</h2>

			<?php if(isset($validation)): ?>
				<div class="alert"><?= $validation->listErrors() ?></div>
			<?php endif; ?>

			<form action="<?= site_url('forgot-password/send-reset-link') ?>" method="post">
				<div class="form-group">
					<label for="email">Email</label>
					<input type="email" name="email" id="email" required>
				</div>
				<button type="submit">Envoyer le lien de réinitialisation</button>
			</form>
		</div>
	</body>
</html>
