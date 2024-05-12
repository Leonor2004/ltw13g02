<?php
  declare(strict_types = 1);

  require_once(__DIR__ . '/../sessions/session.php');
  $session = new Session();

  require_once(__DIR__ . '/../database/connection.db.php');
  require_once(__DIR__ . '/../database/get_from_db.php');
  require_once(__DIR__ . '/../database/user.class.php');


  require_once(__DIR__ . '/../templates/common.tpl.php');
  require_once(__DIR__ . '/../templates/productsprint.tpl.php');

  $db = getDatabaseConnection();
  $categories = getCategories();

  drawHeader($session);
  drawHamburguer($session, 1);

  if ($session->isLoggedIn()) {

    $user = $session->getUser();
    $announcements_ids = $user->getAnnouncements();

    if (sizeof($announcements_ids) > 0) {drawAnnouncements($announcements_ids); }
  else { ?>
    <div class="user-info">
      <h2><?php echo "No Announcements"; ?></h2>
    </div>  
  <?php }
  }
  
  drawFooter();
?>