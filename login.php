<?php
session_start();
include 'includes/functions.php';

// default error message (shown under the title)
$login_error = '';

// If form submitted:
if (isset($_POST['employee_id']) && isset($_POST['password'])) {

    $emp_id  = $_POST['employee_id'];
    $pass    = $_POST['password'];

    // Build login API URL (GET with query params)
    // ex:  $api_url . "/login?username=admin001&password=admin"
    $login_url = $api_url . "/login?username=" . urlencode($emp_id) . "&password=" . urlencode($pass);

    // Hit API
    $raw = get_api_data($login_url, $api_key);
    $resp = json_decode($raw, true);

    if (!is_array($resp)) {
        // API didn't return valid JSON
        $login_error = "Server error. Please contact admin.";
    } else if ($resp['status'] !== 'success') {
        // API said login failed or some other error
        // some APIs return 'message', so try to show it
        $login_error = isset($resp['message']) ? $resp['message'] : "Invalid credentials.";
    } else {
        // Success path
        $user_data = $resp['data'];

        // Map API response -> session
        // Adjust these keys if your /login returns different field names
        $_SESSION['LOGI_EMP_ID']          = $user_data['employee_id'] ?? '';
        $_SESSION['LOGI_EMP_NAME']        = $user_data['employee_name'] ?? '';
        $_SESSION['LOGI_EMP_EMAIL']       = $user_data['employee_email'] ?? '';
        $_SESSION['LOGI_USER_ROLE_ID']    = $user_data['role_id'] ?? '';
        $_SESSION['LOGI_USER_ROLE_NAME']  = $user_data['role_name'] ?? ''; // if your API returns role_name
        // you can add more session vars here if needed

        // If we actually got an ID, consider the login successful
        if ($_SESSION['LOGI_EMP_ID'] !== '') {
            echo "<script>window.location.href='index'</script>";
            exit;
        } else {
            // edge case: API said success but didn't send usable data
            $login_error = "Login data incomplete. Please contact admin.";
        }
    }
}

// If already logged in (session already set), go to dashboard directly
if (isset($_SESSION['LOGI_EMP_ID']) && $_SESSION['LOGI_EMP_ID'] !== '') {
    echo "<script>window.location.href='index'</script>";
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>Renewable Energy Systems Limited: Login</title>
  <link rel="stylesheet" type="text/css" href="css/login.css?V=1">
</head>

<body>
  <div class="login-root">
    <div class="box-root flex-flex flex-direction--column" style="min-height: 100vh;flex-grow: 1;">
      <div class="loginbackground box-background--white padding-top--64">
        <div class="loginbackground-gridContainer">
          <div class="box-root flex-flex" style="grid-area: top / start / 8 / end;">
            <div class="box-root" style="background-image: linear-gradient(white 0%, rgb(247, 250, 252) 33%); flex-grow: 1;">
            </div>
          </div>
          <div class="box-root flex-flex" style="grid-area: 4 / 2 / auto / 5;">
            <div class="box-root box-divider--light-all-2 animationLeftRight tans3s" style="flex-grow: 1;"></div>
          </div>
          <div class="box-root flex-flex" style="grid-area: 7 / start / auto / 4;">
            <div class="box-root box-background--blue animationLeftRight" style="flex-grow: 1;"></div>
          </div>
          <div class="box-root flex-flex" style="grid-area: 8 / 4 / auto / 6;">
            <div class="box-root box-background--gray100 animationLeftRight tans3s" style="flex-grow: 1;"></div>
          </div>
          <div class="box-root flex-flex" style="grid-area: 2 / 15 / auto / end;">
            <div class="box-root box-background--gray100 animationRightLeft tans4s" style="flex-grow: 1;"></div>
          </div>
          <div class="box-root flex-flex" style="grid-area: 3 / 14 / auto / end;">
            <div class="box-root box-background--blue animationRightLeft" style="flex-grow: 1;"></div>
          </div>
          <div class="box-root flex-flex" style="grid-area: 4 / 17 / auto / 20;">
            <div class="box-root box-background--gray100 animationRightLeft tans4s" style="flex-grow: 1;"></div>
          </div>
          <div class="box-root flex-flex" style="grid-area: 5 / 14 / auto / 17;">
            <div class="box-root box-divider--light-all-2 animationRightLeft tans3s" style="flex-grow: 1;"></div>
          </div>
        </div>
      </div>

      <div class="box-root padding-top--24 flex-flex flex-direction--column" style="flex-grow: 1; z-index: 9;">
        <div class="box-root padding-top--48 padding-bottom--24 flex-flex flex-justifyContent--center">
          <h1>
            <a href="https://resindia.co.in/" rel="dofollow">
              Renewable Energy Systems Limited
            </a>
          </h1>
        </div>

        <div class="formbg-outer">
          <div class="formbg">
            <div class="formbg-inner padding-horizontal--48">
              <!-- Error message area -->
              <?php if ($login_error !== ''): ?>
                <div style="color: #b00020; font-size: 14px; margin-bottom: 16px;">
                  <?php echo htmlspecialchars($login_error); ?>
                </div>
              <?php endif; ?>

              <form id="stripe-login" method="POST" action="login">
                <div class="field padding-bottom--24">
                  <label for="employee_id">Employee ID</label>
                  <input type="text" name="employee_id" required>
                </div>

                <div class="field padding-bottom--24">
                  <div class="grid--50-50">
                    <label for="password">Password</label>
                    <!-- <div class="reset-pass">
                      <a href="#">Forgot your password?</a>
                    </div> -->
                  </div>
                  <input type="password" name="password" required id="passwordField">
                </div>

                <div class="field padding-bottom--24" style="text-align:right; font-size:13px;">
                  <a href="#" style="text-decoration:none; color:#555;">Forgot password?</a>
                </div>

                <div class="field padding-bottom--24">
                  <input type="submit" name="submit" value="Login">
                </div>
              </form>
            </div>
          </div>

          <div class="footer-link padding-top--24">
            <div class="listing padding-top--24 padding-bottom--24 flex-flex center-center">
              <span><a href="#">© Renewable Energy Systems Ltd. 2025 | All Rights Reserved</a></span>
            </div>
          </div>
        </div>
      </div>

    </div>
  </div>

  <!-- optional: simple show/hide password like your reference -->
  <script>
    // if you later add an <i class="fa-eye"...> you can hook it here
    // keeping this ready so you can drop in an icon next to the password field
    // const passwordField = document.getElementById('passwordField');
    // const eyeIcon = document.querySelector('.fa-eye');
    // if (eyeIcon && passwordField) {
    //   eyeIcon.addEventListener('click', () => {
    //     if (passwordField.type === 'password') {
    //       passwordField.type = 'text';
    //       eyeIcon.classList.remove('fa-eye');
    //       eyeIcon.classList.add('fa-eye-slash');
    //     } else {
    //       passwordField.type = 'password';
    //       eyeIcon.classList.remove('fa-eye-slash');
    //       eyeIcon.classList.add('fa-eye');
    //     }
    //   });
    // }
  </script>

</body>
</html>
