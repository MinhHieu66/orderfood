<?php
    require_once "../model/User_Database.php";
    $user_Database = new User_Database();
    $users         = $user_Database->getAllUsers();
?>
<style>
.btn-edit-user {
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
<div class="section active">
    <div class="admin-control">
        <div class="admin-control-left">
            <select name="tinh-trang-user" id="tinh-trang-user" onchange="showUser()">
                <option value="2">Tất cả</option>
                <option value="1">Hoạt động</option>
                <option value="0">Bị khóa</option>
            </select>
        </div>
        <div class="admin-control-center">
            <form action="" class="form-search">
                <span class="search-btn"><i class="fa-light fa-magnifying-glass"></i></span>
                <input id="form-search-user" type="text" class="form-search-input" placeholder="Tìm kiếm khách hàng..."
                    oninput="showUser()">
            </form>
        </div>
        <div class="admin-control-right">
            <form action="" class="fillter-date">
                <div>
                    <label for="time-start">Từ</label>
                    <input type="date" class="form-control-date" id="time-start-user" onchange="showUser()">
                </div>
                <div>
                    <label for="time-end">Đến</label>
                    <input type="date" class="form-control-date" id="time-end-user" onchange="showUser()">
                </div>
            </form>
            <button class="btn-reset-order" onclick="cancelSearchUser()"><i
                    class="fa-light fa-arrow-rotate-right"></i></button>
            <button id="btn-add-user" class="btn-control-large" onclick="openCreateAccount()"><i
                    class="fa-light fa-plus"></i> <span>Thêm khách hàng</span></button>
        </div>
    </div>
    <div class="table">
        <table width="100%">
            <thead>
                <tr>
                    <td>STT</td>
                    <td>Họ và tên</td>
                    <td>Liên hệ</td>
                    <td>Email</td>
                    <td>Địa chỉ</td>
                    <td>Tình trạng</td>
                    <td></td>
                </tr>
            </thead>
            <tbody id="show-user">
                <?php
                    $stt = 1;
                foreach ($users as $user): ?>
                <tr>
                    <td><?php echo $stt++; ?></td>
                    <td><?php echo $user['user_name']; ?></td>
                    <td><?php echo $user['user_phone']; ?></td>
                    <td><?php echo $user['user_email']; ?></td>
                    <td><?php echo $user["user_address"] ?></td>
                    <td>
                        <span class="<?php echo $user['status'] == 1 ? 'status-complete' : 'status-no-complete'; ?>">
                            <?php echo $user['status'] == 1 ? 'Hoạt động' : 'Bị khóa'; ?>
                        </span>
                    </td>
                    <td class="control control-table">
                        <?php $data = $user["user_name"] . "#" . $user["user_phone"] . "#" . $user["user_email"] . "#" . $user["user_address"] . "#" . $user["user_password"] . "#" . $user["user_id"]?>
                        <button class="btn-edit-user" data="<?php echo $data ?>"><i class="fa-light fa-pen-to-square"
                                type="submit"></i></button>
                        <?php $confirm = "Bạn có chắc muốn xóa món ăn " . $user['user_name'] . " không ?"?>
                        <a href=" admin.php?check=account&delete=<?php echo $user['user_id'] ?>"
                            onclick="return confirm('<?php echo $confirm ?>')">
                            <button class="btn-delete" id="delete-account">
                                <i class="fa-regular fa-trash"></i>
                            </button>
                        </a>

                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
</div>