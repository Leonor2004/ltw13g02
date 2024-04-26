<?php

class Product {
    public int $idProduct;
    public string $prodName;
    public int $price;
    public int $condition;
    public int $category;
    public int $prodsize;
    public int $seller;
    public ?int $buyer;
    public ?string $purchaseDate;

    public function __construct(int $idProduct, string $prodName, int $price, int $condition, int $category, int $prodsize, int $seller, ?int $buyer, ?string $purchaseDate) {
        $this->idProduct = $idProduct;
        $this->prodName = $prodName;
        $this->price = $price;
        $this->condition = $condition;
        $this->prodsize = $prodsize;
        $this->seller = $seller;
        $this->buyer = $buyer;
        $this->purchaseDate = $purchaseDate;
    }

    static function getBuyer(PDO $db, int $id) : array {
        $stmt = $db->prepare('
            SELECT firstName, lastName, stars, country, city
            FROM Product
            INNER JOIN User ON Product.buyer = User.idUser
            INNER JOIN Country ON User.idCountry = Country.idCountry
            WHERE Product.idProduct = ?
        ');
        $stmt->execute(array($id));

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    static function getSeller(PDO $db, int $id) : array {
        $stmt = $db->prepare('
            SELECT firstName, lastName, stars, country, city
            FROM Product
            INNER JOIN User ON Product.seller = User.idUser
            INNER JOIN Country ON User.idCountry = Country.idCountry
            WHERE Product.idProduct = ?
        ');
        $stmt->execute(array($id));

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    static function getCategory(PDO $db, int $id) : array {
        $stmt = $db->prepare('
            SELECT category
            FROM Product
            INNER JOIN Category ON Product.idCategory = Category.idCategory
            WHERE Product.idProduct = ?
        ');
        $stmt->execute(array($id));

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    static function getSize(PDO $db, int $id) : array {
        $stmt = $db->prepare('
            SELECT tamanho
            FROM Product
            INNER JOIN Sizes ON Product.prodsize = Sizes.idSize
            WHERE Product.idProduct = ?
        ');
        $stmt->execute(array($id));

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // TODO: getReviews

}