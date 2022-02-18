<?php

session_start();
require("mainconfig.php");

if (isset($_SESSION['user'])) {
    $sess_username = $_SESSION['user']['username'];
    $check_user = mysqli_query($db, "SELECT * FROM users WHERE username = '$sess_username'");
    $data_user = mysqli_fetch_assoc($check_user);
    if (mysqli_num_rows($check_user) == 0) {
        header("Location: " . $cfg_baseurl . "account/logout");
    } else if ($data_user['status'] == "Suspended") {
        header("Location: " . $cfg_baseurl . "account/logout");
    } else if ($data_user['level'] != "Developers") {
        header("Location: " . $cfg_baseurl);
    } else {


        include("lib/header.php");

?>

        <!-- Main Content -->
        <div class="main-content">
            <section class="section">
                <div class="section-header">
                    <h1>Changelogs</h1>

                </div>

                <div class="section-body">


                    <div class="row">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header">
                                    <h4>Current Version V1.0</h4>
                                </div>
                                <div class="card-body">
                                    <div class="list-unstyled list-unstyled-border mt-4">
                                        <div class="media">
                                            <div class="media-icon"><i class="far fa-circle"></i></div>
                                            <div class="media-body">
                                                <h6>V1.0</h6>
                                                <p></p>
                                                <p>17-02-2022<br>
                                                    no latest updates
                                                </p>
                                            </div>
                                        </div>


                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        </div>

<?php
        include("lib/footer.php");
    }
} else {
    header("Location: " . $cfg_baseurl);
}
?>