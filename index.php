<?php
    require_once $_SERVER['DOCUMENT_ROOT'] . "/global/global.php";
    InclusionFichier::debut("Index", false, "accueil.css");
?>

<script type="module">
    
</script>

<main class="d-flex flex-column text-justify">
    <section 
        id="carouselAccueil" 
        class="carousel slide align-self-center" 
        data-ride="carousel">
     
        <ul class="carousel-indicators">
            <li data-target="#carouselAccueil" data-slide-to="0" class="active"></li>
            <li data-target="#carouselAccueil" data-slide-to="1"></li>
            <li data-target="#carouselAccueil" data-slide-to="2"></li>
        </ul>


        <div class="carousel-inner">
            <div class="carousel-item active"  id="carousel1">
                <a href="/pages/boutique/boutique.php"> 
                    <div class="embed-responsive embed-responsive-16by9">
                        <video class="embed-responsive-item" muted>
                            <source 
                                src="/styles/images/videos-accueil/video-boutique.mp4" 
                                type="video/mp4">

                            Désolé, votre navigateur ne supporte pas la vidéo
                        </video>
                    </div>
                </a>
            </div>

            <div class="carousel-item" id="carousel2">
                <a href="/pages/evenement/evenement.php"> 
                    <div class="embed-responsive embed-responsive-16by9">
                        <video class="embed-responsive-item" muted>
                            <source 
                                src="/styles/images/videos-accueil/video-evenement.mp4" 
                                type="video/mp4">
                            Désolé, votre navigateur ne supporte pas la vidéo
                        </video>
                    </div>
                </a>
            </div>

            <div class="carousel-item" id="carousel3">
                <a href="/pages/stream/stream.php"> 
                    <div class="embed-responsive embed-responsive-16by9">
                        <video class="embed-responsive-item" muted>
                            <source 
                                src="/styles/images/videos-accueil/video-stream.mp4" 
                                type="video/mp4">
                            Désolé, votre navigateur ne supporte pas la vidéo
                        </video> 
                    </div>
                </a>
            </div>
        </div>


        <a class="carousel-control-prev" href="#carouselAccueil" data-slide="prev">
            <span class="carousel-control-prev-icon"></span>
            <span class="sr-only">Précédent</span>
        </a>
        <a class="carousel-control-next" href="#carouselAccueil" data-slide="next">
            <span class="carousel-control-next-icon"></span>
            <span class="sr-only">Suivant</span>
        </a>
    </section>

    <section class="element mt-5">
        <article class="shadow-lg bg-dark rounded p-5">
            <h3 class="mb-4"><i class="fas fa-gamepad"></i> Qui sommes nous ?</h3>
            <p> 
                Nous sommes des jeunes étudiants de <strong>Pré-Ing2</strong> passionné de jeu vidéo. 
                Nous avons monté cette association afin de partager notre passion avec les autres 
                étudiants, ce qui nous permettrait à tous de pouvoir 
                profiter d'une évasion du rythme scolaire le temps d'un instant
            </p> 
        </article>  

        <article class="shadow-lg bg-dark rounded p-5">
            <h3 class="mb-4"><i class="far fa-question-circle"></i> Que fait-on ?</h3>
            <p>
                Nous organisons des compétitions <strong>chaque semaine !</strong> Participez à
                l'une d'entre elles et tentez de remportez des récompenses exclusives 
                <strong>CY-GAME !</strong>
            </p> 
        </article>
    </section>


    <section class="element mt-4 mb-5">
        <article class="shadow-lg bg-dark rounded p-5 d-flex flex-column justify-content-around">
            <h3 class="mb-4 align-self-center">Pourquoi devenir membre ?</h3>
            <p class="align-self-center">
                En devenant <strong>membre</strong> vous accederez a des <strong>avantages</strong> 
                divers comme des <strong>réductions</strong> 
                exclusives dans la boutique, et bien d'autres encore ! Rejoignez nous !
            </p> 

            <div class="align-self-center">
                <a href="/pages/utilisateur/connexion.php"  class="btn btn-primary">Connexion</a>
                <a href="/pages/utilisateur/inscription.php"  class="btn btn-primary">Inscription</a>
            </div>
        </article> 

        <article class="p-3">
            <h3 class="mb-4 text-center">Nos dernières vidéos</h3>
            <div class="embed-responsive embed-responsive-16by9">
                <iframe
                    class="embed-responsive-item" 
                    src="https://www.youtube.com/embed/b_JV67bwLvE" 
                    allowfullscreen>
                </iframe>   
            </div>
        </article>
    </section>
</main>

<script type="module">
    document.getElementById("carousel1").querySelector("video").play();

    $('#carouselAccueil').on('slide.bs.carousel', 
        function changementImage (evenement) 
        {
            let video = document.getElementById(evenement.relatedTarget.id).querySelector("video");
            video.pause();
            video.currentTime = 0;
            video.play();
        }
    )
</script>

<?php
    InclusionFichier::fin();
?>