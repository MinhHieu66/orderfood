<?php if (isset($products)): ?>
<div class="home-products" id="home-products">

    <?php foreach ($products as $product): ?>
    <div class="col-product" data-id="<?php echo $product['id'] ?>">
        <article class="card-product">
            <div class="card-header">
                <a href="#" class="card-image-link" onclick="detailProduct(1)">
                    <img class="card-image" src="../assets/img/products/<?php echo $product["image"] ?>"
                        alt="Nấm đùi gà xào cháy tỏi">
                </a>
            </div>
            <div class="food-info">
                <div class="card-content">
                    <div class="card-title">
                        <a href="#" class="card-title-link"
                            onclick="detailProduct(1)"><?php echo $product["title"] ?></a>
                    </div>
                </div>
                <div class="card-footer">
                    <div class="product-price">
                        <span
                            class="current-price"><?php echo number_format($product["price"], 0, ",", ".") ?>&nbsp;₫</span>
                    </div>
                    <div class="product-buy">
                        <button onclick="detailProduct(1)" class="card-button order-item"><i
                                class="fa-regular fa-cart-shopping-fast"></i> Đặt món</button>
                    </div>
                </div>
            </div>
        </article>
    </div>
    <?php endforeach?>

</div>
<?php if (isset($isFlag) && $isFlag): ?>
<div class="page-nav">
    <ul class="page-nav-list">
        <!-- Pagination -->
        <?php
            $url   = $_SERVER["PHP_SELF"] . "?check=product";
            $total = $product_Database->paginationTotal($category_id);
            echo $product_Database->nagivationBar($url, $page, $perPage, $total, $category_id);
        ?>
    </ul>
</div>
<?php endif?>
<?php endif?>