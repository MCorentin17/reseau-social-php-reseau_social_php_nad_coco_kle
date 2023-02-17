<?php
include 'connect.env';
include 'header.php'
    ?>
<!doctype html>
<html lang="fr">

<head>
    <meta charset="utf-8">
    <title>ReSoC - Mur</title>
    <meta name="author" content="Julien Falconnet">
    <link rel="stylesheet" href="style.css" />
</head>

<body>
    <div id="wrapper">
        <?php
        $userId = intval($_GET['user_id']);
        ?>

        <aside>
            <?php
            /* Etape 3: récupérer le nom de l'utilisateur*/
            $laQuestionEnSql = "SELECT * FROM users WHERE id= '$userId' ";
            $lesInformations = $mysqli->query($laQuestionEnSql);
            $user = $lesInformations->fetch_assoc();
            ?>

            <img src="user.jpg" alt="Portrait de l'utilisatrice" />

            <section>
                <h3>Présentation</h3>
                <p>Sur cette page vous trouverez tous les message de l'utilisatrice :
                    <?php echo $user["alias"] ?>
                    (n°
                    <?php echo $userId ?>)
                </p>
                <p id="subscribe">
                    <form method="POST" action="wall.php?user_id= <?php echo $userId ?>">
                     <input type="hidden" name="user_id" value="<?php echo $userId ?>">
                     <button type="submit" onclick="subscribe()">S'abonner à <?php echo $user["alias"] ?></button>
                        
                    </form>
                        <script>
                            function subscribe() {
                                var subscribeDiv = document.getElementById("subscribe");
                                subscribeDiv.innerHTML = "Vous êtes abonné(e) à <?php echo $user["alias"] ?>";
                                if (subscribeDiv) {
                                document.getElementById("abonner-bouton").style.display = "none";
                                }
                            }
                            console.log(subscribe())
                        </script>
                </p>
            </section>

            <section>
                <form method="post" action="wall.php?user_id= <?php echo $userId ?>">
                    <dl>
                        <dt><label for='postToSend'>Ecrivez içi</label></dt>
                        <dd><input type='text' name='postToSend'></dd>
                    </dl>
                    <input type='submit'>
                </form>
            </section>

            <?php

            // Etape 1 : vérifier si on est en train d'afficher ou de traiter le formulaire
            if ($_SERVER['REQUEST_METHOD'] == 'POST') {
                // Etape 2: récupérer ce qu'il y a dans le formulaire 
                $new_post = $_POST['postToSend'];
                //Etape 4 : Petite sécurité
                $new_post = $mysqli->real_escape_string($new_post);
                //Etape 5 : construction de la requete
                $lInstructionSql = "INSERT INTO posts 
                                    (id, user_id, content, created, parent_id)
                                    VALUES 
                                    (NULL, '$userId', '$new_post', NOW(), NULL)";
                // Etape 6: exécution de la requete
                $ok = $mysqli->query($lInstructionSql);

                if (!$ok) {
                    echo "Le post a échouée : " . $mysqli->error;
                } else {
                    // Step 2 of Post/Redirect/Get pattern: send HTTP redirect response
                    //it redirect the user to the page state where the form was not send, or a thanks page
                    header("Location: {$_SERVER['REQUEST_URI']}");
                    exit();
                }
            }

            ?>
        </aside>

        <main>
            <?php
            /**
             * Etape 3: récupérer tous les messages de l'utilisatrice
             */
            $laQuestionEnSql = "SELECT posts.content,
            posts.created,
            users.alias as author_name,
            users.id as author_id,
            COUNT(likes.id) as like_number,
            GROUP_CONCAT(DISTINCT tags.label ORDER BY tags.id) AS taglist,
            GROUP_CONCAT(DISTINCT tags.id ORDER BY tags.id) AS tagidlist
        FROM posts
        JOIN users ON users.id=posts.user_id
        LEFT JOIN posts_tags ON posts.id = posts_tags.post_id
        LEFT JOIN tags ON posts_tags.tag_id  = tags.id
        LEFT JOIN likes ON likes.post_id  = posts.id
        WHERE posts.user_id='$userId' 
        GROUP BY posts.id
        ORDER BY posts.created DESC;
        ";
                    $lesInformations = $mysqli->query($laQuestionEnSql);
                    if (!$lesInformations) {
                echo ("Échec de la requete : " . $mysqli-> error);
            }

            /**
             * Etape 4: @todo Parcourir les messsages et remplir correctement le HTML avec les bonnes valeurs php
             */
            while ($post = $lesInformations->fetch_assoc()) {

                // echo "<pre>" . print_r($post, 1) . "</pre>";
                ?>
            <?php require("post.php")?>

            <?php } ?>
        </main>
    </div>            
</body>
</html>