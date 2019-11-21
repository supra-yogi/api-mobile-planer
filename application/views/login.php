<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>MPlanner - Login</title>

    <!--Bootstrap  -->
    <link rel="stylesheet" href="<?php echo URL_Modules; ?>bootstrap/dist/css/bootstrap.min.css">
    <!--Custom Stylesheet  -->
    <link rel="stylesheet" href="<?php echo URL_CSS; ?>login.css">
</head>
<body class="app screen">
    <div class="login-page">
      <div class="login-panel  animated fadeInUp  top">
        <div class="login-panel-heading">
          <div class="text-center" style="margin-bottom: 30px;">
            <img src="<?php echo URL_Img; ?>logo_desc.png" alt="icon">
          </div>
        </div>
        <div class="login-panel-body">
          <form id="myForm">
            <div class="form-group">                    
              <input type="email" class="form-control input-control" id="email" value="<?php echo $c_email; ?>" name="email" placeholder="Email" required="">
            </div>
            <div class="form-group">                    
              <input type="password" class="form-control input-control" id="password" value="<?php echo $c_pass; ?>" name="password" placeholder="Password" required="">
            </div>
            <div class="form-group">
              <div class="checkbox">
                <label>
                  <input type="checkbox" name="remember" <?php if($remember == true) echo 'checked'; ?> > Remember me
                </label>
              </div>
            </div>
            <button type="submit" class="btn btn-primary btn-lg btn-block">Sign In</button>
          </form>
          <div class="forgot">
              <a href="<?php echo URL_BASE; ?>admin/forgetpassword">Forget Password ?</a>
          </div>
      </div>
    </div>
  </div>
  <script src="<?php echo URL_Modules; ?>jquery/dist/jquery.min.js"></script>
    <script>
        $(".screen").height($(window).height());

        $(window).resize(function () {
            $(".screen ").height($(window).height());
        });
    </script>

    <script>
        $(document).ready(function(){
            $("#myForm").on("submit", function(e){
                $.ajax({
                    url : "<?php echo URL_BASE; ?>admin/login",
                    type : "POST",
                    processData:false,
                    contentType:false,
                    cache:false,
                    data : new FormData(this),
                    success: function(json) {      
                        try{  
                            var obj = jQuery.parseJSON(json);
                            alert(obj['message']);
                            if (obj['isLogin']) {
                                window.open("<?php echo URL_BASE; ?>admin/dashboard", "_self");
                            }
                        }catch(e) {  
                            alert('Exception while request..');
                        }  
                    },
                    error : function(xhr, status, error) {
                        var err = eval("(" + xhr.responseText + ")");
                        alert(err.Message);
                    }
                });

                e.preventDefault();
            });
        });
    </script>
</body>
</html>