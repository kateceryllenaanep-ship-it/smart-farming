<?php 
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: agri_error.php?type=unauthorized");
    exit();
}
require 'includes/header.php';
require 'includes/connect.php';
?>

<div class="p-4">

    <!-- HEADER -->
    <div class="mb-4 d-flex justify-content-between align-items-center">
        <div>
            <h4 class="fw-bold mb-1">Device Management</h4>
            <p class="text-muted mb-0">Add, edit, or remove IoT device components connected to your system</p>
        </div>
        <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#addDeviceModal">
            <i class="bi bi-plus-circle me-1"></i> Add Component
        </button>
    </div>

    <!-- COMPONENTS TABLE -->
    <div class="table-responsive">
        <table class="table table-hover align-middle" id="componentsTable">
            <thead class="table-light">
                <tr>
                    <th>ID</th>
                    <th>Component</th>
                    <th>Type</th>
                    <th>Pin</th>
                    <th>UID</th>
                    <th>Status</th>
                    <th>Created Date</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody id="componentsBody">
                <?php
                $query = "SELECT * FROM devices ORDER BY created_date DESC";
                $result = mysqli_query($con, $query);
                while($device = mysqli_fetch_assoc($result)): ?>
                    <tr id="componentRow<?= $device['id']; ?>">
                        <td><?= $device['id']; ?></td>
                        <td><?= htmlspecialchars($device['device_name']); ?></td>
                        <td><?= htmlspecialchars($device['component_type']); ?></td>
                        <td><?= htmlspecialchars($device['esp32_pin']); ?></td>
                        <td><?= htmlspecialchars($device['esp32_uid']); ?></td>
                        <td>
                            <?php
                        $displayStatus = $device['status'] === 'active' ? 'Online' : ucfirst($device['status']);
                        $badgeClass = ($device['status'] === 'active' || $device['status'] === 'online') ? 'bg-success' : ($device['status'] === 'offline' ? 'bg-secondary' : 'bg-warning');
                    ?>
                    <span class="badge statusBadge <?= $badgeClass ?>" data-uid="<?= $device['esp32_uid']; ?>" 
                                id="statusBadge<?= $device['id']; ?>">
                                <?= $displayStatus; ?>
                            </span>
                        </td>
                        <td><?= $device['created_date']; ?></td>
                        <td>
                            <button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#editDeviceModal<?= $device['id']; ?>">
                                Edit
                            </button>
                            <a href="delete_device.php?id=<?= $device['id']; ?>" class="btn btn-sm btn-danger delete-btn">
                                Delete
                            </a>
                        </td>
                    </tr>

                    <!-- EDIT COMPONENT MODAL -->
                    <div class="modal fade" id="editDeviceModal<?= $device['id']; ?>" tabindex="-1">
                        <div class="modal-dialog">
                            <form action="update_device.php" method="POST" class="modal-content edit-form">
                                <input type="hidden" name="device_id" value="<?= $device['id']; ?>">
                                <div class="modal-header">
                                    <h5 class="modal-title">Edit Component</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                </div>
                                <div class="modal-body">
                                    <div class="mb-3">
                                        <label class="form-label">Component Name</label>
                                        <input type="text" name="component_name" class="form-control" value="<?= htmlspecialchars($device['component_name']); ?>" required>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Component Type</label>
                                        <select name="component_type" class="form-select" required>
                                            <?php
                                            $types = ['PIR','LED','Buzzer','Camera','GSM'];
                                            foreach ($types as $type):
                                            ?>
                                                <option value="<?= $type; ?>" <?= $device['component_type'] === $type ? 'selected' : ''; ?>>
                                                    <?= $type; ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">ESP32 Pin</label>
                                        <input type="number" name="esp32_pin" class="form-control" value="<?= htmlspecialchars($device['esp32_pin']); ?>" required>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">ESP32 UID</label>
                                        <input type="text" name="esp32_uid" class="form-control" value="<?= htmlspecialchars($device['esp32_uid']); ?>" required>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Status</label>
                                        <select name="status" class="form-select">
                                            <option value="online" <?= $device['status'] === 'online' ? 'selected' : ''; ?>>Online</option>
                                            <option value="offline" <?= $device['status'] === 'offline' ? 'selected' : ''; ?>>Offline</option>
                                            <option value="active" <?= $device['status'] === 'active' ? 'selected' : ''; ?>>Active</option>
                                            <option value="inactive" <?= $device['status'] === 'inactive' ? 'selected' : ''; ?>>Inactive</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="submit" class="btn btn-primary">Save Changes</button>
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                </div>
                            </form>
                        </div>
                    </div>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>

</div>

<!-- ADD COMPONENT MODAL -->
<div class="modal fade" id="addDeviceModal" tabindex="-1">
    <div class="modal-dialog">
        <form action="add_device.php" method="POST" class="modal-content add-form">
            <div class="modal-header">
                <h5 class="modal-title">Add New Component</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label">Component Name</label>
                    <input type="text" name="component_name" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Component Type</label>
                    <select name="component_type" class="form-select" required>
                        <option value="PIR">PIR</option>
                        <option value="LED">LED</option>
                        <option value="Buzzer">Buzzer</option>
                        <option value="Camera">Camera</option>
                        <option value="GSM">GSM</option>
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label">ESP32 Pin</label>
                    <input type="number" name="esp32_pin" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">ESP32 UID</label>
                    <input type="text" name="esp32_uid" class="form-control" required>
                    <small class="text-muted">Unique identifier of the ESP32 board (MAC or ChipID)</small>
                </div>
                <div class="mb-3">
                    <label class="form-label">Status</label>
                    <select name="status" class="form-select">
                        <option value="online">Online</option>
                        <option value="offline" selected>Offline</option>
                        <option value="active">Active</option>
                        <option value="inactive">Inactive</option>
                    </select>
                </div>
            </div>
            <div class="modal-footer">
                <button type="submit" class="btn btn-success">Add Component</button>
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
            </div>
        </form>
    </div>
</div>

<?php require 'includes/footer.php'; ?>

<!-- SWEETALERT & AJAX SCRIPT -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
document.addEventListener('DOMContentLoaded', () => {
    // Handle Add/Edit/Delete responses via session messages
    <?php if(isset($_SESSION['success'])): ?>
        Swal.fire({icon: 'success', title: 'Success', text: '<?= $_SESSION['success']; ?>'});
        <?php unset($_SESSION['success']); ?>
    <?php endif; ?>
    <?php if(isset($_SESSION['error'])): ?>
        Swal.fire({icon: 'error', title: 'Error', text: '<?= $_SESSION['error']; ?>'});
        <?php unset($_SESSION['error']); ?>
    <?php endif; ?>

    // Handle Delete confirmation via SweetAlert
    document.querySelectorAll('.delete-btn').forEach(btn => {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            const url = this.href;
            Swal.fire({
                title: 'Are you sure?',
                text: "This component will be permanently deleted.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Yes, delete it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = url;
                }
            });
        });
    });

    // AJAX: Live status updates every 5s
    setInterval(() => {
        const badges = document.querySelectorAll('.statusBadge');
        const uids = Array.from(badges).map(b => b.dataset.uid);
        if(uids.length === 0) return;

        fetch('get_device_status.php', {
            method: 'POST',
            headers: {'Content-Type':'application/json'},
            body: JSON.stringify({uids: uids})
        })
        .then(res => res.json())
        .then(data => {
            badges.forEach(badge => {
                const uid = badge.dataset.uid;
                if(data[uid]) {
                    // Show "Online" for active components
                    const displayStatus = data[uid] === 'active' ? 'online' : data[uid];
                    badge.textContent = displayStatus.charAt(0).toUpperCase() + displayStatus.slice(1);
                    badge.className = 'badge ' + (
                        displayStatus === 'online' ? 'bg-success' :
                        displayStatus === 'offline' ? 'bg-secondary' : 'bg-warning'
                    );
                }
            });
        });
    }, 5000);
});
</script>