<?php
declare(strict_types = 1);

require_once(__DIR__ . '/../sessions/session.php');

require_once(__DIR__ . "/user.tpl.php");

?>

<?php function drawFilterBar(User $user) { ?>
    <main id="reviewsPage">
        <div class="filterBar-container">
            <div class="average-ratting">
                <span class="stars">
                    <h2 id="stars">
                        <?php
                        $stars = $user->getStarsFromReviews();

                        drawStars($stars);
                        ?>
                    </h2>
                </span>
                <span class="ratting-score"><?=number_format($stars, 1); ?></span>
                <div><?=$user->name()?></div>
            </div>
            <div class="filters">
                <label for="reviewOrder"><strong>Order:</strong></label>
                    <select id="reviewOrder">
                        <option value="highest">Highest first</option>
                        <option value="lowest">Lowest first</option>
                        <option value="newest">Newest first</option>
                        <option value="oldest">Oldest first</option>
                    </select>
                <form action="../pages/reviewsPage.php" method="get">
                    <label class="classification" for="classification">Classification:</label>
                    <select id="classific-select" class="classification" name="classification"> <!-- Added name attribute -->
                        <option value="-1"></option>
                        <option value="5"> 5 </option>
                        <option value="4"> 4 </option>
                        <option value="3"> 3 </option>
                        <option value="2"> 2 </option>
                        <option value="1"> 1 </option>
                        <option value="0"> 0 </option>
                        <input type="hidden" name="user" value="<?=$user->id?>">
                    </select>
                    <button id="go" class="button" type="submit">Go</button>
                </form>
            </div>
        </div>
<?php } ?>

<?php function drawReviews(User $user, $classification) { 
    $reviews = $user->getReviewsWithUsersFromDB($classification);

    ?>
        <div id="review-list">
        <?php
            foreach ($reviews as $row) {
                ?>
                <div class="review">
                    <a href="../pages/seller_page.php?user=<?=$row['idUserFrom']?>"><h2 class="review-user"><?=$row['firstName'] . " " . $row['lastName']?></h2></a>
                    <h2 class="created-at"><?php echo $row['created_at']; ?></h2>
                    <h2 class="stars"><?php drawStars($row['stars']);?></h2>
                    <h2 class="review-description"><?=$row['reviewsDescription']?></h2>
                </div>
                <?php
            }
            ?>
        </div>
    </main>
<?php } ?>

<?php
    function drawReviewForm(User $user) { 
        $session = new Session()?>
        <div id="reviewForm" class="review">
            <form action="../actions/addReview.php" method="post">
                <input type="hidden" name='csrf' value="<?=$session->getCSRF()?>">
                <div id="starsForm">
                    <div><strong>Stars:</strong></div>
                    <select name='stars'>
                        <option value='5'>5</option>
                        <option value='4'>4</option>
                        <option value='3'>3</option>
                        <option value='2'>2</option>
                        <option value='1'>1</option>
                        <option value='0'>0</option>
                    </select>
                </div>
                    <label for="userReviewForm"></label>
                    <textarea id="userReviewForm" name="reviewText" maxlength="500" placeholder="Review text..." required="required"></textarea>

                    <input type="hidden" name="reviewedUser" value="<?=$user->id?>">
                    <input type="hidden" name="csrf" value="<?=$session->getCSRF()?>">
                    <button class="submitButton" type="submit">Send</button>
            </form> 
        </div><?php
    }
?>

