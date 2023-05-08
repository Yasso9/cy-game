<!DOCTYPE html>
<html lang="fr-FR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- Bootstrap -->
    <!-- jQuery library -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <!-- Popper JS -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
    <!-- <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/2.9.2/umd/popper.min.js"></script> -->
    <!-- Latest compiled JavaScript -->
    <!-- <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script> -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.min.js"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css"></script>
    <!-- Latest compiled and minified CSS -->
    <!-- <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css"> -->

    <!-- Font Awesome -->
    <script src="https://kit.fontawesome.com/541db90648.js" crossorigin="anonymous"></script>

    <link rel="icon" type="image/png" href="/styles/images/cygame-icone.png">

    <?php
        if ($captcha === true)
        {
            echo <<<HTML
            <script src="https://www.google.com/recaptcha/api.js?onload=onloadCallback" async defer></script>
            <script>
                var onloadCallback = function() {
                    grecaptcha.execute();
                };
    
                function setResponse(response) { 
                    document.getElementById('captcha-response').value = response; 
                }
            </script>
            HTML;
        }
    ?>

    <?php
        if(isset($css))
        {
            foreach($css as $fichier)
            {
                echo "<link rel='stylesheet' href='${fichier}'>";
            }
        }
    ?>

    <title><?php echo $titre ?></title>
</head>
<body>