@extends('master')

@section('konten')
  <h4>Selamat datang <b>{{Auth::user()->username}}</b>
  <br><br>
  <div class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-lg-12">
                    <div class="card card-primary card-outline">
                        <div class="card-body">
                            <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#add"> Tulis Diary </button>
                            
                            <br><br>
                            @csrf
                            <table class="table table-bordered datatables">
                                <thead class="text-center">
                                    <tr>
                                        <th>No</th>
                                        <th>Diary</th>
                                        <th>Tanggal</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="add">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <label class="modal-title">Tambah Diary</label>
                </div>

                <div class="modal-body">
                    <ul id="updateForm_errList"></ul>
                    <input type="hidden" id="id">

                    <div class="form-group">
                        <label for="Title">Title </label>
                        <input type="text" class="form-control" name="title" id="title" placeholder="Input Title..."  maxlength="50">
                    </div>
                    <div class="form-group">
                        <label for="Name">Notes </label>
                        <textarea class="form-control" name="notes" id="notes" placeholder="Input Note...">                       
                        </textarea>
                    </div>

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary reset">Reset</button>
                    <button type="button" class="btn btn-primary create">Simpan</button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal" id="updateDiary">
        <div class="modal-dialog">
            <div class="modal-content">
                <!-- Modal Header -->
                <div class="modal-header">
                    <label class="modal-title">Ubah Diary</label>
                </div>
                <form id="updateForm" action="#" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    <div class="modal-body">
                        <ul id="updateForm_errList"></ul>
                        <input type="hidden" id="id">

                        <div class="form-group">
                            <label for="Title">Title </label>
                            <input type="text" class="form-control" name="title" id="title" maxlength="50">
                        </div>
                        <div class="form-group">
                            <label for="Name">Notes </label>
                            <textarea class="form-control" name="notes" id="notes">                       
                            </textarea>
                        </div>

                    </div>
                </form>
                <div class="modal-footer">
                    <button type="button" class="btn btn-success update">Change</button>
                    <button type="button" class="btn btn-info modelClose" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <script type="text/javascript">
        $(function() {
            $.ajaxSetup({
                headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            var table = $('.datatables').DataTable({
                autoWidth: false,
                processing: true,
                serverSide: true,
                ajax: "{{ route('diary.index') }}",
                method: 'GET',
                columns: [
                    {data: 'DT_RowIndex', name: 'DT_RowIndex', width: '5%'},
                    {data: 'title', name: 'title'},
                    {data: 'createdUtc', name: 'createdUtc'},
                    {data: 'action', name: 'action'},
                ],
                order: [
                    [0, 'desc'],
                ],
            });
        });

        $(document).ready(function () {
            $(".reset").click( function(){
                $('#title').val("");
                $('#notes').val("");
            });

            $(document).on('click', '.create', function (e) {
                e.preventDefault();

                var data = {
                    'title': $('#title').val(),
                    'notes' : $('#notes').val(),
                }

                $(this).removeData();

                $.ajax({
                    type: "POST",
                    url: "{{ route('diary.store') }}",
                    data: data,
                    dataType: "json",
                    success: function(response){
                        if(response.status == 400){
                            $('#saveForm_errList').html("");
                            $('#saveForm_errList').addClass('alert alert-danger');
                            $.each(response.errors, function(key, err_values) {
                                $('#saveForm_errList').append('<li>'+err_values+'</li>');
                            });
                        }else if(response.status == 200)
                        {
                            $('#saveForm_errList').html("");
                            $('#success_message').addClass('alert alert-success');
                            $('#success_message').text(response.messages);
                            $('#add').modal('hide');
                            // $('#addCompany').modal('show');
                            $('#add').find("input").val("");
                            $('.modal-backdrop').remove();
                            var table = $('.datatables').DataTable();
                            table.ajax.reload();
                        }
                    }
                });
            });
        });

        //edit
        $(document).on('click', '.editDiary', function (e){

            e.preventDefault();

            var id = $(this).data('id');

            $('#updateDiary').modal('show');

            $.ajax({
                type: "GET",
                url: "{{ route('diary.index') }}" + '/' + id + '/edit',
                success: function(response){
                    if (response.status == 404) {
                        $('#updateForm_errList').addClass('alert alert-success');
                        $('#updateForm_errList').text(response.message);
                        $('.editDiary').modal('hide');
                    }
                    else
                    {
                        $('#btnDelete').html(response.html)
                        $(".reset-update").click( function(){
                            $('#updateForm').find('#title').val("");
                            $('#updateForm').find('#notes').val("");
                        });
                        $("#id").val(id);
                        $('#updateDiary').find("#title").val(response.diary.title);
                        $('#updateDiary').find("#notes").val(response.diary.notes);
                    }
                }
            })
        });

        $(document).on('click', '.update', function(e) {
            e.preventDefault();

            $(this).text('Memperbaharui...');

            var id = $('#id').val();
            console.log(id)

            let formData = new FormData($('#updateForm')[0]);
            console.log(formData)

            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            $.ajax({
                url: "/diary/" + id,
                method: 'POST',
                data:
                formData,
                dataType: "json",
                contentType: false,
                processData: false,
                success: function(response){
                    if(response.status == 400)
                    {
                        $('#updateForm_errList').html("");
                        $('#updateForm_errList').addClass('alert alert-danger');
                        $.each(response.errors, function (key, err_value) {
                            $('#updateForm_errList').append('<li>'+err_value+'</li>');
                        });
                        $('.update').text('update');
                    }
                    else
                    {
                        $('#updateForm_errList').html("");
                        $('#success_message').addClass('alert alert-success');
                        $('#success_message').text(response.messages);
                        $('#updateDiary').find('input').val('');
                        $('.update').text('update');
                        $('#updateDiary').modal('hide');
                        $('.modal-backdrop').remove();
                        var table = $('.datatables').DataTable();
                        table.ajax.reload();
                        location.reload(); //untuk auto refresh halaman
                    }
                }
            })
        })

        $('body').on('click', '.archiveDiary', function () {
            var id = $(this).data('id');
            $.get("{{ route('diary.index') }}" +'/' + id +'/archive_update', function (data) {
                $('#archive_update').val(data.archive_update);  
                location.reload();
            })
        });

        $('body').on('click', '.deleteDiary', function (){
        var id = $(this).data("id");
        var result = confirm("Hapus Data?");
        if(result){
            $.ajax({
                type: "DELETE",
                url: "{{ route('diary.index') }}"+'/'+id,
                success: function (data) {
                    table.draw();
                },
                error: function (data) {
                    console.log('Error:', data);
                }
            });
            location.reload();
        }else{
            return false;
        }
    });
    </script>
@endsection