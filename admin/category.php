<style>
.flex {
    display: flex;
    gap: 16px;
    flex-wrap: wrap;
}

.list {
    flex: 1;
    min-width: 300px;
    background: #fff;
    border: 1px solid #ddd;
    border-radius: 12px;
    padding: 16px;
    box-shadow: 0 2px 6px rgba(0, 0, 0, 0.05);
    margin: 0 !important;
}

.edit-category {
    background-color: #eee;
    padding: 5px 15px;
    border-radius: 15px;
    font-size: 14px;
    color: var(--text-color);
    text-transform: uppercase;
    margin-left: 0;
    outline: none;
    border: none;
    cursor: pointer;
}
</style>
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
            <button class="btn-control-large" id="btn-add-category"><i class="fa-light fa-plus"></i>Thêm danh mục
                mới</button>
        </div>
    </div>
    <div id="">
        <!-- <div class="flex">
            <div class="list">
                <div class="list-left">
                    <div class="list-info">
                        <h4>sdfjkhsjkdf</h4>
                    </div>
                </div>
                <div class="list-right">
                    <div class="list-control">
                        <div class="list-tool">
                            <button class="btn-edit" data=""><i class="fa-light fa-pen-to-square"
                                    type="submit"></i></button>
                            <a href="">
                                <button class="btn-delete"><i class="fa-regular fa-trash"></i></button>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            <div class="list">
                <div class="list-left">
                    <div class="list-info">
                        <h4>dsfsfjs</h4>
                    </div>
                </div>
                <div class="list-right">
                    <div class="list-control">
                        <div class="list-tool">
                            <button class="btn-edit" data=""><i class="fa-light fa-pen-to-square"
                                    type="submit"></i></button>
                            <a href="">
                                <button class="btn-delete"><i class="fa-regular fa-trash"></i></button>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div> -->

        <div class="flex">
            <?php foreach ($categories as $category): ?>
            <div class="list">
                <div class="list-left">
                    <div class="list-info">
                        <h4><?php echo $category["name"] ?></h4>
                    </div>
                </div>
                <div class="list-right">
                    <div class="list-control">
                        <div class="list-tool">
                            <?php $data = $category["name"] . "#" . $category["id"]?>
                            <button class="edit-category" data="<?php echo $data ?>"><i
                                    class="fa-light fa-pen-to-square" type="submit"></i></button>
                            <?php $confirm = "Bạn có chắc muốn xóa danh mục " . $category["name"] . " không ?"?>
                            <a href="admin.php?check=category&delete=<?php echo $category['id'] ?>"
                                onclick="return confirm('<?php echo $confirm ?>')">
                                <button class="btn-delete"><i class="fa-regular fa-trash"></i></button>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            <?php endforeach?>
        </div>
    </div>
    <!-- <div class="page-nav">
        <ul class="page-nav-list">
            <?php
                $url   = $_SERVER["PHP_SELF"] . "?check=product";
                $total = $product_Database->paginationTotal();
                echo $product_Database->nagivationBar($url, $page, $perPage, $total);
            ?>
        </ul>
    </div> -->
</div>