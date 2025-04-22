<?php
    session_start();
    require_once "../model/User_Database.php";
    require_once "../model/Category_Database.php";
    require_once "../model/Product_Database.php";

    if (isset($_SESSION["fullname"])) {
        $_SESSION['logout'] = "Bạn đã đăng xuất khỏi hệ thống!";
        unset($_SESSION["fullname"]);
    }

    $user_Database = new User_Database;
    if (isset($_POST["password"]) && isset($_POST["phone"])) {
        $password = $_POST["password"];
        $phone    = $_POST["phone"];
        $user     = $user_Database->login($phone, $password);
        if (isset($user) && $user["role_id"] == 2) {
            $_SESSION["fullname"]        = $user["user_name"];
            $_SESSION['success_message'] = "Đăng nhập thành công! Chào mừng " . $user["user_name"] . " .";
        } else if (isset($user) && $user["role_id"] == 1) {
            $_SESSION["fullname"]        = $user["user_name"];
            $_SESSION['success_message'] = "Đăng nhập thành công! Chào mừng " . $user["user_name"] . " .";
            header("Location: ../admin/admin.php");
            exit();
        } else {
            $_SESSION['error_message'] = "Tên đăng nhập hoặc mật khẩu không đúng!";
        }

    }

    //Lay danh sach loai mon an
    $category_Database = new Category_Database();
    $categories        = $category_Database->getAllCategories();

    if (isset($_GET["action"]) && $_GET["action"] == "exit") {
        unset($_SESSION["fullname"]);
        $_SESSION['logout'] = "Bạn đã đăng xuất khỏi hệ thống!";
    }

    // Lay tat ca san pham
    $product_Database = new Product_Database();
    $page             = isset($_GET["page"]) ? $_GET["page"] : 1;
    $perPage          = 8;
    if (isset($_GET["category_id"])) {
        $category_id = $_GET["category_id"];
        $products    = $product_Database->getCategoriesPagination($page, $perPage, $category_id);
    } else {
        $category_id = "";
        $products    = $product_Database->getCategoriesPagination($page, $perPage);
    }

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Vy Food</title>
    <link href="../assets/img/favicon.png" rel="icon" type="image/x-icon" />
    <link rel="stylesheet" href="../assets/css/main.css" />
    <link rel="stylesheet" href="../assets/css/home-responsive.css" />
    <link rel="stylesheet" href="../assets/css/toast-message.css" />
    <link rel="stylesheet" href="../assets/font/font-awesome-pro-v6-6.2.0/css/all.min.css" />
    <style>
    /* CSS cho toast */
    .toast {
        visibility: hidden;
        min-width: 250px;
        background-color: #333;
        height: 50px;
        color: #fff;
        text-align: center;
        border-radius: 5px;
        padding: 16px;
        position: fixed;
        bottom: 30px;
        right: 30px;
        z-index: 1000;
        font-size: 16px;
    }

    .toast.show {
        visibility: visible;
        animation: fadein 0.5s, fadeout 0.5s 2.5s;
    }

    @keyframes fadein {
        from {
            bottom: 0;
            opacity: 0;
        }

        to {
            bottom: 30px;
            opacity: 1;
        }
    }

    @keyframes fadeout {
        from {
            bottom: 30px;
            opacity: 1;
        }

        to {
            bottom: 0;
            opacity: 0;
        }
    }
    </style>
</head>

<body>
    <?php
        if (isset($_SESSION['success_message'])) {
            echo '<div id="toast" class="toast show">' . $_SESSION['success_message'] . '</div>';
            unset($_SESSION['success_message']);
        } else if (isset($_SESSION['error_message'])) {
            echo '<div id="toast" class="toast show">' . $_SESSION['error_message'] . '</div>';
            unset($_SESSION['error_message']);
        } else if (isset($_SESSION['logout'])) {
            echo '<div id="toast" class="toast show">' . $_SESSION['logout'] . '</div>';
            unset($_SESSION['logout']);
        }
    ?>
    <!-- Header -->
    <?php require_once "header.php"?>
    <!-- Banner -->
    <?php require_once "banner.php"?>

    <div class="modal product-detail">
        <button class="modal-close close-popup">
            <i class="fa-thin fa-xmark"></i>
        </button>
        <div class="modal-container mdl-cnt" id="product-detail-content"></div>
    </div>
    <div class="modal signup-login">
        <div class="modal-container">
            <button class="form-close" onclick="closeModal()">
                <i class="fa-regular fa-xmark"></i>
            </button>
            <div class="forms mdl-cnt">
                <div class="form-content sign-up">
                    <h3 class="form-title">Đăng ký tài khoản</h3>
                    <p class="form-description">
                        Đăng ký thành viên để mua hàng và nhận những ưu đãi đặc biệt từ
                        chúng tôi
                    </p>
                    <form action="" class="signup-form">
                        <div class="form-group">
                            <label for="fullname" class="form-label">Tên đầy đủ</label>
                            <input id="fullname" name="fullname" type="text" placeholder="VD: Nhật Sinh"
                                class="form-control" />
                            <span class="form-message-name form-message"></span>
                        </div>
                        <div class="form-group">
                            <label for="phone" class="form-label">Số điện thoại</label>
                            <input id="phone" name="phone" type="text" placeholder="Nhập số điện thoại"
                                class="form-control" />
                            <span class="form-message-phone form-message"></span>
                        </div>
                        <div class="form-group">
                            <label for="password" class="form-label">Mật khẩu</label>
                            <input id="password" name="password" type="password" placeholder="Nhập mật khẩu"
                                class="form-control" />
                            <span class="form-message-password form-message"></span>
                        </div>
                        <div class="form-group">
                            <label for="password_confirmation" class="form-label">Nhập lại mật khẩu</label>
                            <input id="password_confirmation" name="password_confirmation"
                                placeholder="Nhập lại mật khẩu" type="password" class="form-control" />
                            <span class="form-message-password-confi form-message"></span>
                        </div>
                        <div class="form-group">
                            <input class="checkbox" name="checkbox" required="" type="checkbox" id="checkbox-signup" />
                            <label for="checkbox-signup" class="form-checkbox">Tôi đồng ý với
                                <a href="#" title="chính sách trang web" target="_blank">chính sách trang
                                    web</a></label>
                            <p class="form-message-checkbox form-message"></p>
                        </div>
                        <button class="form-submit" id="signup-button">Đăng ký</button>
                    </form>
                    <p class="change-login">
                        Bạn đã có tài khoản ?
                        <a href="javascript:;" class="login-link">Đăng nhập ngay</a>
                    </p>
                </div>

                <div class="form-content login">
                    <h3 class="form-title">Đăng nhập tài khoản</h3>
                    <p class="form-description">
                        Đăng nhập thành viên để mua hàng và nhận những ưu đãi đặc biệt từ
                        chúng tôi
                    </p>
                    <form action="index.php" class="login-form" method="POST">
                        <div class="form-group">
                            <label for="phone" class="form-label">Số điện thoại</label>
                            <input id="phone-login" name="phone" type="text" placeholder="Nhập số điện thoại"
                                class="form-control" required />
                            <span class="form-message phonelog"></span>
                        </div>
                        <div class="form-group">
                            <label for="password" class="form-label">Mật khẩu</label>
                            <input id="password-login" name="password" type="password" placeholder="Nhập mật khẩu"
                                class="form-control" required />
                            <span class="form-message-check-login form-message"></span>
                        </div>
                        <!-- <button class="form-submit" id="login-button">Đăng nhập</button> -->
                        <button class="form-submit" type="submit">Đăng nhập</button>
                    </form>
                    <p class="change-login">
                        Bạn chưa có tài khoản ?
                        <a href="javascript:;" class="signup-link">Đăng kí ngay</a>
                    </p>
                </div>
            </div>
        </div>
    </div>
    <div class="modal-cart">
        <div class="cart-container">
            <div class="cart-header">
                <h3 class="cart-header-title">
                    <i class="fa-regular fa-basket-shopping-simple"></i> Giỏ hàng
                </h3>
                <button class="cart-close" onclick="closeCart()">
                    <i class="fa-sharp fa-solid fa-xmark"></i>
                </button>
            </div>
            <div class="cart-body">
                <div class="gio-hang-trong">
                    <i class="fa-thin fa-cart-xmark"></i>
                    <p>Không có sản phẩm nào trong giỏ hàng của bạn</p>
                </div>
                <ul class="cart-list"></ul>
            </div>
            <div class="cart-footer">
                <div class="cart-total-price">
                    <p class="text-tt">Tổng tiền:</p>
                    <p class="text-price">0đ</p>
                </div>
                <div class="cart-footer-payment">
                    <button class="them-mon">
                        <i class="fa-regular fa-plus"></i> Thêm món
                    </button>
                    <button class="thanh-toan disabled">Thanh toán</button>
                </div>
            </div>
        </div>
    </div>
    <div class="modal detail-order">
        <div class="modal-container mdl-cnt">
            <h3 class="modal-container-title">Thông tin đơn hàng</h3>
            <button class="form-close" onclick="closeModal()">
                <i class="fa-regular fa-xmark"></i>
            </button>
            <div class="detail-order-content"></div>
        </div>
    </div>
    <!-- Footer -->
    <?php require_once "footer.php"?>
    <div class="back-to-top">
        <a href="#"><i class="fa-regular fa-arrow-up"></i></a>
    </div>
    <div class="checkout-page">
        <div class="checkout-header">
            <div class="checkout-return">
                <button onclick="closecheckout()">
                    <i class="fa-regular fa-chevron-left"></i>
                </button>
            </div>
            <h2 class="checkout-title">Thanh toán</h2>
        </div>
        <main class="checkout-section container">
            <div class="checkout-col-left">
                <div class="checkout-row">
                    <div class="checkout-col-title">Thông tin đơn hàng</div>
                    <div class="checkout-col-content">
                        <div class="content-group">
                            <p class="checkout-content-label">Hình thức giao nhận</p>
                            <div class="checkout-type-order">
                                <button class="type-order-btn active" id="giaotannoi">
                                    <i class="fa-duotone fa-moped" style="
                        --fa-secondary-opacity: 1;
                        --fa-primary-color: dodgerblue;
                        --fa-secondary-color: #ffb100;
                      "></i>
                                    Giao tận nơi
                                </button>
                                <button class="type-order-btn" id="tudenlay">
                                    <i class="fa-duotone fa-box-heart" style="
                        --fa-secondary-opacity: 1;
                        --fa-primary-color: pink;
                        --fa-secondary-color: palevioletred;
                      "></i>
                                    Tự đến lấy
                                </button>
                            </div>
                        </div>
                        <div class="content-group">
                            <p class="checkout-content-label">Ngày giao hàng</p>
                            <div class="date-order"></div>
                        </div>
                        <div class="content-group chk-ship" id="giaotannoi-group">
                            <p class="checkout-content-label">Thời gian giao hàng</p>
                            <div class="delivery-time">
                                <input type="radio" name="giaongay" id="giaongay" class="radio" />
                                <label for="giaongay">Giao ngay khi xong</label>
                            </div>
                            <div class="delivery-time">
                                <input type="radio" name="giaongay" id="deliverytime" class="radio" />
                                <label for="deliverytime">Giao vào giờ</label>
                                <select class="choise-time">
                                    <option data-hours="08" value="08:00" selected="selected">
                                        08:00 - 09:00
                                    </option>

                                    <option data-hours="09" value="09:00">09:00 - 10:00</option>

                                    <option data-hours="10" value="10:00">10:00 - 11:00</option>

                                    <option data-hours="11" value="11:00">11:00 - 12:00</option>

                                    <option data-hours="12" value="12:00">12:00 - 13:00</option>

                                    <option data-hours="13" value="13:00">13:00 - 14:00</option>

                                    <option data-hours="14" value="14:00">14:00 - 15:00</option>

                                    <option data-hours="15" value="15:00">15:00 - 16:00</option>

                                    <option data-hours="16" value="16:00">16:00 - 17:00</option>

                                    <option data-hours="17" value="17:00">17:00 - 18:00</option>

                                    <option data-hours="18" value="18:00">18:00 - 19:00</option>

                                    <option data-hours="19" value="19:00">19:00 - 20:00</option>

                                    <option data-hours="20" value="20:00">20:00 - 21:00</option>

                                    <option data-hours="21" value="21:00">21:00 - 22:00</option>
                                </select>
                            </div>
                        </div>
                        <div class="content-group" id="tudenlay-group">
                            <p class="checkout-content-label">Lấy hàng tại chi nhánh</p>
                            <div class="delivery-time">
                                <input type="radio" name="chinhanh" id="chinhanh-1" class="radio" />
                                <label for="chinhanh-1">273 An Dương Vương, Phường 3, Quận 5</label>
                            </div>
                            <div class="delivery-time">
                                <input type="radio" name="chinhanh" id="chinhanh-2" class="radio" />
                                <label for="chinhanh-2">04 Tôn Đức Thắng, Phường Bến Nghé, Quận 1</label>
                            </div>
                        </div>
                        <div class="content-group">
                            <p class="checkout-content-label">Ghi chú đơn hàng</p>
                            <textarea type="text" class="note-order" placeholder="Nhập ghi chú"></textarea>
                        </div>
                    </div>
                </div>
                <div class="checkout-row">
                    <div class="checkout-col-title">Thông tin người nhận</div>
                    <div class="checkout-col-content">
                        <div class="content-group">
                            <form action="" class="info-nhan-hang">
                                <div class="form-group">
                                    <input id="tennguoinhan" name="tennguoinhan" type="text"
                                        placeholder="Tên người nhận" class="form-control" />
                                    <span class="form-message"></span>
                                </div>
                                <div class="form-group">
                                    <input id="sdtnhan" name="sdtnhan" type="text" placeholder="Số điện thoại nhận hàng"
                                        class="form-control" />
                                    <span class="form-message"></span>
                                </div>
                                <div class="form-group">
                                    <input id="diachinhan" name="diachinhan" type="text" placeholder="Địa chỉ nhận hàng"
                                        class="form-control chk-ship" />
                                    <span class="form-message"></span>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            <div class="checkout-col-right">
                <p class="checkout-content-label">Đơn hàng</p>
                <div class="bill-total" id="list-order-checkout"></div>
                <div class="bill-payment">
                    <div class="total-bill-order"></div>
                    <div class="policy-note">
                        Bằng việc bấm vào nút “Đặt hàng”, tôi đồng ý với
                        <a href="#" target="_blank">chính sách hoạt động</a>
                        của chúng tôi.
                    </div>
                </div>
                <div class="total-checkout">
                    <div class="text">Tổng tiền</div>
                    <div class="price-bill">
                        <div class="price-final" id="checkout-cart-price-final">0</div>
                    </div>
                </div>
                <button class="complete-checkout-btn">Đặt hàng</button>
            </div>
        </main>
    </div>
    <div id="toast"></div>
    <script>
    // Tự động ẩn toast sau 3 giây
    setTimeout(function() {
        var toast = document.getElementById("toast");
        if (toast) {
            toast.classList.remove("show");
        }
    }, 3000);
    </script>
    <script src="../js/initialization.js"></script>
    <script src="../js/main.js"></script>
    <script src="../js/checkout.js"></script>
    <script src="../js/toast-message.js"></script>
</body>

</html>