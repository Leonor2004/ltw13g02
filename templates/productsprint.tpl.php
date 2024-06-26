<?php
require_once(__DIR__ . '/../database/get_from_db.php');
require_once(__DIR__ . '/../utils/filter.php');


function drawSearchbar(){ ?>
        <main>
        <input id="searchbar" type="text" name="searchbar" oninput="myFunction()" placeholder="Search..."/>
        <div id="search-results"></div>
<?php 
}
?>

<?php  
function drawPath() { 
    $category = $_GET["category"];
    if ($category != null) $types = getTypesofCategory($category);
    else $categories = getCategories();
    $conditions = getConditions();
?>
<script src="../javascript/search.js" defer></script>
<form id="filter" action="../pages/index.php" method="get">

    <select name="category" id="category" oninput="myFunction()">
        <option value="">Category</option> 
        <?php 
            foreach($categories as $c){ 
        ?>
                <option value="<?= $c['idCategory'] ?>"> 
                    <?= $c['category'] ?>
                </option>    
        <?php 
            }
        ?>
    </select>

    <select name="condition" id="condition" oninput="myFunction()">
        <?php 
            echo "<option value=''>Condition</option>";
            foreach($conditions as $c) { 
        ?>
                <option value="<?= $c['idCondition'] ?>"> 
                    <?= $c['condition'] ?>
                </option>    
        <?php 
            }
        ?>
    </select>

    <input type="text" oninput="myFunction()" class="price-filter" id="price-min" name="price-min" placeholder=" Min Price" value="<?= $_GET["price-min"] === NULL ? "" : $_GET["price-min"] ?>"/>
    <input type="text" oninput="myFunction()" class="price-filter" id="price-max" name="price-max" placeholder=" Max Price" value="<?= $_GET["price-max"] === NULL ? "" : $_GET["price-max"] ?>"/>

    <div id="characteristics" class="hidden"></div>

</form>
<?php } ?>




<?php
function drawRecent($recent_ids)
    { ?>
        <section class="Products" id="Recent">
            <h2>Recents</h2>
            <article>
                <div class="sliding_offers_container"> <?php 
                    foreach($recent_ids as $item_id)
                    {
                        $product = getProduct($item_id);

                        $seller = $product->getSeller();
                        ?>
                        <div class="sliding_offer"> <?php
                            drawSmallProduct($product, $seller, null);  ?>
                        </div> <?php 
                    } ?>
                </div>
            </article>
        </section>
    <?php } 
?>

<?php 

function drawFavorites($favorites_ids){ ?>
    <section class="Products" id="Favorites">
        <h2>Favorites</h2>
            <div id="static_offer_container"> <?php 
                foreach($favorites_ids as $item_id)
                {
                    $product = getProduct($item_id);

                    $seller = $product->getSeller();
                    ?>
                    <div class="sliding_offer"> <?php
                        drawSmallProduct($product, $seller, null);  ?>
                    </div> <?php 
                } ?>
            </div>
    </section>
<?php 
}
?>

<?php
function drawRecommended($recommended_ids) { ?>
    <section class="Products" id="Recommended">
        <h2>Recommended</h2>
        <div id="static_offer_container">
            <?php
            for ($i = 0; $i < 20; $i++)
            {
                if(isset($recommended_ids[$i])){
                    $item_id = $recommended_ids[$i];
                    $product = getProduct($item_id);

                    $seller = $product->getSeller();?>
                    <div class="static_offer"> <?php
                        drawSmallProduct($product, $seller, null);  ?>
                    </div> <?php
                }
            } ?>
        </div>
    </section>
        </main>
<?php }
?>

<?php
function drawSellerProducts($seller_items_ids) { ?>
    <section class="Products" id="SellerProducts">
        <h2>Seller Products</h2>
        <div id="static_offer_container">
            <?php
            $firstProduct = getProduct($seller_items_ids[0]);
            $seller = $firstProduct->getSeller();
            foreach($seller_items_ids as $itemId)
            { 
                $product = getProduct($itemId) ?>   
                <div class="static_offer"> <?php
                    drawSmallProduct($product, $seller, null);  ?>
                </div> <?php
            }
            ?>
        </div>
    </section>
<?php }
?>

<?php
function drawAnnouncements($announcements_ids, $user)
    { ?>
        <section class="Products" id="Announcements">
            <h2>My Announcements</h2>
            <div id="static_offer_container"> <?php 
                foreach($announcements_ids as $item_id)
                {
                    $product = getProduct($item_id);

                    $seller = $product->getSeller();
                    ?>
                    <div class="sliding_offer"> <?php
                        drawSmallProduct($product, $seller, $user);  ?>
                    </div> <?php 
                } ?>
            </div>
        </section>
    <?php } 
?>

<?php
function drawArchive($archive_ids)
    { ?>
        <section class="Products" id="Archive">
            <h2>My Archive</h2>
            <div id="static_offer_container"> <?php 
                foreach($archive_ids as $item_id)
                {
                    $product = getProduct($item_id);

                    $seller = $product->getSeller();
                    ?>
                    <div class="sliding_offer"> <?php
                        drawSmallProduct($product, $seller, $product->getBuyer());  ?>
                    </div> <?php 
                } ?>
            </div>
        </section>
    <?php } 
?>

<?php
function drawBought($bought_ids, $user)
    { ?>
        <section class="Products" id="Bought">
            <h2>My Shopping</h2>
            <div id="static_offer_container"> <?php 
                foreach($bought_ids as $item_id)
                {
                    $product = getProduct($item_id);

                    $seller = $product->getSeller();
                    ?>
                    <div class="sliding_offer"> <?php
                        drawSmallProduct($product, $seller, $user);  ?>
                    </div> <?php 
                } ?>
            </div>
        </section>
    <?php } 
?>


<?php
function drawSmallProduct(Product $product, User $seller, ?User $user) { 
    $buyer = $product->getBuyer();
    ?>

    <a href="../pages/seller_page.php?user=<?=$seller->id?>" class="user_small_card">
        <?php if ($seller->photo != "Sem FF") { ?>
            <img class="user_small_pfp" src="../images/userProfile/<?=$seller->photo?>"> 
        <?php } else { ?>
            <h2><i class="fa fa-user fa-1x user-icons"></i></h2>
        <?php } ?>
        <p><?=$seller->name() ?></p>
    </a>

    <?php 
    $productPhotos = $product->getPhotos();
    $productImage = isset($productPhotos[0]['photo']) ? $productPhotos[0]['photo'] : null;

    if (!isset($user)) { 
        if ($productImage != null) { ?>
            <a href="../pages/productPage.php?product=<?=$product->id?>"><img class="offer_img" src="../images/products/<?=$productImage?>"></a>
        <?php } else { ?>
            <a href="../pages/productPage.php?product=<?=$product->id?>"><img class="offer_img" src="../images/products/no_images_small.png"></a>
        <?php } ?>
           
        <a class="offer_info" href="../pages/productPage.php?product=<?=$product->id?>">
            <h4><?=substr($product->name, 0, 30) ?></h4>
            <h5><?= $seller->city . ", " . $seller->getCountry()?></h5>
            <p><?=$product->price?>€</p>
        </a>
    <?php } else if (isset($buyer)) { 
        $imagePageLink = !isset($product->buyer) ? "product={$product->id}" : "shipping={$product->getShipping()->id}"; ?>
        <?php if ($productImage != null) { ?>
            <a href="../pages/shipmentPage.php?<?=$imagePageLink?>"><img class="offer_img" src="../images/products/<?=$productImage?>"></a>
        <?php } else { ?>
            <a href="../pages/shipmentPage.php?<?=$imagePageLink?>"><img class="offer_img" src="../images/products/no_images_small.png"></a>
        <?php } ?>

        <a class="offer_info" href="../pages/shipmentPage.php?<?=$imagePageLink?>">
            <h4><?=substr($product->name, 0, 30) ?></h4>
            <h5><?= $seller->city . ", " . $seller->getCountry()?></h5>
            <p><?=$product->price?>€</p>
        </a>
    <?php } else { 
        if ($productImage != null) { ?>
            <a href="../pages/myProduct.php?product=<?=$product->id?>"><img class="offer_img" src="../images/products/<?=$productImage?>"></a>
        <?php } else { ?>
            <a href="../pages/myProduct.php?product=<?=$product->id?>"><img class="offer_img" src="../images/products/no_images_small.png"></a>
        <?php } ?>
    
        <a class="offer_info" href="../pages/myProduct.php?product=<?=$product->id?>">
            <h4><?=substr($product->name, 0, 30) ?></h4>
            <h5><?= $seller->city . ", " . $seller->getCountry()?></h5>
            <p><?=$product->price?>€</p>
        </a>
    <?php } ?>
    
<?php } ?>

