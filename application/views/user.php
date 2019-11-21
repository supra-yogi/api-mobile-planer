<?php $this->load->view('partial/header.php'); ?>
<?php $this->load->view('partial/sidebar.php'); ?>
        <div id="page-content-wrapper">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-sm-12 col-md-12 col-ld-12">
                        <div class="intro" style="font-weight: bold">
                            LIST USER
                        </div>                        
                    </div>
                    <div class="col-sm-12 col-md-12 col-ld-12">
                        <div class="app-panel">
                            <div class="app-panel-heading">
                                Management User
                            </div>
                            <div class="app-panel-body">
                                  <table id="table_id" class="table table-striped">
                                    <thead>
                                        <tr>
                                            <th class="text-center">No.</th>
                                            <th class="text-center">Username</th>
                                            <th class="text-center">Email</th>
                                            <th class="text-center">Tanggal Register</th>
                                            <th class="text-center">Suspend</th>
                                            <th class="no-sort text-center">Action</th>
                                        </tr>
                                    </thead>
                                </table>
                            </div>

                            <div class="modal fade" id="deleteConfirm" tabindex="-1" role="dialog">
                                <div class="modal-dialog modal-sm" role="document">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                                            <h4 class="modal-title">Confirm Password</h4>
                                        </div>
                                        <div class="modal-body">
                                            <form id="askPassword">
                                                <div class="form-group">
                                                    <label for="">Password</label>
                                                    <input type="password" class="form-control" name="password" id="password" placeholder="password">
                                                    <input type="hidden" id="userId" value="0" name="id">
                                                    <input type="hidden" id="index" value="0" name="index">
                                                    <input type="hidden" id="userEmail" value="<?php echo $this->session->email;?>" name="email">
                                                </div>
                                                <div class="form-group">
                                                    <button type="submit" class="btn btn-danger">Suspend</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div><!-- /.modal-content -->
                                </div><!-- /.modal-dialog -->
                            </div><!-- /.modal -->

                            <div class="modal fade" id="unDeleteConfirm" tabindex="-1" role="dialog">
                                <div class="modal-dialog modal-sm" role="document">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                                            <h4 class="modal-title">Confirm Password</h4>
                                        </div>
                                        <div class="modal-body">
                                            <form id="askPasswordUn">
                                                <div class="form-group">
                                                    <label for="">Password</label>
                                                    <input type="password" class="form-control" name="password" placeholder="password">
                                                    <input type="hidden" id="userIdUn" value="0" name="idUn">
                                                    <input type="hidden" id="indexUn" value="0" name="indexUn">
                                                    <input type="hidden" value="<?php echo $this->session->email;?>" name="email">
                                                </div>
                                                <div class="form-group">
                                                    <button type="submit" class="btn btn-success">Unsuspend</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div><!-- /.modal-content -->
                                </div><!-- /.modal-dialog -->
                            </div><!-- /.modal -->
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
<!-- redirect when button back pressed -->
<script>
    var table = $('#table_id').DataTable({
            "columnDefs": [ {
                "targets": 'no-sort',
                "orderable": false,
            } ]
        });

    $(document).ready( function () {
        $("#askPassword").on("submit", function(e){
            $.ajax({
                url: "<?php echo base_url(); ?>admin/checkPassword",
                type: "POST",
                processData:false,
                contentType:false,
                cache:false,
                data: new FormData(this),
                success: function(json){      
                    try{  
                        var obj = jQuery.parseJSON(json);
                        if(obj['isExist']) {
                            deleteUser($("#userId").val());
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
            e.preventDefault();
        });

        $("#askPasswordUn").on("submit", function(e){
            $.ajax({
                url: "<?php echo base_url(); ?>admin/checkPassword",
                type: "POST",
                processData:false,
                contentType:false,
                cache:false,
                data: new FormData(this),
                success: function(json){      
                    try{  
                        var obj = jQuery.parseJSON(json);
                        if(obj['isExist']) {
                            unDeleteUser($("#userIdUn").val());
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
            e.preventDefault();
        });
    });

    //agar load data saat form di load
    window.onload = getAllUser();

    function getAllUser() {
        $.ajax({
            url: "<?php echo URL_BASE; ?>api/user/",
            type: "GET",
            processData:false,
            contentType:false,
            cache:false,
            success: function(json){      
                try{  
                    var obj = json;
                    table.clear().draw();
                    for (var index = 0; index < obj.length; index++) {
                        var button = 
                            "<button class='btn btn-danger' onclick='showModal("+ obj[index].id +","+ index +")'>" +
                                "<i class='glyphicon glyphicon-remove'></i>" +
                            "</button>" + 
                            "<button style='margin-left: 5px' class='btn btn-success' onclick='showModalRe("+ obj[index].id +","+ index +")'>" +
                                "<i class='glyphicon glyphicon-check'></i>" +
                            "</button>";
                        var suspend = obj[index].suspend == 1 ? 'Ya' : 'Tidak';
                        table.row.add( [
                            index+1,
                            obj[index].username,
                            obj[index].email,
                            obj[index].created_date,
                            suspend,
                            button,
                        ] ).draw();
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

    function showModal(id, index) {
        $("#deleteConfirm").modal();
        var userId = document.getElementById("userId");
        var indexDelete = document.getElementById("index");
        userId.value = id;
        indexDelete.value = index;
    }

    function deleteUser(id) {
        if(confirm('Are you sure suspend this user? ')) {
            var table = $('#table_id').DataTable();
            $.post("<?php echo URL_BASE; ?>api/user/suspend/",
            {
                'id': id
            },
            function(data, status){
                getAllUser();
                $("#deleteConfirm").modal("hide");
            });
        }
    }

    function showModalRe(id, index) {
        $("#unDeleteConfirm").modal();
        var userId = document.getElementById("userIdUn");
        var indexDelete = document.getElementById("indexUn");
        userId.value = id;
        indexDelete.value = index;
    }

    function unDeleteUser(id) {
        if(confirm('Are you sure unsuspend this user? ')) {
            var table = $('#table_id').DataTable();
            $.post("<?php echo URL_BASE; ?>api/user/unSuspend/",
            {
                'id': id
            },
            function(data, status){
                getAllUser();
                $("#unDeleteConfirm").modal("hide");
            });
        }
    }
</script>
</body>
</html>