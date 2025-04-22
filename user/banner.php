<main class="main-wrapper">
    <div class="container" id="trangchu">
        <div class="home-slider">
            <img src="../assets/img/banner-5.png" alt="" />
        </div>
        <div class="home-service" id="home-service">
            <div class="home-service-item">
                <div class="home-service-item-icon">
                    <i class="fa-light fa-person-carry-box"></i>
                </div>
                <div class="home-service-item-content">
                    <h4 class="home-service-item-content-h">GIAO HÀNG NHANH</h4>
                    <p class="home-service-item-content-desc">Cho tất cả đơn hàng</p>
                </div>
            </div>
            <div class="home-service-item">
                <div class="home-service-item-icon">
                    <i class="fa-light fa-shield-heart"></i>
                </div>
                <div class="home-service-item-content">
                    <h4 class="home-service-item-content-h">SẢN PHẨM AN TOÀN</h4>
                    <p class="home-service-item-content-desc">Cam kết chất lượng</p>
                </div>
            </div>
            <div class="home-service-item">
                <div class="home-service-item-icon">
                    <i class="fa-light fa-headset"></i>
                </div>
                <div class="home-service-item-content">
                    <h4 class="home-service-item-content-h">HỖ TRỢ 24/7</h4>
                    <p class="home-service-item-content-desc">
                        Tất cả ngày trong tuần
                    </p>
                </div>
            </div>
            <div class="home-service-item">
                <div class="home-service-item-icon">
                    <i class="fa-light fa-circle-dollar"></i>
                </div>
                <div class="home-service-item-content">
                    <h4 class="home-service-item-content-h">HOÀN LẠI TIỀN</h4>
                    <p class="home-service-item-content-desc">Nếu không hài lòng</p>
                </div>
            </div>
        </div>
        <div class="home-title-block" id="home-title">
            <h2 class="home-title">Khám phá thực đơn của chúng tôi</h2>
        </div>
        <!-- Danh sách sản phẩm -->
        <?php require_once "list-product.php"?>
        <div class="page-nav">
            <ul class="page-nav-list"></ul>
        </div>
    </div>
    <div class="container" id="account-user">
        <div class="main-account">
            <div class="main-account-header">
                <h3>Thông tin tài khoản của bạn</h3>
                <p>Quản lý thông tin để bảo mật tài khoản</p>
            </div>
            <div class="main-account-body">
                <div class="main-account-body-col">
                    <form action="" class="info-user">
                        <div class="form-group">
                            <label for="infoname" class="form-label">Họ và tên</label>
                            <input class="form-control" type="text" name="infoname" id="infoname" placeholder="" />
                        </div>
                        <div class="form-group">
                            <label for="infophone" class="form-label">Số điện thoại</label>
                            <input class="form-control" type="text" name="infophone" id="infophone" disabled="true"
                                placeholder="" />
                        </div>
                        <div class="form-group">
                            <label for="infoemail" class="form-label">Email</label>
                            <input class="form-control" type="email" name="infoemail" id="infoemail"
                                placeholder="Thêm địa chỉ email của bạn" />
                            <span class="inforemail-error form-message"></span>
                        </div>
                        <div class="form-group">
                            <label for="infoaddress" class="form-label">Địa chỉ</label>
                            <input class="form-control" type="text" name="infoaddress" id="infoaddress"
                                placeholder="Thêm địa chỉ giao hàng của bạn" />
                        </div>
                    </form>
                </div>
                <div class="main-account-body-col">
                    <form action="" class="change-password">
                        <div class="form-group">
                            <label for="" class="form-label w60">Mật khẩu hiện tại</label>
                            <input class="form-control" type="password" name="" id="password-cur-info"
                                placeholder="Nhập mật khẩu hiện tại" />
                            <span class="password-cur-info-error form-message"></span>
                        </div>
                        <div class="form-group">
                            <label for="" class="form-label w60">Mật khẩu mới </label>
                            <input class="form-control" type="password" name="" id="password-after-info"
                                placeholder="Nhập mật khẩu mới" />
                            <span class="password-after-info-error form-message"></span>
                        </div>
                        <div class="form-group">
                            <label for="" class="form-label w60">Xác nhận mật khẩu mới</label>
                            <input class="form-control" type="password" name="" id="password-comfirm-info"
                                placeholder="Nhập lại mật khẩu mới" />
                            <span class="password-after-comfirm-error form-message"></span>
                        </div>
                    </form>
                </div>
                <div class="main-account-body-row">
                    <div>
                        <button id="save-info-user" onclick="changeInformation()">
                            <i class="fa-regular fa-floppy-disk"></i> Lưu thay đổi
                        </button>
                    </div>
                    <div>
                        <button id="save-password" onclick="changePassword()">
                            <i class="fa-regular fa-key"></i> Đổi mật khẩu
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="container" id="order-history">
        <div class="main-account">
            <div class="main-account-header">
                <h3>Quản lý đơn hàng của bạn</h3>
                <p>Xem chi tiết, trạng thái của những đơn hàng đã đặt.</p>
            </div>
            <div class="main-account-body">
                <div class="order-history-section"></div>
            </div>
        </div>
    </div>
</main>