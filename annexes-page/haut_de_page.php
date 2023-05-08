<nav class="navbar navbar-expand-lg bg-dark navbar-dark sticky-top p-0 pl-1">
    <a class="navbar-brand" href="<?php echo g_pagePrincipale; ?>">
        <img 
            class="logo"
            src="/styles/images/cygame-logo.svg"
            alt="Logo de CY Game">
    </a>

    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#collapsibleNavbar">
        <span class="navbar-toggler-icon"></span>
    </button>
    
    <div class="collapse navbar-collapse" id="collapsibleNavbar">
        <ul class="navbar-nav ml-auto">
            <li class="nav-item" id="navigation-accueil">
                <a class="nav-link" href="<?php echo g_pagePrincipale; ?>">Accueil</a>
            </li>
            <li class="nav-item" id="navigation-boutique">
                <a class="nav-link" href="/pages/boutique/boutique.php">Boutique</a>
            </li>
            <li class="nav-item" id="navigation-evenement">
                <a class="nav-link" href="/pages/evenement/evenement.php">Événement</a>
            </li>
            <li class="nav-item" id="navigation-stream">
                <a class="nav-link" href="/pages/stream/stream.php">Stream</a>
            </li>

            <?php
                if ($_SESSION[g_utilisateurSession]->verification_role_element("admin"))
                {
                    echo <<<HTML
                    <li class="nav-item dropdown" id="navigation-administration">
                        <a class="nav-link dropdown-toggle" href="#" data-toggle="dropdown">
                            Administration
                        </a>
                    
                        <nav class="dropdown-menu">
                            <a class="dropdown-item" href="/pages/administration/generale_admin.php">Générale</a>
                            <a class="dropdown-item" href="/pages/administration/utilisateur_admin.php">Utilisateurs</a>
                            <a class="dropdown-item" href="/pages/administration/evenement_admin.php">Événements</a>
                            <a class="dropdown-item" href="/pages/administration/boutique_admin.php">Produits</a>
                        </nav>
                    </li>

                    <li class="nav-item" id="navigation-tresorerie">
                        <a class="nav-link" href="/pages/tresorerie/tresorerie.php">Trésorerie</a>
                    </li>
                    HTML;
                }
            ?>

            <!-- A enlever à la fin du projet -->
            <!-- <li><a href="/test.php">Test</a></li> -->
        </ul>
    </div>

    <ul class="navbar-nav text-nowrap mx-3">
    <?php
        $htmlInterfaceUtilisateur = "";
        $pageActuel = htmlspecialchars($_SERVER["PHP_SELF"]);

        if ($_SESSION[g_utilisateurSession]->est_connecte())
        {
            $htmlInterfaceUtilisateur .= <<<HTML
            <li class="nav-item dropdown" id="navigation-utilisateur">
                <a class="nav-link dropdown-toggle" href="#" data-toggle="dropdown">
                    <i class="fas fa-user" aria-hidden="true"></i>
                    {$_SESSION[g_utilisateurSession]->recuperer_donnee("pseudo")}
                </a>

                <nav class="dropdown-menu dropdown-menu-right">
                    <a class="dropdown-item" href="/pages/utilisateur/generale_profil.php">
                        Page Principale
                    </a>
                    <a class="dropdown-item" href="/pages/utilisateur/information_profil.php">
                        <i class="fa fa-info" aria-hidden="true"></i>
                        Information Profil
                    </a>
                    <a class="dropdown-item" href="/pages/utilisateur/modification_profil.php">
                        <i class="fa fa-cog" aria-hidden="true"></i>
                        Modifier Profil
                    </a>
                    <a class="dropdown-item" href="/pages/utilisateur/deconnexion.php">
                        <i class="fa fa-sign-out" aria-hidden="true"></i>
                        Se déconnecter
                    </a>
                </nav>
            </li>
            HTML;
        }
        else
        {
            $htmlInterfaceUtilisateur .= <<<HTML
            <li class="nav-item" id="navigation-connexion">
                <a class="nav-link" href="/pages/utilisateur/connexion.php">
                    <i class="fas fa-user" aria-hidden="true"></i>
                    Se connecter
                </a>
            </li>

            <li class="nav-item" id="navigation-inscription">
                <a class="nav-link" href="/pages/utilisateur/inscription.php">
                    <i class="fa fa-sign-in" aria-hidden="true"></i>
                    S'inscrire
                </a>
            </li>
            HTML;
        }

        echo $htmlInterfaceUtilisateur;
    ?>
    </ul>
</nav>


<script type="module">
    function mettre_navivation_actif(nomNavigation)
    {
        document.getElementById(`navigation-${nomNavigation}`).classList.add("active");
    }

    if (document.title === "Index")
    {
        mettre_navivation_actif("accueil");
    }
    else if (document.title === "Boutique")
    {
        mettre_navivation_actif("boutique");
    }
    else if (document.title === "Événement")
    {
        mettre_navivation_actif("evenement");
    }
    else if (document.title === "Streamer")
    {
        mettre_navivation_actif("stream");
    }
    else if (document.title === "Administration" ||
        document.title === "Admin Utilisateurs" ||
        document.title === "Admin Événements" ||
        document.title === "Admin Boutique")
    {
        mettre_navivation_actif("administration");
    }
    else if (document.title === "Trésorerie")
    {
        mettre_navivation_actif("tresorerie");
    }
    else if (document.title === "Profil" ||
        document.title === "Informations Profil" ||
        document.title === "Modification Profil")
    {
        mettre_navivation_actif("utilisateur");
    }
    else if (document.title === "Connexion")
    {
        mettre_navivation_actif("connexion");
    }
    else if (document.title === "Inscription")
    {
        mettre_navivation_actif("inscription");
    }
</script>