<?php
    require_once(__DIR__ . '/../database/get_from_db.php');

    require_once(__DIR__ . '/../templates/common.tpl.php');
    require_once(__DIR__ . '/../templates/user.tpl.php');
    require_once(__DIR__ . '/../templates/seller.tpl.php');

    require_once(__DIR__ . '/../sessions/session.php');

    if ( !isset($_GET['user']) or !preg_match ("/^[a-zA-Z0-9\s]+$/", $_GET['user'])) {
        header('Location: /index.php');
    }

    $session = new Session();
    $user = getUserbyId($_GET['user']);
    $db = getDatabaseConnection();

    if (isset($user)) {
        $seller = getUserbyId($user->id);

        $products = $seller->getSellingProducts();
    } else {
        header('Location: /index.php');
        exit();
    }
    if ((!$session->isLoggedIn()) or ($session->isLoggedIn() and ($session->getUser()->id !== $user->id)) ) {
        drawHeader($session);
        output_seller_header($db, $user);
        if (sizeof($products) > 0) {
            drawSellerProducts($products);
        }
        drawFooter();
    } else {
        header('Location: /pages/profilePage.php');
        exit();
    }
    
