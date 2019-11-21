<?php $this->load->view('partial/header.php'); ?>
<?php $this->load->view('partial/sidebar.php'); ?>
        <div id="page-content-wrapper">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-sm-12 col-md-12 col-ld-12">
                        <div class="intro" style="font-weight: bold">
                            LIST PLANNING
                        </div>                        
                    </div>
                    <div class="col-sm-12 col-md-12 col-ld-12">
                        <div class="app-panel">
                            <div class="app-panel-body">
                                  <table id="table_id" class="table table-striped">
                                    <thead>
                                        <tr>
                                            <th class="text-center">No.</th>
                                            <th class="text-center">User</th>
                                            <th class="text-center">Email</th>
                                            <th class="text-center">Perencanaan</th>
                                            <th class="text-center">Tanggal</th>
                                            <th class="text-center">Jangka Waktu</th>
                                            <th class="text-center">Future Cost</th>
                                            <th class="text-center">Bunga</th>
                                            <th class="text-center">Biaya Admin</th>
                                        </tr>
                                    </thead>
                                </table>
                            </div>
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

    window.onload = getAllUser();
    function getAllUser() {
        $.ajax({
            url: "<?php echo URL_BASE; ?>api/planning/getAll",
            type: "GET",
            processData:false,
            contentType:false,
            cache:false,
            success: function(json){      
                try{  
                    var obj = json;
                    table.clear().draw();
                    for (var index = 0; index < obj.length; index++) {
                        table.row.add( [
                            index+1,
                            obj[index].username,
                            obj[index].email,
                            obj[index].goalName,
                            obj[index].created_date,
                            obj[index].jangkaWaktu + " bulan",
                            obj[index].futureCost,
                            obj[index].interestRate,
                            obj[index].biayaAdmin,
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
</script>
</body>
</html>