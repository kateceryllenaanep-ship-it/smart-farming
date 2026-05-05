<?php 
    session_start();
    if (!isset($_SESSION['user_id'])) {
        header("Location: agri_error.php?type=unauthorized");
        exit();
    }
    require 'includes/header.php'; 
?>

<div class="p-4">

    <!-- PAGE HEADER -->
    <div class="mb-4">
        <h4 class="fw-bold mb-1">Live Monitoring</h4>
        <p class="text-muted mb-0">
            Real-time motion detection and field surveillance
        </p>
    </div>

    <div class="row g-4 align-items-stretch">

        <!-- LEFT: CAMERA (like Balance Sheet panel) -->
        <div class="col-lg-8">

            <!-- LIVE CAMERA CARD -->
            <div class="card shadow border-0 rounded-3">
                <div class="card-header bg-white d-flex justify-content-between align-items-center">
                    <span class="fw-semibold">
                        <i class="bi bi-camera-video me-1"></i> Camera Feed
                    </span>

                    <div class="d-flex gap-2"></div>
                </div>
                <div class="card-body">

                    <div class="bg-dark rounded-3 overflow-hidden mb-3 position-relative">

                        <video id="camera" autoplay playsinline
                            class="w-100"
                            style="height: 420px; object-fit: cover;">
                        </video>

                        <canvas id="snapshotCanvas" style="display:none;"></canvas>

                        <!-- Overlay canvas -->
                        <canvas id="overlay"
                            class="position-absolute top-0 start-0 w-100 h-100"
                            style="pointer-events: none;">
                        </canvas>

                    </div>
                </div>
            </div>
        </div>

        <!-- RIGHT: SIDE PANEL -->
        <div class="col-lg-4 d-flex flex-column gap-4">

            <!-- DETECTION STATUS + QUICK ACTIONS -->
            <div class="card shadow border-0 rounded-3 flex-grow-1">

                <div class="card-header bg-white fw-semibold">
                    <i class="bi bi-activity me-1"></i> Detection Status
                </div>

                <div class="card-body d-flex flex-column">

                    <!-- STATUS -->
                    <div class="d-flex justify-content-between align-items-center">
                        <span class="text-muted">Status</span>
                        <span class="fw-semibold text-success d-flex align-items-center gap-1">
                            <span class="badge bg-success rounded-circle p-1"></span>
                            No Movement
                        </span>
                    </div>

                    <!-- LAST ACTIVITY -->
                    <div class="d-flex justify-content-between align-items-center">
                        <span class="text-muted">Last Activity</span>
                        <span class="fw-medium">2 mins ago</span>
                    </div>

                    <!-- MODE -->
                    <div class="d-flex justify-content-between align-items-center">
                        <span class="text-muted">Mode</span>
                        <span class="badge bg-secondary px-3 py-2">Night</span>
                    </div>

                    <hr class="mt-4">

                    <ul class="list-group list-group-flush small">

                        <li class="list-group-item d-flex justify-content-between">
                            <span>Motion detected</span>
                            <span class="text-muted">2 mins ago</span>
                        </li>

                        <li class="list-group-item d-flex justify-content-between">
                            <span>Camera started</span>
                            <span class="text-muted">5 mins ago</span>
                        </li>

                        <li class="list-group-item d-flex justify-content-between">
                            <span>Snapshot captured</span>
                            <span class="text-muted">10 mins ago</span>
                        </li>

                        <li class="list-group-item d-flex justify-content-between">
                            <span>System initialized</span>
                            <span class="text-muted">1 hr ago</span>
                        </li>
                    </ul>

                </div>
            </div>

            <!-- ACTIVITY LOGS -->
            <div class="card shadow border-0 rounded-3">

                <div class="card-header bg-white fw-semibold">
                    <i class="bi bi-sliders me-1"></i> Quick Actions
                </div>

                <div class="card-body p-3" style="max-height: 300px; overflow-y: auto;">


                    <!-- QUICK ACTIONS -->
                    <div class="d-flex mt-2">

                        <button class="btn btn-outline-danger flex-fill me-1">
                            <i class="bi bi-bell-fill me-1"></i> Alarm
                        </button>

                        <button class="btn btn-outline-primary flex-fill me-1">
                            <i class="bi bi-lightbulb-fill me-1"></i> Light
                        </button>

                        <button class="btn btn-outline-secondary flex-fill me-1" id="snapshotBtn">
                            <i class="bi bi-camera-fill me-1"></i> Snapshot
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="row mt-4">
        <div id="detectionResult" class="row g-3"></div>
    </div>

</div>
<?php require 'includes/footer.php'; ?>