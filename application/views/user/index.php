<section class="section">
    <div class="row">
        <div class="col-lg-12">

            <div class="card">
                <div class="card-header">
                    <div class="card-title">User</div>
                </div>
                <div class="card-body">
                    <div class="mt-2 mb-2">
                        <button class="btn btn-primary btn-sm" id="tambah"><i class="bi bi-plus-square"></i> Tambah Data</button>
                        <button class="btn btn-secondary btn-sm" id="reload"><i class="bi bi-arrow-repeat"></i> Reload</button>
                    </div>
                    <div class="table-responsive">
                        <table id="datatables" class="table w-100">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Nama</th>
                                    <th>Hp</th>
                                    <th>Email</th>
                                    <th>Username</th>
                                    <th>Level</th>
                                    <th>Created</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>

                            </tbody>

                        </table>
                    </div>
                </div>
            </div>

        </div>
    </div>
</section>

<div class="modal fade" id="modal_user" role="dialog" data-bs-backdrop="false">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Tambah User</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="form" class="form-horizontal">
                <input type="hidden" name="id" id="id">
                <div class="modal-body">
                    <div class="mb-2">
                        <label for="nama">Nama</label>
                        <input name="nama" id="nama" class="form-control" required="" type="text">
                    </div>
                    <div class="mb-2">
                        <label for="hp">No. HP</label>
                        <input name="hp" id="hp" class="form-control" required="" type="text">
                    </div>
                    <div class="mb-2">
                        <label for="email">Email</label>
                        <input name="email" id="email" class="form-control" type="email">
                    </div>
                    <div class="mb-2">
                        <label for="level">Level</label>
                        <select name="level" id="level" class="form-control">
                            <option value="">Pilih Level</option>
                            <option value="1">Super Admin</option>
                            <option value="2">Admin</option>
                            <option value="3">Gudang</option>
                        </select>
                    </div>
                    <div class="mb-2">
                        <label for="username">Username</label>
                        <input name="username" id="username" class="form-control" required="" type="text">
                    </div>
                    <div class="mb-2">
                        <label for="password">Password</label>
                        <input name="password" id="password" class="form-control" required="" min="8" type="text">
                        <small class="info"></small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" id="btnSave" class="btn btn-success btn-sm"><i class="bi bi-save"></i>Simpan</button>
                    <button type="button" class="btn btn-danger btn-sm" data-bs-dismiss="modal">Tutup</button>
                </div>
            </form>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->


<script>
    $(document).ready(function() {
        var table = $('#datatables').DataTable({
            processing: true,
            serverSide: true,
            responsive: true,
            retrieve: true,
            paging: true,
            destroy: true,
            deferRender: true,
            searching: true,
            scrollX: true,
            order: false,
            ajax: {
                url: "user/ajax_table",
                type: "POST",
            },
            columns: [{
                    title: "No",
                    data: 0
                },
                {
                    title: "Nama",
                    data: 2
                },
                {
                    title: "Hp",
                    data: 3
                },
                {
                    title: "Email",
                    data: 4
                },
                {
                    title: "Username",
                    data: 5
                },
                {
                    title: "Level",
                    data: 6
                },
                {
                    title: "Created",
                    data: 7
                },
                {
                    title: "Aksi",
                    data: 8
                }
            ],
            dom: '<"top"lBf>rt<"bottom"ip><"clear">',
            buttons: [{
                    extend: 'copy',
                    text: 'Copy',
                    exportOptions: {
                        columns: ':not(:eq(7))'
                    }
                },
                {
                    extend: 'excel',
                    text: 'Excel',
                    exportOptions: {
                        columns: ':not(:eq(7))'
                    }
                },
                {
                    extend: 'pdf',
                    text: 'PDF',
                    exportOptions: {
                        columns: ':not(:eq(7))'
                    }
                },
                {
                    extend: 'csv',
                    text: 'CSV',
                    exportOptions: {
                        columns: ':not(:eq(7))'
                    }
                },
                {
                    extend: 'print',
                    text: 'Print',
                    exportOptions: {
                        columns: ':not(:eq(7))'
                    }
                }
            ],

        });
        $('.dataTables_length').css('margin-bottom', '10px');

        $("body").on("click", "#reload", function() {
            table.draw();
        })

        $("body").on("click", "#tambah", function() {
            $('#form')[0].reset();
            $("#id").val('');
            $(".info").html('');
            $("#password").prop("required");
            $("#btnSave").html('<i class="bi bi-save"></i> Simpan');
            $("#modal_user").modal("show");
        })


        $("body").on('submit', '#form', function(e) {
            e.preventDefault();
            $('#btnSave').text('Menyimpan...'); //change button text
            $('#btnSave').prop('disabled', true); //set button disable 
            var id = $("#id").val();
            var url = "<?php echo base_url('user/store') ?>";
            if (id != '') {
                url = "<?php echo base_url('user/update') ?>";
            }
            $.ajax({
                url: url,
                secureuri: false,
                cache: false,
                type: "POST",
                data: new FormData(this),
                dataType: "JSON",
                contentType: false,
                processData: false,
                success: function(data) {
                    if (data.success == true) {
                        table.draw();
                        $('#form')[0].reset();
                        $('#modal_user').modal('hide');
                        Swal.fire({
                            title: "Berhasil!",
                            text: data.message,
                            icon: "success",
                            showConfirmButton: false,
                            timer: 1000
                        });
                    } else {
                        Swal.fire({
                            title: "Gagal!",
                            text: data.message,
                            icon: "error",
                            showConfirmButton: true,
                        });
                    }

                    $('#btnSave').text('Simpan'); //change button text
                    $('#btnSave').prop('disabled', false); //set button enable 

                },
                error: function(jqXHR, textStatus, errorThrown) {
                    Swal.fire({
                        title: "Gagal!",
                        text: "Terjadi Kesalahan",
                        icon: "error",
                        showConfirmButton: true,
                    });
                    $('#btnSave').text('Simpan'); //change button text
                    $('#btnSave').prop('disabled', false); //set button enable 
                }
            });
        })


        $("body").on("click", ".edit", function() {
            var id = $(this).data("id");
            $('#form')[0].reset();
            $.ajax({
                url: "<?php echo base_url('user/get_by_id') ?>/" + id,
                type: "GET",
                dataType: "JSON",
                success: function(data) {
                    $('#id').val(data.id);
                    $('#nama').val(data.name);
                    $('#hp').val(data.hp);
                    $('#email').val(data.email);
                    $('#level').val(data.level);
                    $('#username').val(data.username);
                    $("#password").removeAttr("required");
                    $("#btnSave").html('<i class="bi bi-save"></i> Update');
                    $(".info").html('Kosongkan jika tidak ingin mengubah password');
                    $('#modal_user').modal('show');
                    $('.modal-title').text('Ubah Data');
                },
                error: function(jqXHR, textStatus, errorThrown) {
                    Swal.fire({
                        title: "Gagal!",
                        text: "Terjadi Kesalahan",
                        icon: "error",
                        showConfirmButton: true,
                    });
                }
            });
        })

        $("body").on("click", ".delete", function() {
            var id = $(this).data("id");
            Swal.fire({
                title: "Yakin hapus data ini?",
                text: "Data yang dihapus tidak bisa dikembalikan!",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#3085d6",
                cancelButtonColor: "#d33",
                confirmButtonText: "Ya, Hapus"
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: "<?php echo base_url('user/delete') ?>/" + id,
                        type: "get",
                        dataType: "JSON",
                        success: function(data) {
                            if (data.success == true) {
                                table.draw();
                                Swal.fire({
                                    title: "Berhasil!",
                                    text: data.message,
                                    icon: "success",
                                    showConfirmButton: false,
                                    timer: 1000
                                });
                            } else {
                                Swal.fire({
                                    title: "Gagal!",
                                    text: data.message,
                                    icon: "success",
                                    showConfirmButton: true,
                                });
                            }

                        },
                        error: function(jqXHR, textStatus, errorThrown) {
                            Swal.fire({
                                title: "Gagal!",
                                text: "Terjadi Kesalahan",
                                icon: "error",
                                showConfirmButton: true,
                            });
                        }
                    });
                }
            });
        })


    })
</script>