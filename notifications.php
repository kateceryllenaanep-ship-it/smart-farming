<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: agri_error.php?type=unauthorized");
    exit();
}
require 'includes/header.php';
require 'includes/connect.php';

$query = "SELECT * FROM alerts ORDER BY created_at DESC";
$result = mysqli_query($con, $query);
?>

<div class="p-4">

    <!-- HEADER -->
    <div class="mb-4 d-flex justify-content-between align-items-center flex-wrap gap-2">
        <div>
            <h4 class="fw-bold mb-1">Notifications</h4>
            <p class="text-muted mb-0">
                Real-time alerts and system activities
            </p>
        </div>

        <div class="d-flex gap-2">
            <!-- TYPE FILTER -->
            <select id="typeFilter" class="form-select form-select-sm">
                <option value="all">All Types</option>
                <option value="motion">Motion</option>
                <option value="system">System</option>
                <option value="alert">Alert</option>
            </select>

            <!-- MARK ALL -->
            <button id="clearAllBtn" class="btn btn-sm btn-danger" title="Mark all as Read">
                <i class="bi bi-clipboard-check"></i>
            </button>
        </div>
    </div>

    <!-- LIST -->
    <div class="card shadow-sm border-0 rounded-3">
        <div class="list-group list-group-flush" id="notificationList">

            <?php if (mysqli_num_rows($result) > 0): ?>
                <?php while ($row = mysqli_fetch_assoc($result)): ?>

                    <?php
                    $dateDisplay = date("M d • h:i A", strtotime($row['created_at']));

                    $type = strtolower($row['alert_type']);

                    if ($type === 'human') {
                        $icon = 'bi-exclamation-triangle';
                        $bg = 'text-danger';
                        $dataType = 'alert';
                        $title = 'Intrusion Detected';
                    } elseif ($type === 'animal') {
                        $icon = 'bi-camera-video';
                        $bg = 'text-warning';
                        $dataType = 'motion';
                        $title = 'Animal Detected';
                    } else {
                        $icon = 'bi-cpu';
                        $bg = 'text-secondary';
                        $dataType = 'system';
                        $title = 'System Notification';
                    }

                    $status = strtolower($row['status']);
                    $badgeClass = ($status === 'unread') ? 'bg-primary' : 'bg-secondary';
                    ?>

                    <div class="list-group-item notification-item d-flex justify-content-between align-items-start"
                        data-id="<?= $row['id']; ?>" data-type="<?= $dataType; ?>" data-title="<?= $title; ?>"
                        data-message="<?= htmlspecialchars($row['message']); ?>" data-date="<?= $dateDisplay; ?>"
                        data-status="<?= $status; ?>" style="cursor:pointer;">

                        <div class="d-flex gap-3 align-items-start">

                            <!-- ICON -->
                            <div class="d-flex align-items-center justify-content-center rounded-circle bg-light"
                                style="width:45px;height:45px;">
                                <i class="bi <?= $icon; ?> <?= $bg; ?>"></i>
                            </div>

                            <!-- TEXT -->
                            <div>
                                <div class="fw-semibold"><?= $title; ?></div>
                                <div class="text-muted small"><?= $row['message']; ?></div>
                                <div class="small text-muted mt-1"><?= $dateDisplay; ?></div>
                            </div>

                        </div>

                        <!-- BADGE -->
                        <span class="badge <?= $badgeClass; ?> align-self-center">
                            <?= ucfirst($status); ?>
                        </span>

                    </div>

                <?php endwhile; ?>

            <?php else: ?>
                <div class="text-center text-muted py-4">
                    No notifications found.
                </div>
            <?php endif; ?>

        </div>
    </div>

</div>

<!-- MODAL -->
<div id="notifModal" class="modal fade" tabindex="-1">
    <div class="modal-dialog modal-sm modal-dialog-centered">
        <div class="modal-content border-0 shadow">

            <div class="modal-header py-2">
                <h6 class="modal-title fw-semibold" id="notifTitle"></h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body small">
                <p id="notifMessage"></p>

                <div class="text-muted">
                    <div><strong>Date:</strong> <span id="notifDate"></span></div>
                    <div><strong>Status:</strong> <span id="notifStatus"></span></div>
                    <div><strong>Type:</strong> <span id="notifType"></span></div>
                </div>
            </div>

            <div class="modal-footer py-2">
                <button class="btn btn-sm btn-secondary" data-bs-dismiss="modal">
                    Close
                </button>
            </div>

        </div>
    </div>
</div>

<!-- SCRIPTS -->
<script>
    // CLICK NOTIFICATION
    document.querySelectorAll('.notification-item').forEach(item => {
        item.addEventListener('click', function () {

            const id = this.dataset.id;

            document.getElementById('notifTitle').innerText = this.dataset.title;
            document.getElementById('notifMessage').innerText = this.dataset.message;
            document.getElementById('notifDate').innerText = this.dataset.date;
            document.getElementById('notifStatus').innerText = this.dataset.status;
            document.getElementById('notifType').innerText = this.dataset.type;

            new bootstrap.Modal(document.getElementById('notifModal')).show();

            // MARK AS READ
            fetch('mark_read.php?id=' + id);

            this.dataset.status = 'read';
            let badge = this.querySelector('.badge');
            badge.classList.remove('bg-primary');
            badge.classList.add('bg-secondary');
            badge.innerText = 'Read';
        });
    });

    // MARK ALL AS READ
    document.getElementById('clearAllBtn').addEventListener('click', function () {
        if (!confirm('Mark all as read?')) return;

        fetch('clear_notifications.php').then(() => {
            document.querySelectorAll('.notification-item').forEach(item => {
                item.dataset.status = 'read';
                let badge = item.querySelector('.badge');
                badge.classList.remove('bg-primary');
                badge.classList.add('bg-secondary');
                badge.innerText = 'Read';
            });
        });
    });

    // TYPE FILTER
    document.getElementById('typeFilter').addEventListener('change', function () {
        let value = this.value;

        document.querySelectorAll('.notification-item').forEach(item => {
            if (value === 'all' || item.dataset.type === value) {
                item.style.display = '';
            } else {
                item.style.display = 'none';
            }
        });
    });
</script>

<?php require 'includes/footer.php'; ?>