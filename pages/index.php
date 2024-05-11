<?php
  declare(strict_types = 1);

  require_once(__DIR__ . '/../sessions/session.php');
  $session = new Session();
  
  require_once(__DIR__ . '/../database/connection_to_db.php');
  require_once(__DIR__ . '/../database/get_from_db.php');
  require_once(__DIR__ . '/../database/userClass.php');


  require_once(__DIR__ . '/../templates/common_tmpl.php');
  require_once(__DIR__ . '/../templates/productsprint_tmpl.php');


  $db = getDatabaseConnection();
  $categories = getCategories();
  drawHeader($session);

  drawSearchbar($categories);

  if ($session->isLoggedIn()) 
  {

    $user = $session->getUser();
    $recent_ids = $user->getRecent();
    
    if (sizeof($recent_ids) > 0) {drawRecent($recent_ids); }

  }
  $recommended_ids = getRecommended();
  drawRecommended($recommended_ids);
  drawFooter();
