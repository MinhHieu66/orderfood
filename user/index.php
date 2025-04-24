<?php
    session_start();
    require_once "../model/User_Database.php";
    require_once "../model/Category_Database.php";
    require_once "../model/Product_Database.php";
    require_once "../model/Order_Database.php";
    require_once "../model/Order_Detail_Database.php";
    require_once 'config_vnpay.php';
    $user_Database         = new User_Database;
    $order_Database        = new Order_Database;
    $order_Detail_Database = new Order_Detail_Database;

    if (isset($_SESSION["fullname"])) {
        $_SESSION['logout'] = "Bạn đã đăng xuất khỏi hệ thống!";
        unset($_SESSION["fullname"]);
    }

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

    //Thêm người dùng
    // $user_Database -> addUser($fullname, $email, $phone, $address, $password);
    if (isset($_POST["fullname"], $_POST["email"], $_POST["phone"], $_POST["address"], $_POST["password"], $_POST["password_confirmation"])) {
        $fullname              = $_POST["fullname"];
        $email                 = $_POST["email"];
        $phone                 = $_POST["phone"];
        $address               = $_POST["address"];
        $password              = $_POST["password"];
        $password_confirmation = $_POST["password_confirmation"];

        if ($password === $password_confirmation) {
            $result = $user_Database->addUser($fullname, $email, $phone, $address, $password);
            if ($result) {
                $_SESSION["fullname"] = $fullname;
                // $_SESSION['user_id']         = $result["user_id"];
                $_SESSION['success_message'] = "Đăng ký thành công! Chào mừng " . $fullname . " .";
            } else {
                $_SESSION['error_message'] = "Đăng ký thất bại! Vui lòng thử lại.";
            }
        } else {
            $_SESSION['error_message'] = "Mật khẩu và xác nhận mật khẩu không khớp!";
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
    $isFlag           = true;
    if (isset($_GET['key'])) {
        $isFlag   = false;
        $key      = $_GET['key'];
        $products = $product_Database->getProductByProductName($key);
    } elseif (isset($_GET["category_id"])) {
        $category_id = $_GET["category_id"];
        $products    = $product_Database->getCategoriesPagination($page, $perPage, $category_id);
    } else {
        $category_id = "";
        $products    = $product_Database->getCategoriesPagination($page, $perPage);
    }

    if (isset($_GET["action"]) && $_GET["action"] == "cart") {
        $id      = $_POST["product_id"];
        $product = $product_Database->getProductById($id);

        $quantity = $_POST["quantity"];
        $note     = $_POST["note"];

        if (isset($_SESSION['cart'][$id])) {
            $_SESSION['cart'][$id]['quantity'] += $quantity;
        } else {
            $_SESSION['cart'][$id] = [
                'name'     => $product["title"],
                'price'    => $product["price"],
                'quantity' => $quantity,
                'note'     => $note,
            ];
        }
    } elseif (isset($_GET["delete"]) && $_GET["delete"] == 'cart' && isset($_GET["product_id"])) {
        $id = $_GET["product_id"];
        unset($_SESSION['cart'][$id]);
    }
    // elseif ($id && isset($_SESSION['cart'][$id])) {
    //     if ($action === 'increase') {
    //         $_SESSION['cart'][$id]['quantity']++;
    //     } elseif ($action === 'decrease') {
    //         if ($_SESSION['cart'][$id]['quantity'] > 1) {
    //             $_SESSION['cart'][$id]['quantity']--;
    //         } else {
    //             unset($_SESSION['cart'][$id]); // Xoá nếu số lượng = 1 và giảm
    //         }
    //     }
    // }

    // Xử lý thanh toán
    if (isset($_GET["action"]) && $_GET["action"] == "payment") {
        $user_id  = 17;
        $email    = "thao1123@gmail.com";
        $phone    = $_POST["sdtnhan"];
        $note     = $_POST["note-order"];
        $order_id = $order_Database->save($user_id, $email, $phone, $note);
        if ($_POST["payment_method"] == "cash") {
            // save($order_id, $product_id, $price, $num)
            foreach ($_SESSION['cart'] as $index => $item) {
                $order_Detail_Database->save($order_id, $index, $item["price"], $item["quantity"]);
            }
            unset($_SESSION['cart']);
            $_SESSION['success_message'] = "Cảm ơn bạn đã thanh toán! Đơn hàng của bạn đã được xác nhận và sẽ sớm được giao đến tận tay.";
            header("location: index.php");
            exit();
        } elseif ($_POST["payment_method"] == "vnpay") {
            $total = 0;
            foreach ($_SESSION['cart'] as $item) {
                $total += ($item['quantity'] * $item["price"]);
            }
            $vnp_TxnRef   = $order_id;               //Mã giao dịch thanh toán tham chiếu của merchant
            $vnp_Amount   = $total;                  //Số tiền thanh toán
            $vnp_Locale   = "vn";                    //Ngôn ngữ chuyển hướng thanh toán
            $vnp_BankCode = "NCB";                   //Mã phương thức thanh toán
            $vnp_IpAddr   = $_SERVER['REMOTE_ADDR']; //IP Khách hàng thanh toán

            $inputData = [
                "vnp_Version"    => "2.1.0",
                "vnp_TmnCode"    => $vnp_TmnCode,
                "vnp_Amount"     => $vnp_Amount * 100,
                "vnp_Command"    => "pay",
                "vnp_CreateDate" => date('YmdHis'),
                "vnp_CurrCode"   => "VND",
                "vnp_IpAddr"     => $vnp_IpAddr,
                "vnp_Locale"     => $vnp_Locale,
                "vnp_OrderInfo"  => "Thanh toan GD:" . $vnp_TxnRef,
                "vnp_OrderType"  => "other",
                "vnp_ReturnUrl"  => $vnp_Returnurl,
                "vnp_TxnRef"     => $vnp_TxnRef,
                "vnp_ExpireDate" => $expire,
            ];

            if (isset($vnp_BankCode) && $vnp_BankCode != "") {
                $inputData['vnp_BankCode'] = $vnp_BankCode;
            }
            // die();

            ksort($inputData);
            $query    = "";
            $i        = 0;
            $hashdata = "";
            foreach ($inputData as $key => $value) {
                if ($i == 1) {
                    $hashdata .= '&' . urlencode($key) . "=" . urlencode($value);
                } else {
                    $hashdata .= urlencode($key) . "=" . urlencode($value);
                    $i = 1;
                }
                $query .= urlencode($key) . "=" . urlencode($value) . '&';
            }

            $vnp_Url = $vnp_Url . "?" . $query;

            if (isset($vnp_HashSecret)) {
                $vnpSecureHash = hash_hmac('sha512', $hashdata, $vnp_HashSecret);
                $vnp_Url .= 'vnp_SecureHash=' . $vnpSecureHash;
            }

            // save($order_id, $product_id, $price, $num)
            foreach ($_SESSION['cart'] as $index => $item) {
                $order_Detail_Database->save($order_id, $index, $item["price"], $item["quantity"]);
            }
            unset($_SESSION['cart']);
            $_SESSION['success_message'] = "Cảm ơn bạn đã thanh toán! Đơn hàng của bạn đã được xác nhận và sẽ sớm được giao đến tận tay.";
            header('Location: ' . $vnp_Url);
            die();
        } elseif ($_POST["payment_method"] == "momo") {
            $total = 0;
            foreach ($_SESSION['cart'] as $item) {
                $total += ($item['quantity'] * $item["price"]);
            }
            $_SESSION['total'] = $total;
            header("location: xulythanhtoanmomo.php");
        }
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

    <div class="modal product-detail" id="product-detail">
        <form action="index.php?action=cart" method="POST">
            <!-- Input ẩn để truyền id món ăn -->
            <input type="hidden" id="product_id" name="product_id" value="">

            <button class="modal-close close-popup">
                <i class="fa-thin fa-xmark"></i>
            </button>
            <div class="modal-container mdl-cnt" id="product-detail-content">
                <div class="modal-header">
                    <img class="product-image" src="../assets/img/products/nam-dui-ga-chay-toi.jpeg" alt="">
                </div>
                <div class="modal-body">
                    <h2 class="product-title">Nấm đùi gà xào cháy tỏi</h2>
                    <div class="product-control">
                        <div class="priceBox">
                            <span class="current-price" id="price">200.000&nbsp;₫</span>
                        </div>
                        <div class="buttons_added">
                            <input class="minus is-form" type="button" value="-" onclick="decreasingNumber(this)">
                            <input class="input-qty" max="100" min="1" name="quantity" type="number" value="1">
                            <input class="plus is-form" type="button" value="+" onclick="increasingNumber(this)">
                        </div>
                    </div>
                    <p class="product-description">Một Món chay ngon miệng với nấm đùi gà thái chân hương, xào săn với
                        lửa
                        và thật nhiều tỏi băm, nêm nếm với mắm và nước tương chay, món ngon đưa cơm và rất dễ ăn cả cho
                        người lớn và trẻ nhỏ.</p>
                </div>
                <div class="notebox">
                    <p class="notebox-title">Ghi chú</p>
                    <textarea name="note" class="text-note" id="popup-detail-note"
                        placeholder="Nhập thông tin cần lưu ý..."></textarea>
                </div>
                <div class="modal-footer">
                    <div class="price-total">
                        <span class="thanhtien">Thành tiền</span>
                        <span class="price" id="total">200.000&nbsp;₫</span>
                    </div>
                    <div class="modal-footer-control">
                        <button class="button-dathangngay" data-product="1">Đặt hàng ngay</button>
                        <button type="submit" class="button-dat" id="add-cart" onclick="animationCart()"><i
                                class="fa-light fa-basket-shopping"></i></button>
                    </div>
                </div>
            </div>
        </form>

    </div>
    <!-- <div class="modal product-detail">
        <button class="modal-close close-popup">
            <i class="fa-thin fa-xmark"></i>
        </button>
        <div class="modal-container mdl-cnt" id="product-detail-content"></div>
    </div> -->
    <div class="modal signup-login">
        <div class="modal-container">
            <button class="form-close" onclick="closeModal()">
                <i class="fa-regular fa-xmark"></i>
            </button>
            <div class="forms mdl-cnt">
                <div class="form-content sign-up">
                    <h3 class="form-title">Đăng ký tài khoản</h3>
                    <form action="index.php" class="signup-form" method="POST">
                        <div class="form-group">
                            <label for="fullname" class="form-label">Tên đầy đủ</label>
                            <input id="fullname" name="fullname" type="text" placeholder="VD: Nhật Sinh"
                                class="form-control" />
                            <span class="form-message-name form-message"></span>
                        </div>
                        <div class="form-group">
                            <label for="email" class="form-label">Email</label>
                            <input id="email" name="email" type="email" placeholder="Nhập email" class="form-control" />
                            <span class="form-message-email form-message"></span>
                        </div>
                        <div class="form-group">
                            <label for="phone" class="form-label">Số điện thoại</label>
                            <input id="phone" name="phone" type="text" placeholder="Nhập số điện thoại"
                                class="form-control" />
                            <span class="form-message-phone form-message"></span>
                        </div>
                        <div class="form-group">
                            <label for="address" class="form-label">Địa chỉ</label>
                            <input id="address" name="address" type="text" placeholder="Nhập địa chỉ"
                                class="form-control" />
                            <span class="form-message-address form-message"></span>
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
                        <button class="form-submit" id="" type="submit">Đăng ký</button>
                    </form>
                    <p class="change-login">
                        Bạn đã có tài khoản ?
                        <a href="javascript:;" class="login-link">Đăng nhập ngay</a>
                    </p>
                </div>

                <div class="form-content login open">
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
                <div class="gio-hang-trong"
                    style="display:                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                     <?php echo(isset($_SESSION["cart"])) ? 'none' : 'block' ?>;">
                    <i class="fa-thin fa-cart-xmark"></i>
                    <p>Không có sản phẩm nào trong giỏ hàng của bạn</p>
                </div>
                <ul class="cart-list">
                    <?php if (isset($_SESSION['cart'])): ?>
<?php $total = 0; ?>
<?php foreach ($_SESSION['cart'] as $key => $item): ?>
<?php $total += ($item["price"] * $item["quantity"])?>
                    <li class="cart-item" data-id="1">
                        <div class="cart-item-info">
                            <p class="cart-item-title">
                                <?php echo $item["name"] ?>
                            </p>
                            <span class="cart-item-price price" data-price="200000">
                                <?php echo number_format($item["price"], 0, ",", ".") ?>&nbsp;₫
                            </span>
                        </div>
                        <p class="product-note"><i
                                class="fa-light fa-pencil"></i><span><?php echo($item["note"] == "") ? "Không có ghi chú" : $item["note"] ?></span>
                        </p>
                        <div class="cart-item-control">
                            <a href="index.php?delete=cart&product_id=<?php echo $key ?>">
                                <button class="cart-item-delete">Xóa</button>
                            </a>
                            <!-- <button class="cart-item-delete" onclick="deleteCartItem(1,this)">Xóa</button> -->
                            <div class="buttons_added">
                                <input class="minus is-form" type="button" value="-"
                                    onclick="updateQuantity(<?php echo $key ?>, 'decrease')">
                                <input id="qty-<?php echo $key ?>" class="input-qty" max="100" min="1" name=""
                                    type="number" value="<?php echo $item["quantity"] ?>">
                                <input class="plus is-form" type="button" value="+"
                                    onclick="updateQuantity(<?php echo $key ?>, 'increase')">
                            </div>
                        </div>
                    </li>
                    <?php endforeach?>
<?php endif?>
                </ul>
            </div>
            <div class="cart-footer">
                <div class="cart-total-price">
                    <p class="text-tt">Tổng tiền:</p>
                    <!-- <p class="text-price">&nbsp;₫</p> -->
                    <p class="total" style="color: red;"><?php echo number_format($total, 0, ",", ".") ?>&nbsp;₫</p>
                </div>
                <div class="cart-footer-payment">
                    <button class="them-mon">
                        <i class="fa-regular fa-plus"></i> Thêm món
                    </button>
                    <button class="thanh-toan">Thanh toán</button>
                </div>
            </div>
        </div>
    </div>
    <!-- <div class="modal-cart">
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
    </div> -->
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
    <form action="index.php?action=payment" method="POST">
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
                            <!-- <div class="content-group">
                            <p class="checkout-content-label">Ngày giao hàng</p>
                            <div class="date-order"></div>
                        </div> -->
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
                                <textarea type="text" class="note-order" name="note-order"
                                    placeholder="Nhập ghi chú"></textarea>
                            </div>
                        </div>
                    </div>
                    <div class="checkout-row">
                        <div class="checkout-col-title">Thông tin người nhận</div>
                        <div class="checkout-col-content">
                            <div class="content-group">
                                <!-- <form action="" class="info-nhan-hang"> -->
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
                                <!-- </form> -->
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
                            Bằng việc bấm vào nút “Thanh toán”, tôi đồng ý với
                            <a href="#" target="_blank">chính sách hoạt động</a>
                            của chúng tôi.
                        </div>
                    </div>
                    <!-- Danh sách món hàng -->
                    <ul class="cart-list">
                        <?php if (isset($_SESSION['cart'])): ?>
<?php foreach ($_SESSION['cart'] as $item): ?>
                        <li class="cart-item" data-id="1">
                            <div class="cart-item-info">
                                <p class="cart-item-title">
                                    <?php echo $item["name"] ?> </p>
                                <span class="cart-item-price price" data-price="200000">
                                    <?php echo number_format($item["price"], 0, ",", ".") ?>&nbsp;₫
                                </span>
                            </div>
                            <p class="product-note"><i
                                    class="fa-light fa-pencil"></i><span><?php echo($item["note"] == "") ? "Không có ghi chú" : $item["note"] ?></span>
                            </p>
                            <div class="cart-item-control">
                                <div class="buttons_added">
                                    <input id="qty-1" class="input-qty" max="100" min="1" name="" type="number"
                                        value="<?php echo $item["quantity"] ?>">
                                </div>
                            </div>
                        </li>
                        <?php endforeach?>
<?php endif?>
                    </ul>
                    <div style="margin-bottom: 1rem; margin-top: 1rem;">
                        <label for="payment-method"><strong>Chọn hình thức thanh toán:</strong></label>
                        <select id="payment-method" name="payment_method" class="form-select"
                            style="width: 100%; padding: 0.5rem; margin-top: 0.5rem; border-radius: 6px;">
                            <option value="cash">Tiền mặt</option>
                            <option value="momo">Momo</option>
                            <option value="vnpay">VNPAY</option>
                        </select>
                    </div>
                    <!-- <form class="" method="POST" target="_blank" enctype="application/x-www-form-urlencoded"
                        action="demo.php">
                        <input type="hidden" name="tongtien-vnd" value="<?php echo $total ?>">
                        <input type="submit" name="momo" value="Thanh toán MOMO QRcode" class="btn btn-danger">
                    </form> -->
                    <div class="total-checkout">
                        <div class="text">Tổng tiền</div>
                        <div class="price-bill">
                            <div class="price-final" id="checkout-cart-price-final">
                                <?php echo number_format($total, 0, ",", ".") ?>&nbsp;₫</div>
                        </div>
                    </div>
                    <button type="submit" class="complete-checkout-btn">Thanh toán</button>
                </div>
            </main>
        </div>
    </form>
    <div id="toast"></div>
    <script src="../js/initialization.js"></script>
    <script src="../js/main.js"></script>
    <script src="../js/checkout.js"></script>
    <script src="../js/toast-message.js"></script>
    <script>
    // Tự động ẩn toast sau 3 giây
    setTimeout(function() {
        var toast = document.getElementById("toast");
        if (toast) {
            toast.classList.remove("show");
        }
    }, 3000);
    </script>
    <script>
    // Chuyen doi qua lai SignUp & Login
    let signup = document.querySelector(".signup-link");
    let login = document.querySelector(".login-link");
    let container = document.querySelector(".signup-login .modal-container");
    console.log(container);
    login.addEventListener("click", () => {
        container.classList.add("active");
    });

    signup.addEventListener("click", () => {
        container.classList.remove("active");
    });

    let signupbtn = document.getElementById("signup");
    let loginbtn = document.getElementById("login");
    let formsg = document.querySelector(".modal.signup-login");
    signupbtn.addEventListener("click", () => {
        formsg.classList.add("open");
        container.classList.remove("active");
        body.style.overflow = "hidden";
    });

    loginbtn.addEventListener("click", () => {
        document.querySelector(".form-message-check-login").innerHTML = "";
        formsg.classList.add("open");
        container.classList.add("active");
        body.style.overflow = "hidden";
    });

    let modalContainer = document.querySelectorAll(".modal");

    function closeModal() {
        modalContainer.forEach((item) => {
            item.classList.remove("open");
        });
        body.style.overflow = "auto";
    }
    </script>
    <script>
    // Chi tiết sản phẩm
    document.addEventListener("DOMContentLoaded", function() {
        const productDetail = document.getElementById("product-detail");
        const listProduct = document.querySelectorAll(".col-product");
        listProduct.forEach((element) => {
            element.addEventListener("click", function() {
                // productDetail.classList.add("open");
                const productId = element.getAttribute('data-id');
                fetch(`../model/ajax_product_detail.php?id=${productId}`)
                    .then(res => res.json())
                    .then(data => {
                        if (data.error) {
                            alert(data.error);
                        } else {
                            productDetail.querySelector(".product-title").innerHTML = data
                                .title;
                            productDetail.querySelector(".product-description").innerHTML =
                                data
                                .description;
                            productDetail.querySelector(".product-image").src =
                                "../assets/img/products/" + data.image; //200.000&nbsp;₫
                            let price = data.price;
                            let formatted = price.toLocaleString('vi-VN');

                            productDetail.querySelector(".current-price").innerHTML =
                                formatted + "&nbsp;₫";

                            let quantity = productDetail.querySelector(".input-qty").value;
                            let total = parseInt(quantity) * parseInt(price);

                            productDetail.querySelector(".price").innerHTML =
                                total.toLocaleString('vi-VN') + "&nbsp;₫";

                            productDetail.querySelector("#product_id").value = data.id;

                            productDetail.classList.add("open");
                        }
                    })
                    .catch(error => {
                        console.error('Error fetching product:', error);
                        alert('Lỗi khi lấy dữ liệu sản phẩm');
                    });
            });
        });

    });
    </script>
    <script>
    // Tăng giảm số lượng trong giỏ hàng
    function updateQuantity(productId, action) {
        fetch('../model/update-cart.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded'
                },
                body: `id=${productId}&action=${action}`
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    document.getElementById(`qty-${productId}`).value = data.quantity;
                    document.querySelector(`.total`).innerHTML = data.total.toLocaleString('vi-VN') + "&nbsp;₫";;
                }
            });
    }
    </script>

    <!-- Xử lý đăng ký  -->
    <script>
    document.addEventListener("DOMContentLoaded", () => {
        const signupForm = document.querySelector(".signup-form");
        signupForm.addEventListener("submit", (e) => {
            e.preventDefault();

            let fullNameUser = document.getElementById("fullname").value.trim();
            let phoneUser = document.getElementById("phone").value.trim();
            let emailUser = document.getElementById("email").value.trim();
            let passwordUser = document.getElementById("password").value.trim();
            let passwordConfirmation = document.getElementById("password_confirmation").value.trim();
            let addressUser = document.getElementById("address").value.trim();

            // Check validate
            let formMessageName = document.querySelector(".form-message-name");
            let formMessagePhone = document.querySelector(".form-message-phone");
            let formMessagePassword = document.querySelector(".form-message-password");
            let formMessagePasswordConfirmation = document.querySelector(
                ".form-message-password-confi");
            let formMessageEmail = document.querySelector(".form-message-email");
            let formMessageAddress = document.querySelector(".form-message-address");
            let isValid = true;

            if (fullNameUser.length === 0) {
                formMessageName.innerHTML = "Vui lòng nhập họ và tên";
                isValid = false;
            } else if (fullNameUser.length < 3) {
                formMessageName.innerHTML = "Vui lòng nhập họ và tên lớn hơn 3 kí tự";
                isValid = false;
            } else {
                formMessageName.innerHTML = "";
            }

            if (phoneUser.length === 0) {
                formMessagePhone.innerHTML = "Vui lòng nhập vào số điện thoại";
                isValid = false;
            } else if (!/^(0\d{9,11}|\+84\d{9,10})$/.test(phoneUser)) {
                formMessagePhone.innerHTML = "Vui lòng nhập vào số điện thoại 10 số";
                isValid = false;
            } else {
                formMessagePhone.innerHTML = "";
            }

            if (emailUser.length === 0) {
                formMessageEmail.innerHTML = "Vui lòng nhập email";
                isValid = false;
            } else if (!/^\w+@[a-zA-Z_]+?\.[a-zA-Z]{2,3}$/.test(emailUser)) {
                formMessageEmail.innerHTML = "Vui lòng nhập đúng định dạng email";
                isValid = false;
            } else {
                formMessageEmail.innerHTML = "";
            }

            if (addressUser.length === 0) {
                formMessageAddress.innerHTML = "Vui lòng nhập địa chỉ";
                isValid = false;
            } else {
                formMessageAddress.innerHTML = "";
            }

            if (passwordUser.length === 0) {
                formMessagePassword.innerHTML = "Vui lòng nhập mật khẩu";
                isValid = false;
            } else if (passwordUser.length < 6) {
                formMessagePassword.innerHTML = "Vui lòng nhập mật khẩu lớn hơn 6 kí tự";
                isValid = false;
            } else {
                formMessagePassword.innerHTML = "";
            }

            if (passwordConfirmation.length === 0) {
                formMessagePasswordConfirmation.innerHTML = "Vui lòng nhập lại mật khẩu";
                isValid = false;
            } else if (passwordUser !== passwordConfirmation) {
                formMessagePasswordConfirmation.innerHTML = "Mật khẩu và xác nhận mật khẩu không khớp";
                isValid = false;
            } else {
                formMessagePasswordConfirmation.innerHTML = "";
            }

            if (isValid) {
                signupForm.submit();
            }
        });
    });
    </script>
</body>

</html>