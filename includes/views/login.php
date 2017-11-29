<?php 
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        if(count($_POST) ==  4) {
            echo $userHandler->loginUser($_POST);
        }
        elseif (count($_POST) == 5) {;
            if(empty($_POST['username']) ||  empty($_POST['email']) || empty($_POST['password']) || empty($_POST['confirm-password'])){
                showError('Please fill out all details');
            
            }
            else {
                if($_POST['password'] == $_POST['confirm-password']) {
                    $userHandler->makeUser($_POST);   
                }
                else { 
                    showError('Passwords do not match');
                }
            }
        }
    }
?><head>
    <!-- Latest compiled and minified CSS -->
    <link rel="stylesheet" href="styles/bootstrap.min.css">
    <!-- Optional theme -->
    <link rel="stylesheet" href="styles/bootstrap-theme.min.css">
    <link rel="stylesheet" href="styles/login.css">
</head>
<?php    
    function showError($message) {;
?>
        <div id="myModal" class="modal" tabindex="-1" role="dialog">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header titleBar">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">x</span></button>
                    </div>
                    <div class="modal-body">
                        <p><?php echo $message; ?></p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn" data-dismiss="modal" onClick="hideModal()">Close</button>
                    </div>
                </div>
            </div>
        </div>
<?php
    }
?>
<section id="myTabs">
    <div class="titleBar">
        <div class="titleBarLeft"></div>
        Login
        <div class="titleBarRight"></div>
    </div>

  <!-- Tab panes -->
    <div id="loginContainer" class="tab-content">
        <div role="tabpanel" class="tab-pane fade in active" id="login">
            <form id="login-form" method="post" role="form" style="display: block;">
                <div class="form-group">
				    <label for="username">Username: </label>
                    <input type="text" name="username" id="username" class="form-control" value="">
				</div>
				<div class="form-group">
				    <label for="password">Password: </label>
				    <input type="password" name="password" id="password" class="form-control">
				</div>
				<div class="form-group text-center">
				    <label for="remember"> Remember Me</label>
						  <input type="hidden" name="remember" value="off" /> 
					<input type="checkbox" id="remember" class="" name="remember" id="remember">
			    </div>
                <div class="form-group">
				    <div class="row">
						<div class="col-sm-6 col-sm-offset-3">
							<input type="submit" name="login-submit" id="login-submit" class="form-control btn btn-login" value="Log In">
						</div>
					</div>
				</div>
				<div class="form-group">
					<div class="row">
						<div class="col-lg-12">
							<div class="text-center">
								<a href="#" tabindex="5" class="forgot-password">Forgot Password?</a>
				            </div>
						</div>
				    </div>
				</div>
            </form>
        </div>
        <div role="tabpanel" class="tab-pane fade" id="register">
            <form id="register-form" method="post" role="form">
				<div class="form-group">
				    <label for="username">Username: </label>
				    <input type="text" name="username" id="username"     class="form-control" value="">
				</div>
                <div class="form-group">
				    <label for="username">E-mail: </label>
					<input type="email" name="email" id="email"class="form-control"value="">
				</div>
				<div class="form-group">
				    <label for="username">Password: </label>
				    <input type="password" name="password" id="password" class="form-control">
				</div>
				<div class="form-group">
				    <label for="username">Confirm password: </label>
					<input type="password" name="confirm-password" id="confirm-password" class="form-control">
				</div>
				<div class="form-group">
					<div class="row">
						<div class="col-sm-6 col-sm-offset-3">
							<input type="submit" name="register-submit" id="register-submit" class="form-control btn btn-login" value="Register Now">
						</div>
					</div>
				</div>
			</form>
        </div>
        
                <!-- Nav tabs -->
    <ul id="navTabs" class="nav nav-tabs" role="tablist">
        <li role="presentation" class="active"><a href="#login" aria-controls="login" role="tab" data-toggle="tab">Login</a></li>
        <li role="presentation"><a href="#register" aria-controls="register" role="tab" data-toggle="tab">Register</a></li>
        </ul>
    </div>
</section>
            
