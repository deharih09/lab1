<?php
        include(BASE_DIR.DS.'assets/header.php');
        include(BASE_DIR.DS.'assets/sidemenu.php');
?>
 <div class='col-xs-9 content'><!-- main content section -->
                <h2 id='content-title'><?php if (array_key_exists('content-title', $data_set)) echo $data_set['content-title'];
                                else echo "Welcome to Digital Store";?>
                </h2>
                <div class='col-md-6' id='customer-data'></div>

  </div>

<?php
        include(BASE_DIR.DS.'assets/footer.php');
?>

