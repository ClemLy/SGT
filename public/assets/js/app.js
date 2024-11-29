function htmlEntityDecode(str)
{
	const txt     = document.createElement('textarea');
	txt.innerHTML = str;
	return txt.value;
}

// Charge les détails d'une tâche dans le formulaire de modification
function loadTaskData(taskId, titre, description, echeance, etat, categorie)
{
	document.getElementById('task_id').value          = taskId;
	document.getElementById('edit_titre').value       = htmlEntityDecode(titre);
	document.getElementById('edit_description').value = htmlEntityDecode(description);
	document.getElementById('edit_echeance').value    = echeance;
	document.getElementById('edit_etat_tache').value  = etat;
	document.getElementById('edit_categorie').value   = categorie;

	setupCharacterCounter();
}


function setupCharacterCounter()
{
	const descriptionField = document.getElementById('edit_description');
	const charCountDisplay = document.getElementById('charCount');
	const maxLength = 100;

	descriptionField.addEventListener('input', function ()
	{
		const currentLength = descriptionField.value.length;
		charCountDisplay.textContent = `${currentLength}/${maxLength}`;
	});
}



// Formater la date 
function formatDate(dateString, includeTime = true) {
    const days = [
        'Dimanche', 'Lundi', 'Mardi', 'Mercredi', 'Jeudi', 'Vendredi', 'Samedi'
    ];
    const months = [
        'Janvier', 'Février', 'Mars', 'Avril', 'Mai', 'Juin', 
        'Juillet', 'Août', 'Septembre', 'Octobre', 'Novembre', 'Décembre'
    ];

    const date = new Date(dateString);
    const dayName = days[date.getDay()];
    const day = date.getDate();
    const month = months[date.getMonth()];
    const year = date.getFullYear();

    if (includeTime) {
        const hours = date.getHours().toString().padStart(2, '0');
        const minutes = date.getMinutes().toString().padStart(2, '0');
        // const seconds = date.getSeconds().toString().padStart(2, '0');
        return `${dayName} ${day} ${month} ${year} à ${hours}:${minutes}`;
    } else {
        return `${dayName} ${day} ${month} ${year}`;
    }
}



// Charge et affiche les détails et les commentaires d'une tâche dans la popup
function loadTaskDetails(titre, description, echeance, etat, categorie, id_tache)
{
	console.log(titre, description, echeance, etat, categorie);

    document.getElementById('detail_titre').innerText       = htmlEntityDecode(titre);
    document.getElementById('detail_description').innerText = htmlEntityDecode(description);
    document.getElementById('detail_echeance').innerText    = formatDate(echeance, false);
    document.getElementById('detail_etat').innerText        = etat;
    document.getElementById('detail_categorie').innerText   = categorie;
    document.getElementById('comment_task_id').value        = id_tache;

	loadPaginatedComments(id_tache, 1); // Charger les commentaires de la première page

    // Charger les commentaires
    const commentsSection = document.getElementById('commentaires_section');
    commentsSection.innerHTML = ''; // Vider les anciens commentaires

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
                    <div class="mb-3 border p-2 d-flex justify-content-between align-items-center" id="comment-${data.comment.id_commentaire}">
                        <div>
                            <p><strong>${formatDate(data.comment.date_commentaire)}</strong></p>
                            <p>${data.comment.text_commentaire}</p>
                        </div>
						<button class="btn btn-sm btn-danger" onclick="deleteComment(${data.comment.id_commentaire})">
							<i class="bi bi-trash"></i>
                        </button>
                    </div>`;
                commentsSection.insertAdjacentHTML('afterbegin', newCommentHTML); // Insérer en haut
                form.reset(); // Réinitialiser le formulaire
            }
			else
			{
                console.error('Erreur:', data.message);
            }
        })
        .catch(error => {
            console.error('Erreur lors de l\'ajout du commentaire:', error);
        });
    };
}

function deleteComment(commentId) 
{
	fetch(`/comment/delete/${commentId}`, {
		method: 'DELETE'
	})
	.then(response => response.json())
	.then(data => {
		if (data.success)
		{
			const commentElement = document.getElementById(`comment-${commentId}`);
			commentElement.remove();
		}
		else
		{
			console.error('Erreur:', data.message);
		}
	})
	.catch(error => {
		console.error('Erreur lors de la suppression du commentaire:', error);
	});
}


function loadPaginatedComments(id_tache, page)
{
    const commentsSection = document.getElementById('commentaires_section');
    commentsSection.innerHTML = ''; // Vider les anciens commentaires

    fetch(`/comment/getPaginated/${id_tache}?page=${page}&perPage=3`)
        .then(response => response.json())
        .then(data => {
			console.log(data);
            const { comments, pager } = data;
            if (comments.length === 0)
			{
                commentsSection.innerHTML = '<p>Aucun commentaire pour cette tâche.</p>';
            }
			else
			{
                comments.forEach(comment => {
                    const commentHTML = `
                    <div class="mb-3 border p-2 d-flex justify-content-between align-items-center" id="comment-${comment.id_commentaire}">
                        <div>
                            <p><strong>${formatDate(comment.date_commentaire)}</strong></p>
                            <p>${comment.text_commentaire}</p>
                        </div>
                        <button class="btn btn-sm btn-danger" onclick="deleteComment(${comment.id_commentaire})">
                            <i class="bi bi-trash"></i>
                        </button>
                    </div>`;
                    commentsSection.innerHTML += commentHTML;
                });

                // Ajouter les liens de pagination
                const paginationHTML = generatePaginationHTML(pager, id_tache);
                commentsSection.innerHTML += paginationHTML;
            }
        })
        .catch(error => {
            console.error('Erreur lors du chargement des commentaires:', error);
            commentsSection.innerHTML = '<p>Erreur lors du chargement des commentaires.</p>';
        });
}


function generatePaginationHTML(pager, id_tache)
{
	const totalPages = Math.ceil(pager.total / pager.perPage);

    let html = `<div class="pagination">Page ${pager.currentPage} sur ${totalPages}</div>`;

    // Vérifier si une page précédente existe
    if (pager.previous)
	{
        html += `<button onclick="loadPaginatedComments(${id_tache}, ${pager.currentPage - 1})">Précédent</button>`;
    }

    // Vérifier si une page suivante existe
    if (pager.next)
	{
        html += `<button onclick="loadPaginatedComments(${id_tache}, ${pager.currentPage + 1})">Suivant</button>`;
    }

    html += '</div>';
    return html;
}



// Basculer entre tableau et tableur
function switchView(view) {
	// Affiche la vue appropriée
	document.getElementById('tableauView').style.display = view === 'tableau' ? 'block' : 'none';
	document.getElementById('tableurView').style.display = view === 'tableur' ? 'block' : 'none';

	// Met à jour l'état actif des liens de navigation
	document.querySelectorAll('.nav-link').forEach(link => link.classList.remove('active'));
	document.getElementById('view' + (view === 'tableau' ? 'Tableau' : 'Tableur')).classList.add('active');

	// Met à jour l'URL avec le fragment correspondant
	window.location.hash = view;

	// Sauvegarde la vue active dans localStorage
	localStorage.setItem('activeView', view);
}
document.addEventListener('DOMContentLoaded', () => {
	const savedView = localStorage.getItem('activeView') || 'tableau'; // Par défaut : tableau
	switchView(savedView); // Affiche la vue sauvegardée
});

document.addEventListener('DOMContentLoaded', () => {
	const view = window.location.hash.replace('#', '') || 'tableau';
	switchView(view);
});

window.addEventListener('hashchange', () => {
	const view = window.location.hash.replace('#', '') || 'tableau';
	switchView(view);
});



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
	sortTableRows(currentSort.criteria, currentSort.order);
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
				const dateElementA = a.querySelector('.t-date');
				const dateElementB = b.querySelector('.t-date');

				valueA = parseDate(getTextWithoutBefore(dateElementA));
				valueB = parseDate(getTextWithoutBefore(dateElementB));
			}

			if (valueA < valueB) return order === 'asc' ? -1 : 1;
			if (valueA > valueB) return order === 'asc' ? 1 : -1;
			return 0;
		});
		tasks.forEach(task => column.appendChild(task));
	});
}


function updateButtonStates() {
	document.querySelectorAll('button').forEach(button => button.classList.remove('active'));
	if (currentSort.criteria) {
		const button = document.getElementById(`sort${capitalizeFirstLetter(currentSort.criteria)}`);
		button.classList.add('active');
		button.innerText = `${capitalizeFirstLetter(currentSort.criteria)} (${currentSort.order === 'asc' ? '↑' : '↓'})`;
	}
}

function capitalizeFirstLetter(string) {
	return string.charAt(0).toUpperCase() + string.slice(1);
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


function getTextWithoutBefore(element) {
	return element.childNodes[0]?.nodeValue.trim() || '';
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