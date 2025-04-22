<?php session_id() || session_start()?>
<header>
    <div class="header-top">
        <div class="container">
            <div class="header-top-left">
                <ul class="header-top-list">
                    <li>
                        <a href=""><i class="fa-regular fa-phone"></i> 0123 456 789 (miễn phí)</a>
                    </li>
                    <li>
                        <a href=""><i class="fa-light fa-location-dot"></i> Xem vị trí cửa hàng</a>
                    </li>
                </ul>
            </div>
            <div class="header-top-right">
                <ul class="header-top-list">
                    <li><a href="">Giới thiệu</a></li>
                    <li><a href="">Cửa hàng</a></li>
                    <li><a href="">Chính sách</a></li>
                </ul>
            </div>
        </div>
    </div>
    <div class="header-middle">
        <div class="container">
            <div class="header-middle-left">
                <div class="header-logo">
                    <a href="">
                        <img src="../assets/img/vy-food.png" alt="" class="header-logo-img" />
                    </a>
                </div>
            </div>
            <div class="header-middle-center">
                <form action="" class="form-search">
                    <span class="search-btn"><i class="fa-light fa-magnifying-glass"></i></span>
                    <input type="text" class="form-search-input" placeholder="Tìm kiếm món ăn..."
                        oninput="searchProducts()" />
                    <button class="filter-btn">
                        <i class="fa-light fa-filter-list"></i><span>Lọc</span>
                    </button>
                </form>
            </div>
            <div class="header-middle-right">
                <ul class="header-middle-right-list">
                    <li class="header-middle-right-item dnone open" onclick="openSearchMb()">
                        <div class="cart-icon-menu">
                            <i class="fa-light fa-magnifying-glass"></i>
                        </div>
                    </li>
                    <li class="header-middle-right-item close" onclick="closeSearchMb()">
                        <div class="cart-icon-menu">
                            <i class="fa-light fa-circle-xmark"></i>
                        </div>
                    </li>
                    <?php if (isset($_SESSION["fullname"])): ?>
                    <li class="header-middle-right-item dropdown open">
                        <i class="fa-light fa-user"></i>
                        <div class="auth-container">
                            <span class="text-dndk">Tài khoản</span>
                            <span class="text-tk"><?php echo $_SESSION["fullname"] ?>>
                                <i class="fa-sharp fa-solid fa-caret-down"></i></span>
                        </div>
                        <ul class="header-middle-right-menu">
                            <li><a href="javascript:;" onclick="myAccount()"><i class="fa-light fa-circle-user"></i>
                                    Tài khoản của tôi</a></li>
                            <li><a href="javascript:;" onclick="orderHistory()"><i
                                        class="fa-regular fa-bags-shopping"></i> Đơn hàng đã mua</a></li>
                            <li class="border"><a id="logout" href="index.php?action=exit"><i
                                        class="fa-light fa-right-from-bracket"></i> Thoát tài khoản</a></li>
                        </ul>
                    </li>
                    <?php else: ?>
                    <li class="header-middle-right-item dropdown open">
                        <i class="fa-light fa-user"></i>
                        <div class="auth-container">
                            <span class="text-dndk">Đăng nhập / Đăng ký</span>
                            <span class="text-tk"> Tài khoản
                                <i class="fa-sharp fa-solid fa-caret-down"></i></span>
                        </div>
                        <ul class="header-middle-right-menu">
                            <li>
                                <a id="login" href="javascript:;"><i class="fa-light fa-right-to-bracket"></i> Đăng
                                    nhập</a>
                            </li>
                            <li>
                                <a id="signup" href="javascript:;"><i class="fa-light fa-user-plus"></i> Đăng ký</a>
                            </li>
                        </ul>
                    </li>
                    <?php endif?>


                    <li class="header-middle-right-item open" onclick="openCart()">
                        <div class="cart-icon-menu">
                            <i class="fa-light fa-basket-shopping"></i>
                            <span class="count-product-cart">0</span>
                        </div>
                        <span>Giỏ hàng</span>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</header>
<nav class="header-bottom">
    <div class="container">
        <ul class="menu-list">
            <li class="menu-list-item">
                <a href="index.php" class="menu-link">Trang chủ</a>
            </li>
            <?php foreach ($categories as $category): ?>
            <li class="menu-list-item" onclick="showCategory('Món chay')">
                <a href="index.php?category_id=<?php echo $category["id"] ?>"
                    class="menu-link"><?php echo $category["name"] ?></a>
            </li>
            <?php endforeach?>
        </ul>
    </div>
</nav>
<div class="advanced-search">
    <div class="container">
        <div class="advanced-search-category">
            <span>Phân loại </span>
            <select name="" id="advanced-search-category-select" onchange="searchProducts()">
                <option>Tất cả</option>
                <option>Món chay</option>
                <option>Món mặn</option>
                <option>Món lẩu</option>
                <option>Món ăn vặt</option>
                <option>Món tráng miệng</option>
                <option>Nước uống</option>
            </select>
        </div>
        <div class="advanced-search-price">
            <span>Giá từ</span>
            <input type="number" placeholder="tối thiểu" id="min-price" onchange="searchProducts()" />
            <span>đến</span>
            <input type="number" placeholder="tối đa" id="max-price" onchange="searchProducts()" />
            <button id="advanced-search-price-btn">
                <i class="fa-light fa-magnifying-glass-dollar"></i>
            </button>
        </div>
        <div class="advanced-search-control">
            <button id="sort-ascending" onclick="searchProducts(1)">
                <i class="fa-regular fa-arrow-up-short-wide"></i>
            </button>
            <button id="sort-descending" onclick="searchProducts(2)">
                <i class="fa-regular fa-arrow-down-wide-short"></i>
            </button>
            <button id="reset-search" onclick="searchProducts(0)">
                <i class="fa-light fa-arrow-rotate-right"></i>
            </button>
            <button onclick="closeSearchAdvanced()">
                <i class="fa-light fa-xmark"></i>
            </button>
        </div>
    </div>
</div>