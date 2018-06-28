<html>

<?php
$base = "http://localhost/inspeksimonitoring/";

session_start();

// if($_SESSION['status'] == "login admin"){
//     header("location:". $base."login");
// }

?>
    <head>
        <link rel="stylesheet" href="<?php echo $base; ?>assets/css/bootstrap.min.css">    
        <link rel="stylesheet" href="<?php echo $base; ?>assets/css/material-dashboard.css">
        <link rel="stylesheet" type="text/css" href="https://fonts.googleapis.com/css?family=Roboto:300,400,500,700|Roboto+Slab:400,700|Material+Icons" />
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/latest/css/font-awesome.min.css" />
    </head>
    <body>
        <div class="row" style="margin-top:80px">
            <div class="col-md-4 col-md-offset-4">
                <div class="card">
                    <div class="card-header" style="background-color: #FD9C17">
                        <h3 style="text-align: center; font-weight: bold; color: #FFF">LOGIN</h3>
                    </div>
                    <div class="card-body">
                        <div class="col-md-10 col-md-offset-1">
                        <?php if(isset($_SESSION['err_message'])) { echo "<br/><p style='text-align: center; font-weight: bold;color: red;'>" . $_SESSION['err_message'] ."</small>";unset($_SESSION['err_message']);} ?>
                        <form action="doLogin.php" method="POST">
                            <div class="form-group">
                                <input type="text" class="form-control" id="nip" placeholder="Nip" name="nip" autofocus="" required="">
                            </div>
                            <div class="form-group">
                                <input type="password" class="form-control" id="password" placeholder="Password" name="password" required="">
                            </div>
                            <button type="submit" class="btn btn-success">Masuk</button>
                        </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </body>
</html>