<script>
    $(document).ready( function () {
        $('#myTable').DataTable({
            processing:true,
            serverside:true,
            ajax:"{{ url('pegawaiAjax') }}",
            columns:[{
                data:'DT_RowIndex',
                name:'DT_RowIndex',
                orderable:false,
                searchable:false
            },{
                data:'nama',
                name:'Nama'
            },{
                data:'email',
                name:'Email'
            },{
                data:'aksi',
                name:'Aksi'
            }]
        });
    } );

    //Global SETUP

    $.ajaxSetup({
        headers:{
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    //2. Proses SIMPAN
    $('body').on('click', '.tombol-tambah', function(e){
        e.preventDefault();
        $('#modalTambah').modal('show');

        $(".tombol-simpan").off("click"); //supaya data masuk tidak ganda
        $('.tombol-simpan').on('click', function(){
            simpan();
        });
    });

    $('#modalTambah').on('hidden.bs.modal', function() {
        $('#nama').val('');
        $('#email').val('');

        $('.alert-danger').addClass('d-none');
        $('.alert-danger').html('');
        
        $('.alert-success').addClass('d-none');
        $('.alert-success').html('');
    });

     //3. Proses Edit
     $('body').on('click', '.tombol-edit', function(e){
        e.preventDefault();
        var id = $(this).data('id');
        $.ajax({
            url: 'pegawaiAjax/' + id + '/edit',
            type: 'GET',
            success:function(response){
                $('#modalTambah').modal('show');
                $('#nama').val(response.result.nama);
                $('#email').val(response.result.email);

                $(".tombol-simpan").off("click"); //supaya data masuk tidak ganda
                $('.tombol-simpan').on('click', function(){
                    simpan(id);
                });
            }

        });

     });

     //proses delete
     $('body').on('click', '.tombol-delete', function(e){
        e.preventDefault();
        Swal.fire({
            title: 'Yakin mau hapus data ini?',
            showCancelButton: true,
            confirmButtonText: 'Delete',
            }).then((result) => {
            /* Read more about isConfirmed, isDenied below */

            if (result.isConfirmed) {
                var id = $(this).data('id');
                $.ajax({
                    url: 'pegawaiAjax/' +id,
                    type: 'DELETE',
                });
                $('#myTable').DataTable().ajax.reload();
                Swal.fire('Berhasil dihapus!', '', 'success');
            }
        });

        // if (confirm('Yaki mau hapus data?') == true) {}

     });

     //fungsi simpan dan update
     function simpan(id = '') {
        if (id == '') {
            var var_url = 'pegawaiAjax';
            var var_type = 'POST';
        } else{
            var var_url = 'pegawaiAjax/' + id;
            var var_type = 'PUT';
        }
        
        $.ajax({
                url: var_url,
                type: var_type,
                data: {
                    nama : $('#nama').val(),
                    email : $('#email').val()
                },
                success: function(response){
                    if (response.errors ) {
                        $('.alert-danger').removeClass('d-none');
                        $('.alert-danger').html("<ul>");
                        $.each(response.errors.email, function (key, value) {
                            $('.alert-danger').find('ul').append("<li>" + value + "</li>");
                        });
                        $('.alert-danger').append("</ul>");
                    } else {
                        Swal.fire(response.success, '', 'success');
                        $('#modalTambah').modal('hide');
                    }
                    $('#myTable').DataTable().ajax.reload();
                }
        });
     }
 
</script>