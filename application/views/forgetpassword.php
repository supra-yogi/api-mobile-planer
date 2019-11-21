<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>MPlanner - Forget</title>

    <!--Bootstrap  -->
    <link rel="stylesheet" href="<?php echo URL_Modules; ?>bootstrap/dist/css/bootstrap.min.css">
    <!--Custom Stylesheet  -->
    <link rel="stylesheet" href="<?php echo URL_CSS; ?>login.css">
</head>
<body class="app screen">    
    <div class="login-page">
        <div class="login-panel animated fadeInUp top">
            <div class="login-panel-heading">
                <h3>
                    <a href="#">Forget Password</a>
                </h3>
                <small>Please enter your email address. You will receive an email with new password.</small>
            </div>
            <div class="login-panel-body">
                <form id="myForm">
                  <div class="form-group">                    
                    <input type="email" class="form-control input-control" id="email" name="email" placeholder="Email">
                  </div>
                  <button type="submit" class="btn btn-primary btn-lg btn-block">Get New Password</button>
                </form>
                <div class="forgot">
                    <a href="<?php echo URL_BASE; ?>admin/"> ← Login</a>
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
                    url : "<?php echo URL_BASE; ?>admin/resetpassword",
                    type : "POST",
                    processData:false,
                    contentType:false,
                    cache:false,
                    data : new FormData(this),
                    success: function(json) {      
                        try{  
                            var obj = jQuery.parseJSON(json);
                            alert(obj['message']);
                            if (obj['isSubmited']) {
                                window.open("<?php echo URL_BASE; ?>admin/", "_self");
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