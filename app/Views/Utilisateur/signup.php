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
					<label for="name">Nom</label>
					<input type="text" name="name" id="name" value="<?= set_value('name') ?>" required>
				</div>

				<div class="form-group">
					<label for="email">Adresse email</label>
					<input type="email" name="email" id="email" value="<?= set_value('email') ?>" required>
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