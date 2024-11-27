// Charge les détails d'une tâche dans le formulaire de modification
function loadTaskData(taskId, titre, description, echeance, etat, categorie)
{
	document.getElementById('task_id').value = taskId;
	document.getElementById('edit_titre').value = titre;
	document.getElementById('edit_description').value = description;
	document.getElementById('edit_echeance').value = echeance;
	document.getElementById('edit_etat_tache').value = etat;
	document.getElementById('edit_categorie').value = categorie;
}

// Charge et affiche les détails et les commentaires d'une tâche dans la popup
function loadTaskDetails(titre, description, echeance, etat, categorie, id_tache)
{
	document.getElementById('detail_titre').innerText = titre;
	document.getElementById('detail_description').innerText = description;
	document.getElementById('detail_echeance').innerText = echeance;
	document.getElementById('detail_etat').innerText = etat;
	document.getElementById('detail_categorie').innerText = categorie;
	document.getElementById('comment_task_id').value = id_tache;

	// Récupérer les commentaires pour cette tâche
	fetch(`/comment/get/${id_tache}`)
		.then(response => response.json())
		.then(data => {
			const commentsSection = document.getElementById('commentaires_section');
			if (data.length === 0)
				{
				commentsSection.innerHTML = '<p>Aucun commentaire.</p>';
			}
			else
			{
				data.forEach(comment => {
					commentsSection.innerHTML += `
						<div class="mb-3 border p-2">
							<p><strong>${comment.date_commentaire}</strong></p>
							<p>${comment.text_commentaire}</p>
						</div>
					`;
				});
			}
		})
		.catch(error => {
			console.error("Erreur lors de la récupération des commentaires:", error);
		});

	const form = document.getElementById('addCommentForm');
	form.addEventListener('submit', function(event) {
		event.preventDefault();
		const formData = new FormData(form);

		// Envoi de la requête pour ajouter un commentaire
		fetch(`/comment/add`, {
			method: 'POST',
			body: formData
		})
			.then(response => response.json())
			.then(data => {
				if (data.success) {
					const successMessage = document.createElement('div');
					successMessage.classList.add('alert', 'alert-success');
					successMessage.innerText = 'Commentaire ajouté avec succès !';
					commentsSection.prepend(successMessage);
					form.reset();
					setTimeout(() => successMessage.remove(), 3000);
				} else {
					console.error('Erreur :', data.message);
				}
			})
			.catch(error => console.error("Erreur lors de l'ajout du commentaire :", error));
	});
}

// Basculer entre tableau et tableur
function switchView(view)
{
	document.getElementById('tableauView').style.display = view === 'tableau' ? 'block' : 'none';
	document.getElementById('tableurView').style.display = view === 'tableur' ? 'block' : 'none';

	document.querySelectorAll('.nav-link').forEach(link => link.classList.remove('active'));
	document.getElementById('view' + (view === 'tableau' ? 'Tableau' : 'Tableur')).classList.add('active');
}



// Définir le statut par défaut lors de la création d'une tâche
function setTaskStatus(status)
{
	document.getElementById('etat_tache').value = status;
}

// Gestion du glisser-déposer des tâches
let draggedTask = null;

function drag(event)
{
	draggedTask = event.target;
	const taskId = draggedTask.getAttribute('data-task-id');
	event.dataTransfer.setData("text/plain", taskId);
}

function allowDrop(event)
{
	event.preventDefault();
}

function drop(event, newStatusId)
{
	event.preventDefault();
	const taskId = event.dataTransfer.getData("text/plain");

	if (draggedTask)
	{
		// Déplacer visuellement la tâche dans la nouvelle colonne
		const targetColumn = event.target.closest('.task-status').querySelector('#' + newStatusId);
		if (targetColumn)
		{
			targetColumn.appendChild(draggedTask);
		}

		// Appeler le backend pour mettre à jour le statut
		updateTaskStatus(taskId, newStatusId); // C'est ici que la requête est envoyée
	}
}
function updateTaskStatus(taskId, newStatusId)
{
	fetch(('tasks/updateStatus'), {
		method: 'POST',
		headers: {
			'Content-Type': 'application/x-www-form-urlencoded',
			'X-Requested-With': 'XMLHttpRequest', // AJAX request
			'X-CSRF-Token': '<?= csrf_hash(); ?>'
		},
		body: new URLSearchParams({
			id_tache: taskId,
			etat_tache: newStatusId
		})
	})
		.then(response => response.json())
		.then(data => {
			if (data.success)
			{
				console.log('Tâche mise à jour avec succès');
				if (data.refresh)
				{
					// Recharger la page
					location.reload();
				}
			} 
			else
			{
				console.error('Erreur serveur :', data.message);
			}
		})
		.catch(error => console.error('Erreur AJAX :', error));
}