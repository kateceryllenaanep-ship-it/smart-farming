<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: agri_error.php?type=unauthorized");
    exit();
}
require 'includes/header.php';
require 'includes/connect.php';

// FETCH SNAPSHOTS
$query = "
            SELECT 
                s.id AS snapshot_id,
                s.image_path,
                s.created_at,
                s.source,
                d.device_name,
                dt.detection_type
            FROM snapshots s
            LEFT JOIN devices d ON s.device_id = d.id
            LEFT JOIN detections dt ON s.id = dt.snapshot_id
            ORDER BY s.created_at DESC
            ";
$result = mysqli_query($con, $query);
?>

<div class="p-4">

    <!-- HEADER -->
    <div class="mb-4 d-flex justify-content-between align-items-center">
        <div>
            <h4 class="fw-bold mb-1">Snapshots</h4>
            <p class="text-muted mb-0">
                Browse and manage captured snapshot records
            </p>
        </div>

        <!-- <button class="btn btn-sm btn-primary">
            <i class="bi bi-images me-1"></i> Gallery View
        </button> -->
    </div>

    <!-- FILTER CARD -->
    <div class="card shadow-sm border-0 mb-4">
        <div class="card-body d-flex flex-wrap gap-2 justify-content-between align-items-center">

            <h6 class="mb-0 fw-semibold">Filters</h6>

            <div class="d-flex gap-2">

                <!-- FILTER TYPE -->
                <select id="filterType" class="form-select form-select-sm">
                    <option value="all">All</option>
                    <option value="Human">Human</option>
                    <option value="Object">Object</option>
                    <option value="Animals">Animals</option>
                    <option value="System">System</option>
                </select>

                <!-- SEARCH -->
                <input type="text" class="form-control form-control-sm" placeholder="Search..." style="width: 180px;">

            </div>

        </div>
    </div>

    <!-- SNAPSHOT GRID -->
    <div class="row g-3">

        <?php if (mysqli_num_rows($result) > 0): ?>
            <?php while ($row = mysqli_fetch_assoc($result)): ?>

                <?php
                // FORMAT DATE
                $date = date("M d, Y • h:i A", strtotime($row['created_at']));

                // DETECT LABEL FROM IMAGE NAME (optional logic)
                $imageName = strtolower(basename($row['image_path']));
                $label = "Unknown";

                if (str_contains($imageName, 'human'))
                    $label = "Human";
                elseif (str_contains($imageName, 'bird'))
                    $label = "Bird";
                elseif (str_contains($imageName, 'object'))
                    $label = "Object";
                ?>

                <div class="col-md-4">
                    <div class="card border-0 shadow-sm overflow-hidden h-100">

                        <!-- IMAGE WRAPPER -->
                        <div class="position-relative">

                            <img src="<?= htmlspecialchars($row['image_path'], ENT_QUOTES, 'UTF-8'); ?>" class="w-100" style="height: 230px; object-fit: cover;">

                            <!-- TOP BADGE -->
                            <?php
                            if ($row['source'] === 'detection') {
                                $badgeClass = 'bg-danger';
                            } else {
                                $badgeClass = 'bg-success';
                            }
                            ?>

                            <span class="badge <?= $badgeClass; ?> position-absolute top-0 start-0 m-2 small">
                                <?= ucfirst($row['source']); ?>
                            </span>

                            <!-- OVERLAY -->
                            <div class="position-absolute bottom-0 start-0 w-100 p-2"
                                style="background: linear-gradient(to top, rgba(0,0,0,0.65), transparent);">

                                <div class="text-white small">
                                    <div class="fw-semibold"><?= $row['device_name']; ?></div>
                                    <div style="font-size: 12px;"><?= $date; ?></div>
                                </div>

                            </div>

                        </div>

                        <!-- BODY -->
                        <div class="card-body py-2 px-3 d-flex justify-content-between align-items-center">

                            <!-- DETECTION -->
                            <small class="fw-semibold text-dark mb-0">
                                <?= $row['detection_type']; ?>
                            </small>

                            <!-- ACTIONS -->
                            <div class="d-flex gap-1">
                                <button class="btn btn-sm btn-light border view-btn" data-img="<?= htmlspecialchars($row['image_path'], ENT_QUOTES, 'UTF-8'); ?>"
                                    data-device="<?= htmlspecialchars($row['device_name'], ENT_QUOTES, 'UTF-8'); ?>" data-date="<?= htmlspecialchars($date, ENT_QUOTES, 'UTF-8'); ?>"
                                    data-type="<?= htmlspecialchars($row['detection_type'], ENT_QUOTES, 'UTF-8'); ?>">
                                    <i class="bi bi-info"></i>
                                </button>

                                <a href="delete_snapshot.php?id=<?= $row['snapshot_id']; ?>"
                                    class="btn btn-sm btn-light border text-danger"
                                    onclick="return confirm('Delete this snapshot?')">
                                    <i class="bi bi-trash"></i>
                                </a>
                            </div>

                        </div>

                    </div>
                </div>

            <?php endwhile; ?>

        <?php else: ?>
            <div class="text-center text-muted py-5">
                No snapshots found.
            </div>
        <?php endif; ?>

    </div>

</div>
<!-- SNAPSHOT MODAL -->
<div id="snapshotModal" class="modal fade" tabindex="-1">
  <div class="modal-dialog modal-sm modal-dialog-centered">
    <div class="modal-content border-0 shadow">

      <!-- HEADER -->
      <div class="modal-header py-2">
        <h6 class="modal-title fw-semibold">Snapshot</h6>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>

      <!-- BODY -->
      <div class="modal-body p-2">

        <!-- IMAGE WRAPPER -->
        <div class="position-relative rounded overflow-hidden">

          <!-- IMAGE -->
          <img id="modalImage" class="w-100" style="object-fit: cover;">

        </div>

      </div>

      <!-- FOOTER -->
      <div class="modal-footer py-2 px-2 d-flex justify-content-between">

        <small class="text-muted">Snapshot Preview</small>

        <button class="btn btn-sm btn-secondary" data-bs-dismiss="modal">
          Close
        </button>

      </div>

    </div>
  </div>
</div>
<script>
    document.querySelectorAll('.view-btn').forEach(btn => {
        btn.addEventListener('click', function () {

            document.getElementById('modalImage').src = this.dataset.img;

            let modal = new bootstrap.Modal(document.getElementById('snapshotModal'));
            modal.show();
        });
    });
</script>
<?php require 'includes/footer.php'; ?>