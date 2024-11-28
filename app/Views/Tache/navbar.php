<div class="wrapper">
    <div class="sidebar">

        <div class="brand mb-4 mt-4">Bonjour, <span class="text-primary"><?= session()->get('prenom_user') ?></span></div>
        <nav class="nav flex-column">
            <a href="#tableau" class="bi bi-grid-1x2-fill nav-link active" id="viewTableau" onclick="switchView('tableau')">  Tableau</a>
            <a href="#tableur" class="bi bi-grid-3x2-gap-fill nav-link" id="viewTableur" onclick="switchView('tableur')">  Tableur</a>
        </nav>

        <div class="sidebar-footer">
            <div class="d-flex justify-content-between text-center w-100">
                <a class="bi bi-box-arrow-right w-100 text-center link-footer" href=<?= site_url('/logout') ?>> Log out</a>
            </div>
        </div>
    </div>