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
function loadTaskDetails(titre, description, echeance, etat, categorie, id_tache) {
    // Mise à jour des détails de la tâche
    document.getElementById('detail_titre').innerText = titre;
    document.getElementById('detail_description').innerText = description;
    document.getElementById('detail_echeance').innerText = echeance;
    document.getElementById('detail_etat').innerText = etat;
    document.getElementById('detail_categorie').innerText = categorie;
    document.getElementById('comment_task_id').value = id_tache;

    // Charger les commentaires
    const commentsSection = document.getElementById('commentaires_section');
    commentsSection.innerHTML = ''; // Vider les anciens commentaires

    fetch(`/comment/get/${id_tache}`)
        .then(response => response.json())
        .then(data => {
            if (data.length === 0) {
                commentsSection.innerHTML = '<p>Aucun commentaire pour cette tâche.</p>';
            } else {
                data.forEach(comment => {
                    const commentHTML = `
                        <div class="mb-3 border p-2">
                            <p><strong>${comment.date_commentaire}</strong></p>
                            <p>${comment.text_commentaire}</p>
                        </div>`;
                    commentsSection.innerHTML += commentHTML; // Ajouter en bas
                });
            }
        })
        .catch(error => {
            console.error('Erreur lors du chargement des commentaires:', error);
            commentsSection.innerHTML = '<p>Erreur lors du chargement des commentaires.</p>';
        });

    // Gestion de l'envoi du formulaire AJAX
    const form = document.getElementById('addCommentForm');
    form.onsubmit = function (event) {
        event.preventDefault(); // Empêcher le rechargement de la page
        const formData = new FormData(form);

	fetch(`/comment/add`, {
		method: 'POST',
		body: formData
	})
		.then(response => response.json())
		.then(data => {
			if (data.success) {
				// Ajouter le commentaire directement en haut de la liste
				const newCommentHTML = `
					<div class="mb-3 border p-2">
						<p><strong>${data.comment.date_commentaire}</strong></p>
						<p>${data.comment.text_commentaire}</p>
					</div>`;
				commentsSection.insertAdjacentHTML('afterbegin', newCommentHTML); // Insérer en haut
				form.reset(); // Réinitialiser le formulaire
			} else {
				console.error('Erreur:', data.message);
			}
		})
		.catch(error => {
			console.error('Erreur lors de l\'ajout du commentaire:', error);
		});
    };
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

let currentSort = {
	criteria: null,  // Critère de tri actif
	order: 'asc'     // Ordre actuel : 'asc' ou 'desc'
};

function toggleSort(criteria) {
	if (currentSort.criteria === criteria) {
		currentSort.order = currentSort.order === 'asc' ? 'desc' : 'asc';
	} else {
		currentSort.criteria = criteria;
		currentSort.order = 'asc';
	}
	sortTasks(currentSort.criteria, currentSort.order);
	updateButtonStates();
}

function sortTasks(criteria, order) {
	const columns = document.querySelectorAll('.class_taches');
	columns.forEach(column => {
		const tasks = Array.from(column.querySelectorAll('.t-card'));
		tasks.sort((a, b) => {
			let valueA, valueB;

			if (criteria === 'name') {
				valueA = a.querySelector('.t-title').innerText.toLowerCase();
				valueB = b.querySelector('.t-title').innerText.toLowerCase();
			} else if (criteria === 'category') {
				valueA = a.querySelector('.t-category').innerText.toLowerCase();
				valueB = b.querySelector('.t-category').innerText.toLowerCase();
			} else if (criteria === 'date') {
				valueA = parseDate(a.querySelector('.t-date').innerText);
				valueB = parseDate(b.querySelector('.t-date').innerText);
			}

			if (valueA < valueB) return order === 'asc' ? -1 : 1;
			if (valueA > valueB) return order === 'asc' ? 1 : -1;
			return 0;
		});
		tasks.forEach(task => column.appendChild(task));
	});
}

function parseDate(dateStr) {
	const months = {
		janvier: 0, février: 1, mars: 2, avril: 3, mai: 4, juin: 5,
		juillet: 6, août: 7, septembre: 8, octobre: 9, novembre: 10, décembre: 11
	};

	const parts = dateStr.split(' ');
	const day = parseInt(parts[1], 10);
	const month = months[parts[2].toLowerCase()];
	const year = parts.length === 4 ? parseInt(parts[3], 10) : new Date().getFullYear(); // Utilise l'année courante si absente.

	return new Date(year, month, day);
}

function updateButtonStates() {
	document.querySelectorAll('button').forEach(button => button.classList.remove('active'));
	if (currentSort.criteria) {
		const button = document.getElementById(`sort${capitalizeFirstLetter(currentSort.criteria)}`);
		button.classList.add('active');
		button.innerText = `${capitalizeFirstLetter(currentSort.criteria)} (${currentSort.order === 'asc' ? '↑' : '↓'})`;
	}
}

let currentTableSort = { criteria: null, order: 'asc' };
function capitalizeFirstLetter(string) {
	return string.charAt(0).toUpperCase() + string.slice(1);
}

function toggleSortTable(criteria) {
	if (currentTableSort.criteria === criteria) {
		currentTableSort.order = currentTableSort.order === 'asc' ? 'desc' : 'asc';
	} else {
		currentTableSort.criteria = criteria;
		currentTableSort.order = 'asc';
	}

	sortTableRows(currentTableSort.criteria, currentTableSort.order);
}

function sortTableRows(criteria, order) {
	const tableBody = document.querySelector('tbody');
	const rows = Array.from(tableBody.querySelectorAll('tr'));

	rows.sort((a, b) => {
		let valueA, valueB;

		if (criteria === 'name') {
			valueA = a.querySelector('td:nth-child(1)').innerText.toLowerCase();
			valueB = b.querySelector('td:nth-child(1)').innerText.toLowerCase();
		} else if (criteria === 'category') {
			valueA = a.querySelector('td:nth-child(2)').innerText.toLowerCase();
			valueB = b.querySelector('td:nth-child(2)').innerText.toLowerCase();
		} else if (criteria === 'status') {
			valueA = a.querySelector('td:nth-child(3)').innerText.toLowerCase();
			valueB = b.querySelector('td:nth-child(3)').innerText.toLowerCase();
		} else if (criteria === 'date') {
			valueA = parseDate(a.querySelector('td:nth-child(4)').innerText);
			valueB = parseDate(b.querySelector('td:nth-child(4)').innerText);
		}

		if (valueA < valueB) return order === 'asc' ? -1 : 1;
		if (valueA > valueB) return order === 'asc' ? 1 : -1;
		return 0;
	});

	rows.forEach(row => tableBody.appendChild(row));
}
