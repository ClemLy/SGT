<!DOCTYPE html>
<html lang="fr">
	<head>
		<meta charset="UTF-8">
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
		<title>Inscription</title>
		<link href="/assets/css/style.css" rel="stylesheet" type="text/css">
	</head>
	<body>
		<div class="container">
			<h2>Inscription</h2>

			<?php if(isset($validation)): ?>
				<div class="alert alert-danger">
					<ul>
						<?php foreach($validation->getErrors() as $error): ?>
							<li><?= esc($error) ?></li>
						<?php endforeach; ?>
					</ul>
				</div>
			<?php endif; ?>

			<form action="<?= site_url('signup/store') ?>" method="post">
				<div class="form-group">
					<label for="nom_user">Nom</label>
					<input type="text" name="nom_user" id="nom_user" value="<?= set_value('nom_user') ?>" required>
				</div>

				<div class="form-group">
					<label for="prenom_user">Prenom</label>
					<input type="text" name="prenom_user" id="prenom_user" value="<?= set_value('prenom_user') ?>" required>
				</div>

				<div class="form-group">
					<label for="email_user">Adresse email</label>
					<input type="email_user" name="email_user" id="email_user" value="<?= set_value('email_user') ?>" required>
				</div>

				<div class="form-group">
					<label for="password">Mot de passe</label>
					<input type="password" name="password" id="password" required>
				</div>

				<div class="form-group">
					<label for="confirmpassword">Confirmer le mot de passe</label>
					<input type="password" name="confirmpassword" id="confirmpassword" required>
				</div>

				<button type="submit">S'inscrire</button>
			</form>
			
			<p>Vous avez déjà un compte ? <a href="<?= site_url('signin') ?>">Connectez-vous ici</a></p>
		</div>
	</body>
</html>