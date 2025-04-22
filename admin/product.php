<div class="section product-all active">
    <div class="admin-control">
        <div class="admin-control-left">
            <select name="the-loai" id="the-loai" onchange="showProduct()">
                <option>Tất cả</option>
                <option>Món chay</option>
                <option>Món mặn</option>
                <option>Món lẩu</option>
                <option>Món ăn vặt</option>
                <option>Món tráng miệng</option>
                <option>Nước uống</option>
            </select>
        </div>
        <div class="admin-control-center">
            <form action="" class="form-search">
                <span class="search-btn"><i class="fa-light fa-magnifying-glass"></i></span>
                <input id="form-search-product" type="text" class="form-search-input" placeholder="Tìm kiếm tên món..."
                    oninput="showProduct()">
            </form>
        </div>
        <div class="admin-control-right">
            <a href="admin.php?check=product">
                <button class="btn-control-large" id="btn-cancel-product" onclick="cancelSearchProduct()"><i
                        class="fa-light fa-rotate-right"></i> Làm mới</button>
            </a>
            <button class="btn-control-large" id="btn-add-product"><i class="fa-light fa-plus"></i> Thêm món
                mới</button>
        </div>
    </div>
    <div id="">
        <?php foreach ($products as $product): ?>
        <div class="list">
            <div class="list-left">
                <img src="../assets/img/products/<?php echo $product["image"] ?>" alt="">
                <div class="list-info">
                    <h4><?php echo $product["title"] ?></h4>
                    <p class="list-note"><?php echo $product["description"] ?></p>
                    <?php
                        $category = $category_Database->getCategoryById($product["category_id"]);
                    ?>
                    <span class="list-category"><?php echo $category["name"] ?></span>
                </div>
            </div>
            <div class="list-right">
                <div class="list-price">
                    <?php
                        $price           = $product["price"];
                        $formatted_price = number_format($price, 0, ',', '.');
                    ?>

                    <span class="list-current-price"><?php echo $formatted_price ?>&nbsp;₫</span>
                </div>
                <div class="list-control">
                    <div class="list-tool">
                        <?php $data = $product["title"] . "#" . $product["category_id"] . "#" . $product["price"] . "#" . $product["description"] . "#" . $product["id"] . "#" . $product["image"]?>
                        <button class="btn-edit" data="<?php echo $data ?>"><i class="fa-light fa-pen-to-square"
                                type="submit"></i></button>
                        <?php $confirm = "Bạn có chắc muốn xóa món ăn " . $product['title'] . " không ?"?>
                        <a href="admin.php?check=product&delete=<?php echo $product['id'] ?>&image=<?php echo $product['image'] ?>"
                            onclick="return confirm('<?php echo $confirm ?>')">
                            <button class="btn-delete"><i class="fa-regular fa-trash"></i></button>
                        </a>
                    </div>
                </div>
            </div>
        </div>
        <?php endforeach?>
    </div>
    <div class="page-nav">
        <ul class="page-nav-list">
            <!-- Pagination -->
            <?php
                $url   = $_SERVER["PHP_SELF"] . "?check=product";
                $total = $product_Database->paginationTotal();
                echo $product_Database->nagivationBar($url, $page, $perPage, $total);
            ?>
        </ul>
    </div>
</div>