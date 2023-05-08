<?php
	require_once $_SERVER['DOCUMENT_ROOT'] . "/global/global.php";

	$_SESSION[g_utilisateurSession]->verification_role_page("admin");

	InclusionFichier::debut("Trésorerie", false, "tresorerie.css");
?>

<table class="table table-dark table-hover text-center non-selection mt-3 container-xl">
	
	<?php
		inclure_fichier("/pages/tresorerie/tableau_tresorerie.php");
		$tresorerie = new TableauTresorerie();

		echo $tresorerie->creer_tableau();
	?>
</table>

<?php
	InclusionFichier::fin();
?>



