<?php
  declare(strict_types = 1);

  require_once(__DIR__ . '/../sessions/session.php');
  $session = new Session();
  
  require_once(__DIR__ . '/../database/connection_to_db.php');
  require_once(__DIR__ . '/../database/get_from_db.php');
  require_once(__DIR__ . '/../database/userClass.php');


  require_once(__DIR__ . '/../templates/common_tmpl.php');
  require_once(__DIR__ . '/../templates/index_tmpl.php');


  $db = getDatabaseConnection();
  $categories = getCategories($db);

  drawHeader($session);

  drawSearchbar($categories);
  if ($session->isLoggedIn()) 
  {
    $user = $session->getUser();
    $recent_ids = $user->getRecent();
    $favourites_ids = $user->getFavorites();

    if (sizeof($recent_ids) > 0) {drawRecent($recent_ids); }
    if (sizeof($favourites_ids) > 0) {drawFavorites($favourites_ids); }

    $recommended_ids = getRecommended();
  }else{
    $recommended_ids = getRecommended();
  }
  drawRecommended($recommended_ids);
  //TODO o header devia desenhar coisas diferentes dependendo se o user está logado ou não
  drawFooter();
?>
</main>
</body>