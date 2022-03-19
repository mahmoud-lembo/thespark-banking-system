<?php
$mysqli = new mysqli("localhost","root","","mybank");
if ($mysqli -> connect_errno) {
    echo "Failed to connect to MySQL: " . $mysqli -> connect_error;
    exit();
  }
$errors = array();
$success = false;
if(isset($_POST['submit'])){
    $sql = "SELECT * FROM customers where id = ".$_POST['sender'];
    $row = $mysqli -> query($sql) -> fetch_assoc();
    if($_POST['amount'] < 0){
        array_push($errors, 'Inserted Negative Value');
    }else{
    if($row['balance'] >= $_POST['amount']){
    $sql = "UPDATE customers SET balance=balance-".$_POST['amount']." WHERE id=".$_POST['sender'];
    $mysqli -> query($sql);
    $sql = "UPDATE customers SET balance=balance+".$_POST['amount']." WHERE id=".$_POST['receiver'];
    $mysqli -> query($sql);
    $sql = "INSERT INTO transfers (sender, receiver, amount)
    VALUES ('".$_POST['sender']."', '".$_POST['receiver']."', '".$_POST['amount']."')";
    $mysqli -> query($sql);
    $success = true;
    }else{
    array_push($errors, 'Insufficient Fund');
    }
}
}

if(isset($_GET['id'])){
$sql = "SELECT * FROM customers where id = ".$_GET['id']; // get selected customer
$row = $mysqli -> query($sql);
$row = $row -> fetch_assoc();

$sql2 = "SELECT * FROM customers where id != ".$_GET['id']; // get all customers except the selected 
$rows2 = $mysqli -> query($sql2);
$rows2 -> fetch_all(MYSQLI_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
        <meta name="description" content />
        <meta name="author" content />
        <title>MyBank Transfers</title>
        <!-- Favicon-->
        <link rel="icon" type="image/x-icon" href="assets/favicon.ico" />
        <!-- Bootstrap icons-->
        <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.5.0/font/bootstrap-icons.css" rel="stylesheet" />
        <!-- Core theme CSS (includes Bootstrap)-->
        <link href="css/styles.css" rel="stylesheet" />
    </head>
    <body class="d-flex flex-column">
        <main class="flex-shrink-0">
            <!-- Navigation-->
            <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
                <div class="container px-5">
                    <a class="navbar-brand" href="index.php">MyBank</a>
                    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation"><span class="navbar-toggler-icon"></span></button>
                    <div class="collapse navbar-collapse" id="navbarSupportedContent">
                        <ul class="navbar-nav ms-auto mb-2 mb-lg-0">
                            <li class="nav-item"><a class="nav-link" href="index.php">Home</a></li>
                            <li class="nav-item"><a class="nav-link" href="customers.php">Customers</a></li>
                            <li class="nav-item"><a class="nav-link" href="transactions.php">Transactions</a></li>
                        </ul>
                    </div>
                </div>
            </nav>
            <!-- Page content-->
            <section class="py-5">
                <div class="container px-5">
                    <!-- Contact form-->
                    <div class="bg-light rounded-3 py-5 px-4 px-md-5 mb-5">
                        <div class="text-center mb-5">
                            <div class="feature bg-primary bg-gradient text-white rounded-3 mb-3"><i class="bi bi-envelope"></i></div>
                            <h1 class="fw-bolder">Instant Transfer from <?php echo $row['name']; ?></h1>
                            <p class="lead fw-normal text-muted mb-0">Your Balance: <?php echo $row['balance']; ?>$</p>
                        </div>
                        <div class="row gx-5 justify-content-center">
                            <div class="col-lg-8 col-xl-6">
                            <?php if($success){ echo'<div class="alert alert-success" role="alert">Amount Transferred Successfully</div>';} ?>
                            <?php if(count($errors) > 0){ echo'<div class="alert alert-danger" role="alert">'.$errors[0].'</div>';} ?>
                                <form method="post" id="transferForm" >
                                <input class="form-control" id="sender" name="sender" value="<?php echo $_GET['id']?>" hidden/>
                                    <!-- Name input-->
                                    <div class="form-floating mb-3">
                                        <select class="form-select" id="receiver" name="receiver" aria-label="Enter beneficiary name..." required>
                                        <?php 
      foreach($rows2 as $row2){ ?>
      <option value="<?php echo $row2['id'] ?>"><?php echo $row2['name'] ?></option>
    <?php
      }
      ?>
                                        </select>
                                        <label for="name">Transfer To</label>
                                    </div>
                                    <!-- Amount input-->
                                    <div class="form-floating mb-3">
                                    <input class="form-control" id="amount" name="amount" type="number" placeholder="Enter beneficiary name..." required/>
                                        <label for="phone">Amount In US Dollar $</label>
                                    </div>
                                    <!-- Submit Button-->
                                    <div class="d-grid"><button class="btn btn-primary btn-lg" id="submit" name="submit" type="submit">Transfer</button></div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        </main>
        <!-- Footer-->
        <footer class="bg-dark py-4 mt-auto">
            <div class="container px-5">
                <div class="row align-items-center justify-content-between flex-column flex-sm-row">
                    <div class="col-auto"><div class="small m-0 text-white">Copyright &copy; MyBank 2021</div></div>
                    <div class="col-auto">
                        <a class="link-light small" href="#!">Privacy</a>
                        <span class="text-white mx-1">&middot;</span>
                        <a class="link-light small" href="#!">Terms</a>
                        <span class="text-white mx-1">&middot;</span>
                        <a class="link-light small" href="#!">Contact</a>
                    </div>
                </div>
            </div>
        </footer>
        <!-- Bootstrap core JS-->
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
        <!-- Core theme JS-->
        <script src="js/scripts.js"></script>
    </body>
</html>
<?php
$mysqli -> close();
}else{
header("Location: index.php");
die();
}
?>