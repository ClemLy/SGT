<!DOCTYPE html>
<html lang="fr">
	<head>
		<meta charset="UTF-8">
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
		<title>Réinitialisation du Mot de Passe</title>
		<link href="/assets/css/style.css" rel="stylesheet" type="text/css">
	</head>

	<body>
		<div class="container">
			<h2>Réinitialisation du Mot de Passe</h2>

			<form action="<?= site_url('reset-password/update') ?>" method="post">
				<input type="hidden" name="token" value="<?= $token ?>">

				<div class="form-group">
					<label for="password">Nouveau Mot de Passe</label>
					<input type="password" name="password" id="password" required>
				</div>

				<div class="form-group">
					<label for="confirm_password">Confirmer Mot de Passe</label>
					<input type="password" name="confirm_password" id="confirm_password" required>
				</div>
				
				<button type="submit">Réinitialiser Mot de Passe</button>
			</form>
		</div>
	</body>
</html>
