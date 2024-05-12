<?php
declare(strict_types = 1);

require_once(__DIR__ . '/../sessions/session.php');

require_once(__DIR__ . '/../database/get_from_db.php');

require_once(__DIR__ . '/../database/change_in_db.php');

require_once(__DIR__ . '/user.tpl.php');


?>

<?php function drawChatHeader(Session $session, $idChat) { 
    $chat = getChat($idChat);
    $info = $chat->getInfo(); ?>
    <div class="chat-header">
        <?php 
        $product = getProduct($info['idProduct']);
        $photos = $product->getPhotos();
        ?>
        <a href="../pages/<?php echo $info['BId'] === $session->getUser()->id ? "chatsAsBuyerPage.php" : "chatsAsSellerPage.php" ?>"><i class="fa fa-angle-left fa-2x chat-back-button"></i></a>
        <img class="chat-productphoto" src="../images/products/<?php echo $photos[0]["photo"]; ?>" alt="Photo">
        <div class="chat">
                <?php if ($session->getUser()->id === $info['SId']) { ?>
                    <a href="../pages/seller_page.php?user=<?php echo $info['SId']; ?>"><h2 class="with-user"><?php echo $info['BFN'] . " " . $info['BLN']; ?></h2>
                <?php } else { ?>
                    <a href="../pages/seller_page.php?user=<?php echo $info['BId']; ?>"><h2 class="with-user"><?php echo $info['SFN'] . " " . $info['SLN']; ?></h2>
                <?php } ?>
            </a>
            <a href="../pages/productPage.php?product=<?php echo $info['idProduct']; ?>&chat=<?php echo $idChat; ?>"><h2 class="message-product"><?php echo $info['ProdName']; ?></h2> </a>
        </div>
            </div>
<?php } ?> 

<?php function drawMessages(Session $session, $idChat) {
    $chat = getChat($idChat);
    $messages = $chat->getMessages();
    $chat->setAsSeen($session->getUser()->id); ?>
    <div class="column-of-messages">
        <?php foreach ($messages as $key => $message) { ?>
            <?php if ($message->sender === $session->getUser()->id) { ?>
                <div class="message-container">
                    <div class="message-tile message own-message">
                        <p><?php echo $message->content; ?></p>
                    </div>
                    <h2 class="message-status <?php echo $message->seen ? "fa fa-check-circle" : "fa fa-check-circle-o"; ?>"></h2>
                </div>
                <?php
                if ($key < count($messages) - 1 && strtotime($message->messageDate) - strtotime($messages[$key + 1]->messageDate) < 3600) {
                } 
                else { ?>
                    <div class="time">
                        <p><?php echo $message->messageDate; ?></p>
                    </div>
                <?php } 
             } else { 
            ?>
            <div class="message-tile message other-message">
                <p><?php echo $message->content;?></p>
            </div>
            <?php
                if ($key < count($messages) - 1 && strtotime($message->messageDate) - strtotime($messages[$key + 1]['messageDate']) < 3600) {
                } 
                else { ?>
                    <div class="time">
                        <p><?php echo $message->messageDate; ?></p>
                    </div>
                <?php }
             }
            } ?>
    </div>
<?php } ?> 

<?php function drawMessagesFooter(Session $session, $idChat) { ?>
        <div class="input">
            <form method="post" action="" class="messages-input-form">
                <input name="message" placeholder="Type your message here!" type="text">
                <button id="Send" type="submit" class="Send"><i class="fa fa-paper-plane fa-1x icon send-icon"></i></button>
            </form>    
        </div>
    </body>
</html>
<?php
    if ($_SERVER["REQUEST_METHOD"] === "POST") {
        $chat = getChat($idChat);
        $message = $_POST['message'];
        $chat->addMessage($session->getUser()->id, $message);

        header("Location: ".$_SERVER['PHP_SELF']."?chat=".urlencode($idChat));
    }
} ?> 