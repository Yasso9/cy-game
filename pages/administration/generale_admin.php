<?php
    require_once $_SERVER['DOCUMENT_ROOT'] . "/global/global.php";
    InclusionFichier::debut("Administration", false, "page_general.css");
?>

<main>
    <nav>
        <ul class="list-group list-unstyled">
            <a href="utilisateur_admin.php">
                <li class="list-group-item list-group-item-primary text-center">
                    Gestion des Utilisateurs
                </li>
            </a>
            <a href="boutique_admin.php">
                <li class="list-group-item list-group-item-primary text-center">
                    Gestion de la Boutique
                </li>
            </a>
            <a href="evenement_admin.php">
                <li class="list-group-item list-group-item-primary text-center">
                    Gestion des Évenements
                </li>
            </a>
        </ul>
    </nav>
</main>

<?php
    InclusionFichier::fin();
?>