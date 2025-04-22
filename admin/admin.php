<?php
    session_start();
    require_once "../model/Product_Database.php";
    require_once "../model/Category_Database.php";
    require_once "../model/User_Database.php";

    $category_Database = new Category_Database();
    $product_Database  = new Product_Database();
    $user_Database     = new User_Database();
    // $products          = $product_Database->getAllProducts();'
    $page     = isset($_GET["page"]) ? $_GET["page"] : 1;
    $perPage  = 5;
    $products = $product_Database->getCategoriesPagination($page, $perPage);
    $check    = isset($_GET["check"]) ? $_GET["check"] : "";

    //Lay danh sach loai mon an
    $category_Database = new Category_Database();
    $categories        = $category_Database->getAllCategories();

    //Cập nhật sản phẩm
    if (isset($_GET["check"]) && $_GET['check'] == "product" && isset($_POST["update"]) && $_POST["update"] != "") {
        $title       = $_POST["title"];
        $price       = $_POST["price"];
        $description = $_POST["description"];
        $category_id = $_POST["category_id"];
        $id          = $_POST["update"];
        $image       = basename($_FILES["image"]["name"]);
        $image_old   = $_POST["image-old"];

        $target_dir  = "../assets/img/products/";
        $target_file = $target_dir . $image;
        unlink($target_dir . $image_old);
        move_uploaded_file($_FILES["image"]["tmp_name"], $target_file);

        $product_Database->upadteProduct($title, $price, $image, $description, $category_id, $status = 1, $id);
        $_SESSION['update-product'] = "Sửa món ăn thành công!";
    } elseif (isset($_GET["check"]) && $_GET['check'] == "product" && isset($_GET["delete"])) {
        //Xoa san pham
        $id          = $_GET["delete"];
        $image       = $_GET["image"];
        $target_dir  = "../assets/img/products/";
        $target_file = $target_dir . $image;
        unlink($target_file);
        $product_Database->deleteProduct($id);
        $_SESSION['delete-product'] = "Xóa món ăn thành công!";
    } elseif (isset($_GET["check"]) && $_GET['check'] == "product" && isset($_POST["add"])) {
        // Them san pham
        $title       = $_POST["title"];
        $price       = $_POST["price"];
        $description = $_POST["description"];
        $category_id = $_POST["category_id"];
        $image       = basename($_FILES["image"]["name"]);

        $target_dir  = "../assets/img/products/";
        $target_file = $target_dir . $image;
        move_uploaded_file($_FILES["image"]["tmp_name"], $target_file);

        $product_Database->addProduct($title, $price, $image, $description, $category_id, $status = 1);
        $_SESSION['add-product'] = "Thêm món ăn thành công!";
    }

    //Cập nhật sản phẩm
    if (isset($_GET["check"]) && $_GET['check'] == "account" && isset($_POST["update"]) && $_POST["update"] != "") {
        $fullname = $_POST['fullname'];
        $phone    = $_POST['phone'];
        $email    = $_POST['email'];
        $address  = $_POST['address'];
        $password = $_POST['password'];
        $id       = $_POST["update"];
        if (isset($_POST["user-status"]) && $_POST["user-status"] == "on") {
            $status = 1;
        } else {
            $status = 0;
        }

        $result = $user_Database->upadateUser($fullname, $phone, $email, $address, $password, $status, $id);
        if ($result) {
            $_SESSION['success_message'] = "Sửa khách hàng thành công!";
        } else {
            $_SESSION['error_message'] = "Sửa khách hàng thất bại!";
        }
    } elseif (isset($_GET['check']) && $_GET['check'] == 'account' && $_SERVER['REQUEST_METHOD'] == 'POST') {
        //Them người dùng
        $fullname = $_POST['fullname'];
        $phone    = $_POST['phone'];
        $email    = $_POST['email'];
        $address  = $_POST['address'];
        $password = $_POST['password'];
        if (isset($_POST["user-status"]) && $_POST["user-status"] == "on") {
            $status = 1;
        } else {
            $status = 0;
        }

        // $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        $result = $user_Database->addUser($fullname, $email, $phone, $address, $password, $status);
        if ($result) {
            $_SESSION['success_message'] = "Thêm khách hàng thành công!";
        } else {
            $_SESSION['error_message'] = "Thêm khách hàng thất bại!";
        }

        header("Location: admin.php?check=account");
        exit();
    } elseif (isset($_GET["check"]) && $_GET['check'] == "account" && isset($_GET["delete"])) {
        //Xoa san khách hàng
        $id = $_GET["delete"];

        $user_Database->deleteUserById($id);
        $_SESSION['delete-user'] = "Xóa khách hàng thành công!";
        header("Location: admin.php?check=account");
        exit();
    }

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href='../assets/img/favicon.png' rel='icon' type='image/x-icon' />
    <link rel="stylesheet" href="../assets/css/admin.css">
    <link rel="stylesheet" href="../assets/css/toast-message.css">
    <link href="../assets/font/font-awesome-pro-v6-6.2.0/css/all.min.css" rel="stylesheet" type="text/css" />
    <link rel="stylesheet" href="../assets/css/admin-responsive.css">
    <title>Quản lý cửa hàng</title>
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
        } elseif (isset($_SESSION['add-product'])) {
            echo '<div id="toast" class="toast show">' . $_SESSION['add-product'] . '</div>';
            unset($_SESSION['add-product']);
        } elseif (isset($_SESSION['delete-product'])) {
            echo '<div id="toast" class="toast show">' . $_SESSION['delete-product'] . '</div>';
            unset($_SESSION['delete-product']);
        } elseif (isset($_SESSION['update-product'])) {
            echo '<div id="toast" class="toast show">' . $_SESSION['update-product'] . '</div>';
            unset($_SESSION['update-product']);
        } elseif (isset($_SESSION['delete-user'])) {
            echo '<div id="toast" class="toast show">' . $_SESSION['delete-user'] . '</div>';
            unset($_SESSION['delete-user']);
        }

    ?>
    <header class="header">
        <button class="menu-icon-btn">
            <div class="menu-icon">
                <i class="fa-regular fa-bars"></i>
            </div>
        </button>
    </header>
    <div class="container">
        <aside class="sidebar open">
            <div class="top-sidebar">
                <a href="#" class="channel-logo"><img src="../assets/img/favicon.png" alt="Channel Logo"></a>
                <div class="hidden-sidebar your-channel"><img src="assets/img/admin/vy-food-title.png"
                        style="height: 30px;" alt="">
                </div>
            </div>
            <div class="middle-sidebar">
                <ul class="sidebar-list">
                    <li
                        class="sidebar-list-item tab-content                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                              <?php echo($check == "home" || $check == "") ? "active" : "" ?>">
                        <a href="admin.php?check=home" class="sidebar-link">
                            <div class="sidebar-icon"><i class="fa-light fa-house"></i></div>
                            <div class="hidden-sidebar">Trang tổng quan</div>
                        </a>
                    </li>
                    <li
                        class="sidebar-list-item tab-content                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                    <?php echo($check == "product") ? "active" : "" ?>">
                        <a href="admin.php?check=product" class="sidebar-link">
                            <div class="sidebar-icon"><i class="fa-light fa-pot-food"></i></div>
                            <div class="hidden-sidebar">Sản phẩm</div>
                        </a>
                    </li>
                    <li
                        class="sidebar-list-item tab-content                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                 <?php echo($check == "account") ? "active" : "" ?>">
                        <a href="admin.php?check=account" class="sidebar-link">
                            <div class="sidebar-icon"><i class="fa-light fa-users"></i></div>
                            <div class="hidden-sidebar">Khách hàng</div>
                        </a>
                    </li>
                    <li class="sidebar-list-item tab-content<?php echo($check == "order") ? "active" : "" ?>">
                        <a href="admin.php?check=order" class="sidebar-link">
                            <div class="sidebar-icon"><i class="fa-light fa-basket-shopping"></i></div>
                            <div class="hidden-sidebar">Đơn hàng</div>
                        </a>
                    </li>
                    <li
                        class="sidebar-list-item tab-content                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                         <?php echo($check == "report") ? "active" : "" ?>">
                        <a href="admin.php?check=report" class="sidebar-link">
                            <div class="sidebar-icon"><i class="fa-light fa-chart-simple"></i></div>
                            <div class="hidden-sidebar">Thống kê</div>
                        </a>
                    </li>
                </ul>
            </div>
            <div class="bottom-sidebar">
                <ul class="sidebar-list">
                    <li class="sidebar-list-item user-logout">
                        <a href="/" class="sidebar-link">
                            <div class="sidebar-icon"><i class="fa-thin fa-circle-chevron-left"></i></div>
                            <div class="hidden-sidebar">Trang chủ</div>
                        </a>
                    </li>
                    <li class="sidebar-list-item user-logout">
                        <a href="/" class="sidebar-link">
                            <div class="sidebar-icon"><i class="fa-light fa-circle-user"></i></i></div>
                            <div class="hidden-sidebar">
                                <?php echo $fullname = isset($_SESSION['fullname']) ? $_SESSION['fullname'] : "" ?>
                            </div>
                        </a>
                    </li>
                    <li class="sidebar-list-item user-logout">
                        <a href="../user/index.php" class="sidebar-link">
                            <div class="sidebar-icon"><i class="fa-light fa-arrow-right-from-bracket"></i></div>
                            <div class="hidden-sidebar">Đăng xuất</div>
                        </a>
                    </li>
                </ul>
            </div>
        </aside>
        <main class="content">

            <?php if ($check == "home" || $check == ""): ?>
            <!-- Home -->
            <?php require_once "home.php"?>
<?php elseif ($check == "product"): ?>
            <!-- Product  -->
            <?php require_once "product.php"?>
<?php elseif ($check == "account"): ?>
            <!-- Account  -->
            <?php require_once "account.php"?>
<?php elseif ($check == "order"): ?>
            <!-- Order  -->
            <?php require_once "order.php"?>
<?php elseif ($check == "report"): ?>
            <!-- Report -->
            <?php require_once "report.php"?>
<?php endif?>
        </main>
    </div>

    <div class="modal add-product">
        <div class="modal-container">
            <h3 class="modal-container-title add-product-e">THÊM MỚI SẢN PHẨM</h3>
            <h3 class="modal-container-title edit-product-e">CHỈNH SỬA SẢN PHẨM</h3>
            <button class="modal-close product-form"><i class="fa-regular fa-xmark"></i></button>
            <div class="modal-content">
                <form action="admin.php?check=product" method="POST" class="add-product-form"
                    enctype="multipart/form-data">
                    <div class="modal-content-left">
                        <img src="../assets/img/blank-image.png" alt="" class="upload-image-preview">
                        <input type="hidden" name="image-old" id="image-old" value="">
                        <div class="form-group file">
                            <label for="up-hinh-anh" class="form-label-file"><i
                                    class="fa-regular fa-cloud-arrow-up"></i>Chọn hình ảnh</label>
                            <input accept="image/jpeg, image/png, image/jpg" id="up-hinh-anh" name="image" value=""
                                type="file" class="form-control" onchange="uploadImage(this)">
                        </div>
                    </div>
                    <div class="modal-content-right">
                        <div class="form-group">
                            <label for="title" class="form-label">Tên món</label>
                            <input id="title" name="title" type="text" placeholder="Nhập tên món" class="form-control">
                            <span class="form-message"></span>
                        </div>
                        <div class="form-group">
                            <label for="category_id" class="form-label">Chọn món</label>
                            <select name="category_id" id="category_id">
                                <?php foreach ($categories as $category): ?>
                                <option value="<?php echo $category["id"] ?>"><?php echo $category["name"] ?></option>
                                <?php endforeach?>

                            </select>
                            <span class="form-message"></span>
                        </div>
                        <div class="form-group">
                            <label for="price" class="form-label">Giá bán</label>
                            <input id="price" name="price" type="text" placeholder="Nhập giá bán" class="form-control">
                            <span class="form-message"></span>
                        </div>
                        <div class="form-group">
                            <label for="description" class="form-label">Mô tả</label>
                            <textarea class="product-desc" id="description" name="description"
                                placeholder="Nhập mô tả món ăn..."></textarea>
                            <span class="form-message"></span>
                        </div>
                        <button class="form-submit btn-add-product-form add-product-e" type="submit">
                            <i class="fa-regular fa-plus"></i>
                            <span>THÊM MÓN</span>
                            <input type="hidden" name="add" value="true">
                        </button>
                        <button class="form-submit btn-update-product-form edit-product-e" id="" type="submit">
                            <i class="fa-light fa-pencil"></i>
                            <span>LƯU THAY ĐỔI</span>
                            <input type="hidden" name="update" id="product_id" value="">
                        </button>
                    </div>
                </form>
            </div>
            </form>
        </div>
    </div>
    <div class="modal detail-order">
        <div class="modal-container">
            <h3 class="modal-container-title">CHI TIẾT ĐƠN HÀNG</h3>
            <button class="modal-close"><i class="fa-regular fa-xmark"></i></button>
            <div class="modal-detail-order">
            </div>
            <div class="modal-detail-bottom">
            </div>
            </form>
        </div>
    </div>
    <div class="modal detail-order-product">
        <div class="modal-container">
            <button class="modal-close"><i class="fa-regular fa-xmark"></i></button>
            <div class="table">
                <table width="100%">
                    <thead>
                        <tr>
                            <td>Mã đơn</td>
                            <td>Số lượng</td>
                            <td>Đơn giá</td>
                            <td>Ngày đặt</td>
                        </tr>
                    </thead>
                    <tbody id="show-product-order-detail">
                    </tbody>
                </table>
            </div>
            </form>
        </div>
    </div>
    <div class="modal signup add-user">
        <div class="modal-container">
            <h3 class="modal-container-title add-account-e">THÊM KHÁCH HÀNG MỚI</h3>
            <h3 class="modal-container-title edit-account-e">CHỈNH SỬA THÔNG TIN</h3>
            <button class="modal-close"><i class="fa-regular fa-xmark"></i></button>
            <div class="form-content sign-up">
                <form action="admin.php?check=account" class="signup-form" method="POST">
                    <div class="form-group">
                        <label for="fullname" class="form-label">Tên đầy đủ</label>
                        <input id="fullname" name="fullname" type="text" placeholder="VD: Nhật Sinh"
                            class="form-control">
                        <span class="form-message-name form-message"></span>
                    </div>
                    <div class="form-group">
                        <label for="phone" class="form-label">Số điện thoại</label>
                        <input id="phone" name="phone" type="text" placeholder="Nhập số điện thoại"
                            class="form-control">
                        <span class="form-message-phone form-message"></span>
                    </div>
                    <div class="form-group">
                        <label for="email" class="form-label">Email</label>
                        <input id="email" name="email" type="text" placeholder="Nhập email" class="form-control">
                        <span class="form-message-email form-message"></span>
                    </div>
                    <div class="form-group">
                        <label for="address" class="form-label">Địa chỉ</label>
                        <input id="address" name="address" type="text" placeholder="Nhập địa chỉ" class="form-control">
                        <span class="form-message-address form-message"></span>
                    </div>
                    <div class="form-group">
                        <label for="password" class="form-label">Mật khẩu</label>
                        <input id="password" name="password" type="text" placeholder="Nhập mật khẩu"
                            class="form-control">
                        <span class="form-message-password form-message"></span>
                    </div>
                    <div class="form-group">
                        <label for="user-status" class="form-label">Trạng thái</label>
                        <input type="checkbox" name="user-status" value="on" id="user-status" class="switch-input">
                        <label for="user-status" class="switch"></label>
                    </div>
                    <button class="form-submit add-account-e" id="signup-button">Đăng ký</button>
                    <button class="form-submit edit-account-e" id="btn-update-account"><i
                            class="fa-regular fa-floppy-disk"></i> Lưu thông tin</button>
                    <input type="hidden" name="update" id="user_id" value="">
                </form>
            </div>
        </div>
    </div>
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
    <script>
    // Sua san pham
    document.addEventListener("DOMContentLoaded", function() {
        document.querySelectorAll(".btn-edit").forEach(button => {
            button.addEventListener("click", function() {
                let data = this.getAttribute("data");
                let res = data.split("#");
                let image_old = res[5];
                let path = "../assets/img/products/" + image_old;

                // Điền dữ liệu vào modal
                document.querySelector("#title").value = res[0];
                document.querySelector("#category_id").value = res[1];
                document.querySelector("#price").value = res[2];
                document.querySelector("#description").value = res[3];
                document.querySelector("#product_id").value = res[4];
                document.querySelector("#image-old").value = image_old;
                document.querySelector(".upload-image-preview").setAttribute("src", path);

                // Open Modal
                document.querySelectorAll(".add-product-e").forEach((item) => {
                    item.style.display = "none";
                });
                document.querySelectorAll(".edit-product-e").forEach((item) => {
                    item.style.display = "block";
                });
                document.querySelector(".add-product").classList.add("open");
            });
        });
    });

    // Sửa khách hàng
    document.addEventListener("DOMContentLoaded", function() {
        document.querySelectorAll(".btn-edit-user").forEach(button => {
            button.addEventListener("click", function() {
                let data = this.getAttribute("data");
                let res = data.split("#");
                alert(res[5]);
                // Điền dữ liệu vào modal
                document.querySelector("#fullname").value = res[0];
                document.querySelector("#phone").value = res[1];
                document.querySelector("#email").value = res[2];
                document.querySelector("#address").value = res[3];
                document.querySelector("#password").value = res[4];
                document.querySelector("#user_id").value = res[5];

                // Open Modal
                document.querySelectorAll(".add-account-e").forEach((item) => {
                    item.style.display = "none";
                });
                document.querySelectorAll(".edit-account-e").forEach((item) => {
                    item.style.display = "block";
                });
                document.querySelector(".add-user").classList.add("open");
            });
        });
    });

    let btnAddProduct = document.getElementById("btn-add-product");
    if (btnAddProduct) {
        btnAddProduct.addEventListener("click", () => {
            document.querySelectorAll(".add-product-e").forEach((item) => {
                item.style.display = "block";
            });
            document.querySelectorAll(".edit-product-e").forEach((item) => {
                item.style.display = "none";
            });
            document.querySelector("#title").value = "";
            document.querySelector("#category_id").value = "";
            document.querySelector("#price").value = "";
            document.querySelector("#description").value = "";
            document.querySelector(".add-product").classList.add("open");
        });
    }

    let btnAddAccount = document.getElementById("btn-add-user");
    if (btnAddAccount) {
        btnAddAccount.addEventListener("click", () => {
            document.querySelectorAll(".add-account-e").forEach((item) => {
                item.style.display = "block";
            });
            document.querySelectorAll(".edit-account-e").forEach((item) => {
                item.style.display = "none";
            });
            document.querySelector("#title").value = "";
            document.querySelector("#category_id").value = "";
            document.querySelector("#price").value = "";
            document.querySelector("#description").value = "";
            document.querySelector(".add-user").classList.add("open");
        });
    }


    // Close Popup Modal
    let closePopup = document.querySelectorAll(".modal-close");
    let modalPopup = document.querySelectorAll(".modal");

    for (let i = 0; i < closePopup.length; i++) {
        closePopup[i].onclick = () => {
            modalPopup[i].classList.remove("open");
        };
    }

    function uploadImage(el) {
        // document.getElementById('image').value = el.value.split("\\")[2];
        let file = el.files[0]; // Lấy file được chọn
        if (file) {
            let reader = new FileReader(); // Tạo một FileReader để đọc file

            reader.onload = function(e) {
                document.querySelector(".upload-image-preview").setAttribute("src", e.target.result);
            };

            reader.readAsDataURL(file); // Chuyển file thành Data URL để hiển thị
        }
    }
    </script>
    <script src="../js/admin.js"></script>
    <script src="../js/toast-message.js"></script>
</body>

</html>