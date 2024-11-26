<div class="container">
	<h2>Mot de Passe Oublié</h2>

	<?php if(isset($validation)): ?>
		<div class="alert"><?= $validation->listErrors() ?></div>
	<?php endif; ?>

	<form action="<?= site_url('forgot-password/send-reset-link') ?>" method="post">
		<div class="form-group">
			<label for="email_user">Email</label>
			<input type="email_user" name="email_user" id="email_user" required>
		</div>
		<button type="submit">Envoyer le lien de réinitialisation</button>
	</form>
</div>