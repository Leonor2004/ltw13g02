<?php
require_once(__DIR__ . '/../database/connection.db.php');
require_once(__DIR__ . '/../database/get_from_db.php');

class Product {
    public int $id;
    public string $name;
    public float $price;
    public int $condition;
    public int $category;
    public string $description;
    public string $characteristic1;
    public ?string $characteristic2;
    public ?string $characteristic3;
    public string $seller;
    public ?string $buyer;
    public function __construct(int $id, string $name, float $price, int $condition, string $description,
                                 ?string $characteristic1, ?string $characteristic2, ?string $characteristic3 , 
                                string $seller, ?string $buyer) {
        $this->id = $id;
        $this->name = $name;
        $this->price = $price;
        $this->condition = $condition;
        $this->description = $description;
        $this->characteristic1 = $characteristic1;
        $this->characteristic2 = $characteristic2;
        $this->characteristic3 = $characteristic3;
        $this->seller = $seller;
        $this->buyer = $buyer;
    }

    static function searchProduct($parameters=null) : array {        
        $query = '';
        $values = [];
        foreach($_GET as $key => $value) {
            if($value) {
                if($key=='minPrice') $query .= "price >= ? and ";
                elseif($key=='maxPrice') $query .= "price <= ? and ";
                elseif($key=='search') continue;
                elseif($key=='category') $query .= "t.category = ? and ";
                elseif($key=='condition') $query .= "p.condition = ? and ";
                else $query .= $key."= ? and ";
                $values[] = $value;
            }
        }

        $query = substr($query,0,strlen($query)-5);

        $db = getDatabaseConnection();

        if ($query) {
            $stmt = $db->prepare("SELECT idProduct,prodName FROM Product p
            left join Characteristic c1 on c1.idCharacteristic=p.characteristic1
            left join Characteristic c2 on c2.idCharacteristic=p.characteristic2
            left join Characteristic c3 on c3.idCharacteristic=p.characteristic3
            left join TypesInCategory t on t.idType=c1.idType
            left join Category c on c.idCategory=t.category
            left join Condition co on co.idCondition=p.condition
            WHERE $query"
            );
            $stmt->execute($values);
        }
        else {
            $stmt = $db->prepare("SELECT idProduct,prodName FROM Product");
            $stmt->execute([]);
        }

        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $products = array();
        foreach ($result as $product) {
            if( empty($parameters['search']) || (! empty($parameters['search']) && str_contains(strtolower($product['prodName']),strtolower($parameters['search'])))) {
                $products[] = getProduct($product['idProduct']);
            }
        }
        return $products;
    }

    function getCharacteristics(): array {
        $db = getDatabaseConnection();
        $characteristics = [];
    
        if ($this->characteristic1 !== null) {
            $stmt = $db->prepare('
                SELECT Characteristic.characteristic
                FROM Characteristic
                JOIN Product ON Characteristic.idCharacteristic = Product.characteristic1
                WHERE Product.idProduct = ?
            ');
            $stmt->execute([$this->id]);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            if ($result) {
                $characteristics[] = $result['characteristic'];
            }
        }
    
        if ($this->characteristic2 !== null) {
            $stmt = $db->prepare('
                SELECT Characteristic.characteristic
                FROM Characteristic
                JOIN Product ON Characteristic.idCharacteristic = Product.characteristic2
                WHERE Product.idProduct = ?
            ');
            $stmt->execute([$this->id]);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            if ($result) {
                $characteristics[] = $result['characteristic'];
            }
        }
    
        if ($this->characteristic3 !== null) {
            $stmt = $db->prepare('
                SELECT Characteristic.characteristic
                FROM Characteristic
                JOIN Product ON Characteristic.idCharacteristic = Product.characteristic3
                WHERE Product.idProduct = ?
            ');
            $stmt->execute([$this->id]);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            if ($result) {
                $characteristics[] = $result['characteristic'];
            }
        }
        
        return $characteristics;
    }

    function getCondition() : string {
        $db = getDatabaseConnection();
        $stmt = $db->prepare('
                SELECT Condition.condition
                FROM Condition, Product
                WHERE Product.idProduct = ? AND Product.condition = Condition.idCondition
            ');
        $stmt->execute([$this->id]);
        $result = $stmt->fetch();
        return $result['condition'];
    }
    
    function getCategory() : string {
        $db = getDatabaseConnection();
        $stmt = $db->prepare('
                SELECT Category.category
                FROM Characteristic, Category, TypesInCategory, Product
                WHERE Product.idProduct = ? AND Product.characteristic1 = Characteristic.idCharacteristic AND Characteristic.idType = TypesInCategory.idType AND TypesInCategory.category = Category.idCategory
            ');
        $stmt->execute([$this->id]);
        $result = $stmt->fetch();
        return $result['category'];
    }

    function getBuyer() : ?User {
        if (!isset($this->buyer)) {
            return null;
        }
        return getUserbyId($this->buyer);
    }

    function getSeller() : ?User {
        $db = getDatabaseConnection();
        $stmt = $db->prepare('SELECT * FROM User WHERE User.idUser = ?');
        $stmt->execute(array($this->seller));
        $user = $stmt->fetch();
        if ($user) {
            return new User(
                $user['idUser'],
                $user['firstName'],
                $user['lastName'],
                $user['phone'],
                $user['email'],
                $user['userAddress'],
                $user['stars'],
                $user['city'],
                $user['idCountry'],
                $user['photo'],
                $user['zipCode'],
            );
        } else {
            return null;
        }
    }

    function getPhotos(): ?array {
        $db = getDatabaseConnection();
        $stmt = $db->prepare('
            SELECT photo
            FROM Photo
            WHERE Photo.idProduct = ?
        ');
        $stmt->execute(array($this->id));
        $photos = $stmt->fetchAll();
        return $photos;
    }

    function getPhotosId(): ?array {
        $db = getDatabaseConnection();
        $stmt = $db->prepare('
            SELECT idPhoto, photo
            FROM Photo
            WHERE Photo.idProduct = ?
        ');
        $stmt->execute(array($this->id));
        $photos = $stmt->fetchAll();
        return $photos;
    }

    static function ajaxGetProducts ($parameters) : string {
        
        $products = Product::searchProduct($parameters);
    
        $final = "<section id='searchedProducts' class='Products'><div id='static_offer_container'> ";
        foreach ($products as $product) { 
            $user = getUserbyId($product->seller);
            $final .= "<div class='static_offer'>";
            $final .= "<a href='../pages/seller_page.php?user=$user->id' class='user_small_card'>";
            if ($user->photo != "Sem FF") {
                $final .= "<img class='user_small_pfp' src='../images/userProfile/$user->photo'> ";
            } else {
                $final .= "<h2><i class='fa fa-user fa-1x user-icons'></i></h2>";
            }
            $final .= "<p>" .$user->name() . "</p></a>";
            $final .= "<a href='../pages/productPage.php?product=$product->id'>";
            if ($product->getPhotos() != null) {
                $final .= "<img class='offer_img' src='../images/products/" . $product->getPhotos()[0]['photo']. "'></a>";
            } else {
                $final .= "<img class='offer_img' src='../images/products/no_images_small.png'></a>";
            }
            $final .= "<a class='offer_info' href='../pages/productPage.php?product=$product->id'>";
            $final .= "<h4>" . substr($product->name, 0, 30) . "</h4>";
            $final .= "<h5>" . $user->city . ', ' . $user->getCountry() . "</h5>";
            $final .= "<p>$product->price €</p></a></div>";
        }
        $final .= "</div></section>";
        return $final;
    }

    function getShipping() :Shipping | null{
        $db = getDatabaseConnection();
        $stmt = $db->prepare('SELECT P.shipping FROM Product P WHERE P.idProduct = ?');
        $stmt->execute(array($this->id));
        $shippment_id = $stmt->fetch(PDO::FETCH_COLUMN);
        if (isset($shippment_id)) {
            return getShipping($shippment_id);
        } else {
            return null;
        }
    }

}