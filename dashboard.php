<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: agri_error.php?type=unauthorized");
    exit();
}
require 'includes/header.php';
require 'includes/connect.php';

// Overview data
$deviceCount = $con->query("SELECT COUNT(*) as total FROM devices")->fetch_assoc()['total'];
$onlineDevices = $con->query("SELECT COUNT(*) as total FROM devices WHERE status IN ('online','active')")->fetch_assoc()['total'];
$activeAlerts = $con->query("SELECT COUNT(*) as total FROM alerts WHERE status='unread'")->fetch_assoc()['total'];
$lastDetection = $con->query("SELECT * FROM detections ORDER BY triggered_at DESC LIMIT 1")->fetch_assoc();

// Recent alerts & detections
$recentAlerts = $con->query("SELECT * FROM alerts ORDER BY created_at DESC LIMIT 5")->fetch_all(MYSQLI_ASSOC);
$recentDetections = $con->query("SELECT d.*, s.image_path FROM detections d LEFT JOIN snapshots s ON d.snapshot_id=s.id ORDER BY triggered_at DESC LIMIT 5")->fetch_all(MYSQLI_ASSOC);
$snapshots = $con->query("SELECT * FROM snapshots ORDER BY created_at DESC LIMIT 6")->fetch_all(MYSQLI_ASSOC);

// System status
$systemStatus = ($activeAlerts > 0) ? 'ALERT' : 'SAFE';
$statusClass = ($activeAlerts > 0) ? 'text-danger' : 'text-success';
?>

<div class="p-4">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-bold mb-1"><i class="bi bi-speedometer2 me-2"></i>Dashboard</h4>
            <p class="text-muted mb-0">Live Monitoring System Overview</p>
        </div>
    </div>

    <div class="row g-4 mb-4">
        <div class="col-md-3 d-flex">
            <div class="card shadow-sm border-0 rounded-4 p-3 flex-fill bg-white">
                <div class="d-flex align-items-center">
                    <div class="bg-primary bg-opacity-10 p-3 rounded-3 me-3">
                        <i class="bi bi-hdd-network text-primary fs-4"></i>
                    </div>
                    <div>
                        <small class="text-muted d-block mb-1">Total Devices</small>
                        <h4 class="fw-bold mb-0"><?= $deviceCount ?></h4>
                        <small class="text-success fw-medium"><?= $onlineDevices ?> Online</small>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3 d-flex">
            <div class="card shadow-sm border-0 rounded-4 p-3 flex-fill bg-white">
                <div class="d-flex align-items-center">
                    <div class="bg-danger bg-opacity-10 p-3 rounded-3 me-3">
                        <i class="bi bi-exclamation-triangle text-danger fs-4"></i>
                    </div>
                    <div>
                        <small class="text-muted d-block mb-1">Active Alerts</small>
                        <h4 class="fw-bold text-danger mb-0"><?= $activeAlerts ?></h4>
                        <small class="<?= $activeAlerts > 0 ? 'text-danger fw-bold' : 'text-muted' ?>">
                            <?= $activeAlerts > 0 ? 'Action Required' : 'System Clear' ?>
                        </small>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3 d-flex">
            <div class="card shadow-sm border-0 rounded-4 p-3 flex-fill bg-white">
                <div class="d-flex align-items-center">
                    <div class="bg-warning bg-opacity-10 p-3 rounded-3 me-3">
                        <i class="bi bi-camera text-warning fs-4"></i>
                    </div>
                    <div class="overflow-hidden">
                        <small class="text-muted d-block mb-1">Last Detection</small>
                        <h5 class="fw-bold mb-0 text-truncate"><?= $lastDetection['detection_type'] ?? 'None' ?></h5>
                        <small class="text-muted" style="font-size: 0.75rem;">
                            <?= isset($lastDetection['triggered_at']) ? date('M j, g:i A', strtotime($lastDetection['triggered_at'])) : 'No activity' ?>
                        </small>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3 d-flex">
            <div class="card shadow-sm border-0 rounded-4 p-3 flex-fill bg-white">
                <div class="d-flex align-items-center">
                    <div class="<?= $activeAlerts > 0 ? 'bg-danger' : 'bg-success' ?> bg-opacity-10 p-3 rounded-3 me-3">
                        <i class="bi <?= $activeAlerts > 0 ? 'bi-shield-exclamation' : 'bi-shield-check' ?> <?= $statusClass ?> fs-4"></i>
                    </div>
                    <div>
                        <small class="text-muted d-block mb-1">System Status</small>
                        <h5 class="fw-bold <?= $statusClass ?> mb-0"><?= $systemStatus ?></h5>
                        <div class="d-flex align-items-center">
                            <div class="spinner-grow spinner-grow-sm text-success me-1" role="status" style="width: 6px; height: 6px;"></div>
                            <small class="text-muted" style="font-size: 0.75rem;">Live</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4 mb-4">
        <div class="col-md-6 d-flex">
            <div class="card shadow-lg p-4 border-0 rounded-4 w-100">
                <h6 class="fw-semibold mb-3"><i class="bi bi-graph-up me-2"></i>Alerts Trend (Last 7 Days)</h6>
                <div style="position: relative; height: 250px; width: 100%;">
                    <canvas id="alertsChart"></canvas>
                </div>
            </div>
        </div>
        <div class="col-md-6 d-flex">
            <div class="card shadow-lg p-4 border-0 rounded-4 w-100">
                <h6 class="fw-semibold mb-3"><i class="bi bi-hdd-stack me-2"></i>Device Status</h6>
                <div style="position: relative; height: 250px; width: 100%;">
                    <canvas id="deviceChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4 mb-4">
        <div class="col-md-6">
            <div class="card shadow-lg p-4 border-0 rounded-4 h-100">
                <h6 class="fw-semibold mb-3"><i class="bi bi-bug me-2"></i>Recent Detections</h6>
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Type</th>
                                <th>Confidence</th>
                                <th>Time</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($recentDetections as $det): ?>
                            <tr>
                                <td><?= htmlspecialchars($det['detection_type']) ?></td>
                                <td><?= number_format($det['confidence'], 2) ?>%</td>
                                <td><?= $det['triggered_at'] ?></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card shadow-lg p-4 border-0 rounded-4 h-100">
                <h6 class="fw-semibold mb-3"><i class="bi bi-bell me-2"></i>Recent Alerts</h6>
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Type</th>
                                <th>Status</th>
                                <th>Time</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($recentAlerts as $alert): ?>
                            <tr>
                                <td><?= htmlspecialchars($alert['alert_type']) ?></td>
                                <td>
                                    <?php if($alert['status']=='unread'): ?>
                                        <span class="badge bg-danger">Unread</span>
                                    <?php else: ?>
                                        <span class="badge bg-secondary">Read</span>
                                    <?php endif; ?>
                                </td>
                                <td><?= $alert['created_at'] ?></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-md-12">
            <div class="card shadow-lg p-4 border-0 rounded-4">
                <h6 class="fw-semibold mb-3"><i class="bi bi-camera-reels me-2"></i>Recent Snapshots</h6>
                <div class="d-flex flex-wrap gap-2">
                    <?php foreach ($snapshots as $snap): ?>
                        <div class="border rounded overflow-hidden shadow-sm" style="width:120px; height:80px; transition: transform 0.2s;">
                            <img src="<?= htmlspecialchars($snap['image_path'], ENT_QUOTES, 'UTF-8') ?>" class="img-fluid w-100 h-100" style="object-fit:cover;" alt="Snapshot">
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>

</div>

<?php require 'includes/footer.php'; ?>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // Alerts trend chart
    const alertsCtx = document.getElementById('alertsChart').getContext('2d');
    const alertsChart = new Chart(alertsCtx, {
        type: 'line',
        data: {
            labels: [<?= implode(',', array_map(function($i){ return "'".date('M d', strtotime("-$i days"))."'"; }, range(6,0))) ?>],
            datasets: [{
                label: 'Alerts',
                data: [<?= implode(',', array_map(function($i) use ($con){ 
                    $count = $con->query("SELECT COUNT(*) FROM alerts WHERE DATE(created_at) = CURDATE() - INTERVAL $i DAY")->fetch_row()[0]; 
                    return $count; 
                }, range(6,0))) ?>],
                borderColor: '#dc3545',
                backgroundColor: 'rgba(220,53,69,0.1)',
                tension: 0.4,
                fill: true,
                pointRadius: 5,
                pointBackgroundColor: '#dc3545'
            }]
        },
        options: { 
            responsive: true, 
            maintainAspectRatio: false, // Allows chart to fill width of container
            plugins: { legend: { display: false } }, 
            scales: { 
                y: { beginAtZero: true, ticks: { stepSize: 1 } }
            }
        }
    });

    // Device status chart
    const deviceCtx = document.getElementById('deviceChart').getContext('2d');
    const deviceChart = new Chart(deviceCtx, {
        type: 'doughnut',
        data: {
            labels: ['Online', 'Offline'],
            datasets: [{
                data: [<?= $onlineDevices ?>, <?= $deviceCount - $onlineDevices ?>],
                backgroundColor: ['#198754', '#6c757d'],
                borderWidth: 0
            }]
        },
        options: { 
            responsive: true, 
            maintainAspectRatio: false, // Crucial for filling the card width
            plugins: { 
                legend: { position: 'bottom' } 
            },
            cutout: '70%'
        }
    });

    // Snapshot hover zoom
    document.querySelectorAll('.card img').forEach(img => {
        img.addEventListener('mouseenter', () => img.parentElement.style.transform = 'scale(1.05)');
        img.addEventListener('mouseleave', () => img.parentElement.style.transform = 'scale(1)');
    });
</script>