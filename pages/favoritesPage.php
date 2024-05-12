<?php
    declare(strict_types = 1);

    require_once(__DIR__ . '/../sessions/session.php');
    $session = new Session();

    require_once(__DIR__ . '/../database/connection.db.php');

    require_once(__DIR__ . '/../templates/common.tpl.php');
    require_once(__DIR__ . '/../templates/productsprint.tpl.php');

    
    if (!$session->isLoggedIn()) { header('Location: /index.php'); } 

    drawHeader($session);
    $user = $session->getUser();

    $favorites_ids = $user->getFavorites();
    if ($favorites_ids == null) {?>
        <h2 id="no-favs">You don't have favorites yet! </h2>
        <?php }
    else {
        drawFavorites($favorites_ids);
    }
    drawFooter();