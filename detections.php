<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: agri_error.php?type=unauthorized");
    exit();
}

require 'includes/header.php';
require 'includes/connect.php';

$search = $_GET['search'] ?? '';
$filter = $_GET['filter'] ?? 'All';

// ----------------------
// PAGINATION SETTINGS
// ----------------------
$limit = 6;

$page = isset($_GET['page']) ? (int) $_GET['page'] : 1;
if ($page < 1) $page = 1;

$offset = ($page - 1) * $limit;

// ----------------------
// BASE QUERY (WITH FILTERS)
// ----------------------
$baseSql = "FROM detections d
            LEFT JOIN snapshots s ON d.snapshot_id = s.id
            WHERE 1=1";

if (!empty($search)) {
    $baseSql .= " AND d.detection_type LIKE '%$search%'";
}

if ($filter != 'All') {
    $baseSql .= " AND d.detection_type = '$filter'";
}

// ----------------------
// TOTAL ROWS (WITH FILTERS)
// ----------------------
$totalQuery = "SELECT COUNT(*) as total " . $baseSql;
$totalResult = mysqli_query($con, $totalQuery);
$totalRow = mysqli_fetch_assoc($totalResult);
$totalRecords = $totalRow['total'];

$totalPages = ceil($totalRecords / $limit);

// ----------------------
// MAIN QUERY WITH LIMIT
// ----------------------
$sql = "SELECT d.*, s.image_path " . $baseSql . "
        ORDER BY d.triggered_at DESC
        LIMIT $limit OFFSET $offset";

$result = mysqli_query($con, $sql);
?>
<div class="container-fluid py-4">

    <!-- HEADER -->
    <div class="mb-4 d-flex justify-content-between align-items-center">
        <div>
            <h4 class="fw-bold mb-1">Detections</h4>
            <p class="text-muted mb-0">AI-based object detection logs and captured events</p>
        </div>
    </div>

    <div class="row g-3">

        <!-- LEFT TABLE -->
        <div class="col-lg-8">
            <div class="card shadow-sm border-0">
                <div class="card-body">

                    <form method="GET" class="d-flex gap-2 mb-3">
                        <input type="text" name="search" class="form-control form-control-sm" placeholder="Search..."
                            value="<?= htmlspecialchars($search) ?>">
                        <select name="filter" class="form-select form-select-sm" onchange="this.form.submit()">
                            <option value="All" <?= $filter == 'All' ? 'selected' : '' ?>>All Categories</option>
                            <option value="Human" <?= $filter == 'Human' ? 'selected' : '' ?>>Human</option>
                            <option value="Animal" <?= $filter == 'Animal' ? 'selected' : '' ?>>Animal</option>
                            <option value="Object" <?= $filter == 'Object' ? 'selected' : '' ?>>Object</option>
                        </select>
                        <button class="btn btn-sm btn-dark">Search</button>
                    </form>

                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0 table-striped">
                            <thead class="table-light">
                                <tr>
                                    <th>Image</th>
                                    <th>Category</th>
                                    <th>Objects</th>
                                    <th>Confidence</th>
                                    <th>Timestamp</th>
                                    <th>View</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (mysqli_num_rows($result) > 0): ?>
                                    <?php while ($row = mysqli_fetch_assoc($result)):
                                        $badge = 'bg-secondary';
                                        if ($row['detection_type'] == 'Human')
                                            $badge = 'bg-danger';
                                        if ($row['detection_type'] == 'Animal')
                                            $badge = 'bg-warning';
                                        if ($row['detection_type'] == 'Object')
                                            $badge = 'bg-primary';
                                        ?>
                                        <tr class="detection-row" data-image="<?= htmlspecialchars($row['image_path'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                                            data-coords='<?= $row['coords'] ?>'
                                            data-raw='<?= htmlspecialchars($row['raw_json'], ENT_QUOTES) ?>'
                                            data-category="<?= $row['detection_type'] ?>"
                                            data-objects="<?= htmlspecialchars($row['objects']) ?>"
                                            data-confidence="<?= $row['confidence'] ?>"
                                            data-timestamp="<?= date('M d, Y @ h:i A', strtotime($row['triggered_at'])) ?>">

                                            <td>
                                                <img src="<?= htmlspecialchars($row['image_path'] ?? 'https://via.placeholder.com/80', ENT_QUOTES, 'UTF-8') ?>"
                                                    class="rounded" style="width:70px;height:50px;object-fit:cover;">
                                            </td>

                                            <td><span class="badge <?= $badge ?>"><?= $row['detection_type'] ?></span></td>

                                            <td class="text-muted small">
                                                <?php
                                                $objects = json_decode($row['objects'], true);

                                                if (is_array($objects)) {
                                                    $formatted = array_map(function ($obj) {
                                                        return ucfirst($obj);
                                                    }, $objects);

                                                    echo implode(', ', $formatted);
                                                } else {
                                                    echo htmlspecialchars($row['objects']);
                                                }
                                                ?>
                                            </td>

                                            <td>
                                                <?= $row['confidence']
                                                    ? "<span class='fw-semibold text-success'>{$row['confidence']}%</span>"
                                                    : "<span class='text-muted'>N/A</span>" ?>
                                            </td>

                                            <td class="small text-muted">
                                                <?= date('M d, Y @ h:i A', strtotime($row['triggered_at'])) ?>
                                            </td>

                                            <td>
                                                <button class="btn btn-sm btn-dark select-detection">
                                                    View
                                                </button>
                                            </td>

                                        </tr>
                                    <?php endwhile; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="6" class="text-center text-muted py-4">
                                            No detections found
                                        </td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                        <nav class="mt-3">
                            <ul class="pagination justify-content-center">

                                <!-- PREVIOUS -->
                                <li class="page-item <?= ($page <= 1) ? 'disabled' : '' ?>">
                                    <a class="page-link" href="?page=<?= $page - 1 ?>">Previous</a>
                                </li>

                                <!-- PAGE NUMBERS -->
                                <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                                    <li class="page-item <?= ($i == $page) ? 'active' : '' ?>">
                                        <a class="page-link" href="?page=<?= $i ?>">
                                            <?= $i ?>
                                        </a>
                                    </li>
                                <?php endfor; ?>

                                <!-- NEXT -->
                                <li class="page-item <?= ($page >= $totalPages) ? 'disabled' : '' ?>">
                                    <a class="page-link" href="?page=<?= $page + 1 ?>">Next</a>
                                </li>

                            </ul>
                        </nav>
                    </div>

                </div>
            </div>
        </div>

        <!-- RIGHT DETAILS -->
        <div class="col-lg-4">
            <div class="card shadow-sm border-0" style="min-height:490px;">
                <div class="card-body">

                    <h5 class="fw-bold mb-3">Detection Details</h5>

                    <div id="details-placeholder" class="text-center py-5 px-3">

                        <div class="mb-3">
                            <i class="bi bi-image text-secondary" style="font-size: 40px;"></i>
                        </div>

                        <h6 class="fw-semibold mb-1">No Record Selected</h6>

                        <p class="text-muted mb-3 justify-content" style="font-size: 14px;">
                            Choose a snapshot from the list to view its details, including detection type, device, and
                            timestamp.
                        </p>

                    </div>

                    <div id="details-content" class="d-none">

                        <div class="position-relative mb-3">
                            <img id="detail-image" class="img-fluid rounded w-100"
                                style="max-height:250px; object-fit:cover;">

                            <canvas id="bbox-canvas" class="position-absolute top-0 start-0 w-100 h-100"></canvas>
                        </div>

                        <p><strong>Category:</strong> <span id="detail-category"></span></p>
                        <p><strong>Objects:</strong> <span id="detail-objects"></span></p>
                        <p><strong>Confidence:</strong> <span id="detail-confidence"></span></p>
                        <p><strong>Timestamp:</strong> <span id="detail-time"></span></p>

                    </div>

                </div>
            </div>
        </div>

    </div>
</div>

<?php require 'includes/footer.php'; ?>

<script>
    document.querySelectorAll('.select-detection').forEach(btn => {
        btn.addEventListener('click', function () {

            let row = this.closest('tr');

            let image = row.dataset.image;
            let coords = row.dataset.coords;
            let raw = row.dataset.raw;
            let category = row.dataset.category;
            let objects = row.dataset.objects;
            let confidence = row.dataset.confidence;
            let time = row.dataset.timestamp;

            // SHOW CONTENT
            document.getElementById('details-placeholder').classList.add('d-none');
            document.getElementById('details-content').classList.remove('d-none');

            // SET VALUES
            const img = document.getElementById('detail-image');
            img.src = image;

            document.getElementById('detail-category').innerText = category;

            try {
                let objArr = JSON.parse(objects);
                document.getElementById('detail-objects').innerText = objArr.join(', ');
            } catch {
                document.getElementById('detail-objects').innerText = objects;
            }

            document.getElementById('detail-confidence').innerText =
                confidence ? confidence + '%' : 'N/A';

            document.getElementById('detail-time').innerText = time;

            // CANVAS
            let canvas = document.getElementById('bbox-canvas');
            let ctx = canvas.getContext('2d');

            img.onload = function () {

                // Match canvas to displayed image size
                canvas.width = img.clientWidth;
                canvas.height = img.clientHeight;

                ctx.clearRect(0, 0, canvas.width, canvas.height);

                if (!coords) return;

                let boxes = [];
                let rawData = [];

                // SAFE PARSE
                try {
                    boxes = JSON.parse(coords);
                } catch {
                    boxes = [];
                }

                try {
                    rawData = raw ? JSON.parse(raw) : [];
                } catch {
                    rawData = [];
                }

                if (!Array.isArray(boxes)) boxes = [boxes];

                // 🔥 IMPORTANT: YOLO is 640x640
                const baseSize = 640;
                const scaleX = canvas.width / baseSize;
                const scaleY = canvas.height / baseSize;

                ctx.lineWidth = 2;
                ctx.font = "13px Arial";

                boxes.forEach((box, index) => {

                    let x = box.x * scaleX;
                    let y = box.y * scaleY;
                    let w = box.width * scaleX;
                    let h = box.height * scaleY;

                    // GET LABEL + CONF
                    let label = "Object";
                    let conf = null;

                    if (rawData[index]) {
                        label = rawData[index].label || "Object";
                        conf = rawData[index].confidence ?? null;
                    }

                    // 🎨 COLOR LOGIC
                    let color = "cyan";

                    if (label.toLowerCase().includes("person")) color = "red";
                    else if (
                        label.toLowerCase().includes("dog") ||
                        label.toLowerCase().includes("cat") ||
                        label.toLowerCase().includes("animal")
                    ) color = "orange";
                    else if (label.toLowerCase().includes("bird")) color = "yellow";

                    ctx.strokeStyle = color;
                    ctx.fillStyle = color;

                    // DRAW BOX
                    ctx.strokeRect(x, y, w, h);

                    // TEXT
                    let text = label;
                    if (conf !== null) {
                        text += " (" + (conf * 100).toFixed(1) + "%)";
                    }

                    let textWidth = ctx.measureText(text).width;
                    let textHeight = 16;

                    // Prevent text going outside top
                    let textY = y - textHeight;
                    if (textY < 0) textY = y + 2;

                    // LABEL BG
                    ctx.fillRect(x, textY, textWidth + 6, textHeight);

                    // TEXT COLOR
                    ctx.fillStyle = "#000";
                    ctx.fillText(text, x + 3, textY + 12);
                });
            };
        });
    });
</script>