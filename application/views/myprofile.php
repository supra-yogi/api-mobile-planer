<?php $this->load->view('partial/header.php'); ?>
<?php $this->load->view('partial/sidebar.php'); ?>
        <div id="page-content-wrapper">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-sm-12 col-md-12 col-ld-12">
                        <div class="intro" style="font-weight: bold">
                            MY PROFILE
                        </div>                        
                    </div>
                    <div class="col-sm-12 col-md-12 col-ld-12">
                        <div class="app-panel">
                            <div class="app-panel-body">
                                <form class="form-horizontal" id="myForm">
                                  <div class="box-body">
                                    <div class="form-group">
                                      <label for="username" class="col-sm-2 control-label">*Username</label>
                                      <div class="col-sm-4">
                                        <input type="text" value="<?php echo $data[0]->username ?>" id="username" name="username" class="form-control" placeholder="Username" required="" />
                                      </div>
                                    </div>
                                    <div class="form-group">
                                      <label for="email" class="col-sm-2 control-label">*Email</label>
                                      <div class="col-sm-4">
                                        <input type="email" value="<?php echo $data[0]->email ?>" id="email" name="email" class="form-control" placeholder="Email" required="" />
                                      </div>
                                    </div>
                                    <div class="form-group">
                                      <div class="col-sm-4 col-sm-offset-2">
                                        <input id="changepassword" name="changepassword" type="checkbox"/>
                                        <label for="changepassword">Change Password</label>
                                      </div>
                                    </div>
                                    <div class="form-group">
                                      <label for="currentpassword" class="col-sm-2 control-label">*Current Password</label>
                                      <div class="col-sm-4">
                                        <input type="password" value="" id="currentpassword" name="currentpassword" class="form-control" placeholder="Current Password" disabled="" />
                                      </div>
                                    </div>
                                    <div class="form-group">
                                      <label for="newpassword" class="col-sm-2 control-label">*New Password</label>
                                      <div class="col-sm-4">
                                        <input type="password" value="" id="newpassword" name="newpassword" class="form-control" placeholder="New Password" disabled="" />
                                      </div>
                                    </div>
                                    <div class="form-group">
                                      <div class="col-sm-4 col-sm-offset-2">
                                        <input type="hidden" value="<?php echo $data[0]->id ?>" id="id" name="id" class="form-control" />
                                        <button type="submit" class="btn btn-lg btn-primary"><i class="fa fa-save"></i> SAVE</button>
                                      </div>
                                    </div>
                                  </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
<!-- redirect when button back pressed -->
<script>
    $(document).ready( function () {
        var checkbox = document.getElementById('changepassword');
        $("#myForm").on("submit", function(e){
            if (checkbox.checked) {
                $.ajax({
                    url: "<?php echo base_url(); ?>admin/checkPassword",
                    type: "POST",
                    data: {
                        'email': $('#email').val(),
                        'password': $("#currentpassword").val()
                    },
                    success: function(json){      
                        try{  
                            var obj = jQuery.parseJSON(json);
                            if(obj['isExist']) {
                                save();
                            } else {
                                alert('wrong password');
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
            } else {
                save();
            }
            e.preventDefault();
        });

        function save() {
            var checkbox = document.getElementById('changepassword');
            var chk = checkbox.checked ? true : false;

            $.ajax({
                url: "<?php echo base_url(); ?>admin/save",
                type: "POST",
                data:{
                    'id': $("#id").val(),
                    'username': $("#username").val(),
                    'email': $("#email").val(),
                    'password': $("#newpassword").val(),
                    'changepassword': chk,
                },
                success: function(json){      
                    try{  
                        var obj = jQuery.parseJSON(json);
                        if(obj['isSubmited']) {
                            alert(obj['message']);
                            location.reload();
                        } else {
                            alert(obj['message']);
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
        }

        document.getElementById('changepassword').onchange = function() {
            document.getElementById('currentpassword').disabled = !this.checked;
            document.getElementById('newpassword').disabled = !this.checked;
            if (this.checked) {
                $("#currentpassword").attr('required', '');    //turns required on
                $("#newpassword").attr('required', '');    //turns required on
            } else {
                $("#currentpassword").removeAttr('required');    //turns required off
                $("#newpassword").removeAttr('required');    //turns required off
            }
        };
    });
</script>
</body>
</html>