<div class="wrapper">
    <div class="sidebar">

        <div class="brand mb-4">Bonjour, <span class="text-primary"><?php session()->get('user_id')?></span></div>
        <nav class="nav flex-column">
            <a href="#" class="bi bi-grid-1x2-fill nav-link active">  Tableau</a>
            <a href="#" class="bi bi-grid-3x2-gap-fill nav-link">  Tableur</a>
        </nav>

        <div class="sidebar-footer">
            <div class="d-flex justify-content-between text-center w-100">
                <a class="bi bi-box-arrow-right w-100 text-center" href=<?= site_url('/logout') ?>> Log out</a>
            </div>
        </div>
    </div>
