<?php 
    session_start();
    if (!isset($_SESSION['user_id'])) {
        header("Location: agri_error.php?type=unauthorized");
        exit();
    }
    require 'includes/header.php'; 
?>

<div class="p-4">

    <!-- HEADER -->
    <div class="mb-4 d-flex justify-content-between align-items-center">
        <div>
            <h4 class="fw-bold mb-1">Automation</h4>
            <p class="text-muted mb-0">Smart rules and scheduled actions</p>
        </div>

        <button class="btn btn-primary btn-sm">
            <i class="bi bi-plus"></i> Add Automation
        </button>
    </div>


    <!-- ================= SCHEDULES ================= -->
    <h6 class="fw-semibold mb-3">Schedules</h6>

    <div class="row g-3 mb-4">

        <!-- SCHEDULE CARD -->
        <div class="col-md-4">
            <div class="card shadow-sm border-0 p-3">

                <div class="d-flex justify-content-between align-items-center mb-2">
                    <span class="fw-semibold">Night Monitoring</span>

                    <!-- TOGGLE -->
                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" checked>
                    </div>
                </div>

                <small class="text-muted">6:00 PM → 5:00 AM</small><br>
                <small class="text-muted">Repeat: Daily</small>

                <hr>

                <div class="d-flex justify-content-between">
                    <button class="btn btn-sm btn-outline-secondary">Edit</button>
                    <button class="btn btn-sm btn-outline-danger">Delete</button>
                </div>

            </div>
        </div>

    </div>


    <!-- ================= TRIGGER RULES ================= -->
    <h6 class="fw-semibold mb-3">Trigger Rules</h6>

    <div class="row g-3">

        <!-- RULE CARD -->
        <div class="col-md-4">
            <div class="card shadow-sm border-0 p-3">

                <!-- HEADER -->
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <span class="fw-semibold">Motion Alert</span>

                    <!-- TOGGLE -->
                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" checked>
                    </div>
                </div>

                <!-- RULE -->
                <div class="bg-light rounded p-2 mb-2 small">
                    <strong>IF</strong> Motion Detected <br>
                    <strong>THEN</strong> Activate Buzzer
                </div>

                <small class="text-muted">Instant response when movement is detected</small>

                <hr>

                <div class="d-flex justify-content-between">
                    <button class="btn btn-sm btn-outline-secondary">Edit</button>
                    <button class="btn btn-sm btn-outline-danger">Delete</button>
                </div>

            </div>
        </div>


        <!-- RULE 2 -->
        <div class="col-md-4">
            <div class="card shadow-sm border-0 p-3">

                <div class="d-flex justify-content-between align-items-center mb-2">
                    <span class="fw-semibold">Night Lighting</span>

                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" checked>
                    </div>
                </div>

                <div class="bg-light rounded p-2 mb-2 small">
                    <strong>IF</strong> Night Time <br>
                    <strong>THEN</strong> Turn Lights ON
                </div>

                <small class="text-muted">Automatically lights up area at night</small>

                <hr>

                <div class="d-flex justify-content-between">
                    <button class="btn btn-sm btn-outline-secondary">Edit</button>
                    <button class="btn btn-sm btn-outline-danger">Delete</button>
                </div>

            </div>
        </div>

    </div>

</div>

<?php require 'includes/footer.php'; ?>