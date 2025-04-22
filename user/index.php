<?php
    session_start();
    require_once "../model/User_Database.php";
    require_once "../model/Category_Database.php";
    require_once "../model/Product_Database.php";

    if (isset($_SESSION["fullname"], $_GET['action']) && $_GET['action'] == "exit") {
        $_SESSION['logout'] = "Bạn đã đăng xuất khỏi hệ thống!";
        unset($_SESSION["fullname"]);
    }
    $user_Database = new User_Database;
    if (isset($_POST["password"]) && isset($_POST["phone"])) {
        $password = $_POST["password"];
        $phone    = $_POST["phone"];
        $user     = $user_Database->login($phone, $password);
        if (isset($user) && $user["role_id"] == 2) {
            $_SESSION["fnullame"]        = $user["user_name"];
            $_SESSION['user_id']         = $user["user_id"];
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

    //Lay thong tin user theo id
    // $user_id = $_GET['user_id'];
    $user_Database = new User_Database();
    if (isset($_SESSION['user_id'])) {
        $user = $user_Database->getUserById($_SESSION['user_id']);

    }

    //Cap nhat thong tin ca nhan
    if (isset($_POST["action"]) && $_POST["action"] == "changeInfor") {

        $name             = trim($_POST['infoname']);
        $phone            = trim($_POST['infophone']);
        $email            = trim($_POST['infoemail']);
        $address          = trim($_POST['infoaddress']);
        $current_password = trim($_POST['current_password']);
        $new_password     = trim($_POST['new_password']);
        $confirm_password = trim($_POST['confirm_password']);
        $error            = "";
        //kiem tra thong tin bat buoc
        if (empty($name) || empty($phone) || empty($email) || empty($address) || empty($current_password) || empty($new_password) || empty($confirm_password)) {
            $error .= "<p>Vui lòng nhập đầy đủ thông tin cá nhân.</p>";
        }

        // Kiểm tra định dạng số điện thoại
        if (! preg_match('/^(?:\+84|0)[1-9][0-9]{8}$/', $phone)) {
            $error .= "<p>Số điện thoại không hợp lệ.</p>";
        }

        //kiểm tra định dạng email
        // if (! preg_match('/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/', $email)) {
        //     $error .= "<p>Email không hợp lê.</p>";
        // }

        if ($error == "") {
            // Kiểm tra và cập nhật mật khẩu
            $user = $user_Database->getUserById($_SESSION['user_id']);
            if ($current_password !== $user['user_password']) {
                $_SESSION['reset-password'] = "Mật khẩu hiện tại không chính xác.";
            } elseif ($new_password !== $confirm_password) {
                $_SESSION['reset-password'] = "Mật khẩu mới và xác nhận không khớp.";
            } elseif ($current_password == $user['user_password'] && $new_password == $confirm_password) {
                $user_id = $user["user_id"];
                $user_Database->updateUsers($user_id, $name, $phone, $email, $address, $new_password);
                $_SESSION['reset-password'] = "Cập nhật thông tin thành công!";
            }
        } else {
            $_SESSION["changeInfor"] = $error;
        }
    }

    // //doi mat khau
    // if (isset($_POST["current_password"]) && isset($_SESSION['user_id'])) {
    //     $password     = $_POST["current_password"];
    //     $new_password = $_POST["new_password"];
    //     $re_password  = $_POST["re_password"];
    //     $user_id      = $_SESSION['user_id'];

    //     if (empty($password) || empty($new_password) || empty($re_password)) {
    //         $_SESSION['reset-password'] = "Vui lòng nhập đầy đủ thông tin";
    //     } elseif ($password !== $user['user_password']) {
    //         $_SESSION['reset-password'] = "Mật khẩu hiện tại không chính xác.";
    //     } elseif ($new_password !== $re_password) {
    //         $_SESSION['reset-password'] = "Mật khẩu mới và xác nhận không khớp.";
    //     } elseif ($password == $user['user_password'] && $new_password == $re_password) {
    //         $user_id = $user["user_id"];
    //         $user_Database->reSetPassword($user_id, $new_password);
    //         $_SESSION['reset-password'] = "Đổi mật khẩu thành công!";
    //     }

    // }

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>GHH</title>
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
        } elseif (isset($_SESSION["changeInfor"])) {
            echo '<div id="toast" class="toast show">' . $_SESSION['changeInfor'] . '</div>';
            unset($_SESSION['changeInfor']);
        } elseif (isset($_SESSION['reset-password'])) {
            echo '<div id="toast" class="toast show">' . $_SESSION['reset-password'] . '</div>';
            unset($_SESSION['reset-password']);
        }
        //Hien
        // elseif (isset($_SESSION['reset-password'])) {
        //     echo '<div id="toast" class="toast show">' . $_SESSION['reset-password'] . '</div>';
        //     unset($_SESSION['reset-password']);
        // } elseif (isset($_SESSION['update_phone'])) {
        //     echo '<div id="toast" class="toast show">' . $_SESSION['update_phone'] . '</div>';
        //     unset($_SESSION['update_phone']);
        // }
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
                    <form action="index.php" class="signup-form" method="POST">
                        <div class="form-group">
                            <label for="fullname" class="form-label">Tên đầy đủ</label>
                            <input id="fullname" name="fullname" type="text" placeholder="VD: Nhật Sinh"
                                class="form-control"  />
                            <span class="form-message-name form-message"></span>
                        </div>
                        <div class="form-group">
                            <label for="email" class="form-label">Email</label>
                            <input id="email" name="email" type="email" placeholder="Nhập email" class="form-control"
                                 />
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
                                class="form-control"  />
                            <span class="form-message-address form-message"></span>
                        </div>
                        <div class="form-group">
                            <label for="password" class="form-label">Mật khẩu</label>
                            <input id="password" name="password" type="password" placeholder="Nhập mật khẩu"
                                class="form-control"  />
                            <span class="form-message-password form-message"></span>
                        </div>
                        <div class="form-group">
                            <label for="password_confirmation" class="form-label">Nhập lại mật khẩu</label>
                            <input id="password_confirmation" name="password_confirmation"
                                placeholder="Nhập lại mật khẩu" type="password" class="form-control"  />
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