<?php
    require_once $_SERVER['DOCUMENT_ROOT'] . "/global/global.php";
    InclusionFichier::debut("Profil", false, "page_general.css");
?>

<main>
    <nav>
        <ul class="list-group list-unstyled">
            <a href="information_profil.php">
                <li class="list-group-item list-group-item-info text-center">
                    <i class="fa fa-info" aria-hidden="true"></i>
                    Information Profil
                </li>
            </a>
            <a href="modification_profil.php">
                <li class="list-group-item list-group-item-warning text-center">
                    <i class="fa fa-cog" aria-hidden="true"></i>
                    Modifier Profil
                </li>
            </a>
            <a href="deconnexion.php">
                <li class="list-group-item list-group-item-danger text-center">
                    <i class="fa fa-sign-out" aria-hidden="true"></i>
                    Se déconnecter
                </li>
            </a>
        </ul>
    </nav>
</main>

<?php
    InclusionFichier::fin();
?>