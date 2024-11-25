<!DOCTYPE html>
<html lang="fr">
	<head>
		<meta charset="UTF-8">
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
		<title>Modifier le profil</title>
		<link href="/assets/css/style.css" rel="stylesheet" type="text/css">
	</head>
	<body>
		<div class="container">
			<h2>Modifier le profil</h2>
			<form action="<?= site_url('profile/update') ?>" method="post">
				<div class="form-group">
					<label for="name">Nom</label>
					<input type="text" name="name" value="<?= $user['name'] ?>" required>
				</div>

				<div class="form-group">
					<label for="email">Adresse email</label>
					<input type="email" name="email" value="<?= $user['email_user'] ?>" required>
				</div>

				<button type="submit">Mettre à jour</button>
			</form>
		</div>
	</body>
</html>