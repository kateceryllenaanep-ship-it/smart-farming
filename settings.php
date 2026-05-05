<?php
    session_start();
    if (!isset($_SESSION['user_id'])) {
        header("Location: agri_error.php?type=unauthorized");
        exit();
    }
    require 'includes/header.php';
    require 'includes/connect.php';

    // SAMPLE USER (replace with session later)
    $user_id = 1;

    // FETCH USER
    $user = mysqli_fetch_assoc(mysqli_query($con, "SELECT * FROM users WHERE id='$user_id'"));

    // FETCH LOGS
    $logs = mysqli_query($con, "SELECT * FROM system_logs ORDER BY created_at DESC LIMIT 10");

    // FETCH SMS SETTINGS
    $sms = mysqli_fetch_assoc(mysqli_query($con, "SELECT * FROM sms_settings LIMIT 1"));
    $sms_logs = mysqli_query($con, "SELECT * FROM sms_logs ORDER BY sent_at DESC LIMIT 10");
?>

<div class="p-4">

    <!-- PAGE HEADER -->
    <div class="mb-4">
        <h4 class="fw-bold">Settings</h4>
        <p class="text-muted">Manage your account, system activity, and SMS configuration</p>
    </div>

    <div class="row g-4">

        <!-- ================= ACCOUNT CARD ================= -->
        <div class="col-md-6">
            <div class="card shadow-sm border-0 p-4 h-100">
                <h5 class="fw-semibold mb-3">Account Information</h5>

                <form action="update_account.php" method="POST">
                    <input type="hidden" name="id" value="<?= $user['id']; ?>">

                    <div class="mb-3">
                        <label class="form-label">Name</label>
                        <input type="text" name="name" class="form-control" value="<?= $user['name']; ?>">
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Username</label>
                        <input type="text" name="username" class="form-control" value="<?= $user['username']; ?>">
                    </div>

                    <div class="mb-3">
                        <label class="form-label">New Password</label>
                        <input type="password" name="password" class="form-control" placeholder="Leave blank to keep current">
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Role</label>
                        <span class="badge bg-light text-dark px-3 py-2"><?= ucfirst($user['role']); ?></span>
                    </div>

                    <div class="text-end">
                        <button class="btn btn-primary">Update Account</button>
                    </div>
                </form>
            </div>
        </div>

        <!-- ================= SYSTEM LOGS CARD ================= -->
        <div class="col-md-6">
            <div class="card shadow-sm border-0 p-4 h-100">
                <h5 class="fw-semibold mb-3">System Logs</h5>

                <div class="list-group list-group-flush">
                    <?php while($log = mysqli_fetch_assoc($logs)): ?>
                        <?php
                        $type = $log['log_type'];
                        $badgeClass = $type == 'error' ? 'bg-danger' : ($type == 'action' ? 'bg-success' : 'bg-primary');
                        ?>
                        <div class="list-group-item d-flex justify-content-between align-items-start border-light-subtle">
                            <div>
                                <div class="fw-semibold"><?= ucfirst($type); ?></div>
                                <div class="text-muted small"><?= $log['message']; ?></div>
                                <div class="small text-muted"><?= date("M d • h:i A", strtotime($log['created_at'])); ?></div>
                            </div>
                            <span class="badge <?= $badgeClass; ?> bg-opacity-25 text-dark"><?= ucfirst($type); ?></span>
                        </div>
                    <?php endwhile; ?>
                </div>
            </div>
        </div>

        <!-- ================= SMS CONFIGURATION CARD ================= -->
        <div class="col-md-6">
            <div class="card shadow-sm border-0 p-4 h-100">
                <h5 class="fw-semibold mb-3">SMS Configuration</h5>

                <form action="update_sms.php" method="POST">
                    <div class="mb-3">
                        <label class="form-label">Phone Number</label>
                        <input type="text" name="phone" class="form-control" value="<?= $sms['phone_number'] ?? ''; ?>">
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Sender Name</label>
                        <input type="text" name="sender" class="form-control" value="<?= $sms['sender_name'] ?? ''; ?>">
                    </div>

                    <div class="mb-3">
                        <label class="form-label">API Key</label>
                        <input type="text" name="api_key" class="form-control" value="<?= $sms['api_key'] ?? ''; ?>">
                    </div>

                    <div class="text-end">
                        <button class="btn btn-success">Save SMS Settings</button>
                    </div>
                </form>
            </div>
        </div>

        <!-- ================= SMS LOGS CARD ================= -->
        <div class="col-md-6">
            <div class="card shadow-sm border-0 p-4 h-100">
                <h5 class="fw-semibold mb-3">SMS Logs</h5>

                <div class="list-group list-group-flush">
                    <?php while($log = mysqli_fetch_assoc($sms_logs)): ?>
                        <div class="list-group-item d-flex justify-content-between align-items-center border-light-subtle">
                            <div>
                                <div class="fw-semibold"><?= $log['phone_number']; ?></div>
                                <div class="text-muted small"><?= $log['message']; ?></div>
                            </div>
                            <span class="badge <?= $log['status'] == 'sent' ? 'bg-success bg-opacity-25 text-dark' : 'bg-danger bg-opacity-25 text-dark'; ?>">
                                <?= ucfirst($log['status']); ?>
                            </span>
                        </div>
                    <?php endwhile; ?>
                </div>
            </div>
        </div>

    </div>

</div>

<?php require 'includes/footer.php'; ?>