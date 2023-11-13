@extends('layouts.layout')
@section('content')
    <div class="post d-flex flex-column-fluid" id="kt_post">
        <!--begin::Container-->
        <div id="kt_content_container" class="container">
            <div class="row">
                <div class="card">
                    <div class="card-body p-0">
                        <div class="mt-5">
                            <form class="form-data" id="cross-validation-form">
                                <div class="fs-4 text row mb-3">
                                    <label for="informationgain" class="col-sm-2 col-form-label">Information
                                        Gain</label>
                                    <div class="col-sm-2">
                                        <input type="number" class="form-control" id="informationgain">
                                    </div>
                                </div>
                                <div class="fs-4 text row mb-3">
                                    <label for="crosValidation" class="col-sm-2 col-form-label">Cross Validation</label>
                                    <div class="col-sm-2">
                                        <input type="number" class="form-control" id="crosValidation">
                                    </div>
                                </div>
                                <div class="d-flex my-5">
                                    <button class="btn btn-primary btn-sm " id="button-side-form"></i> Uji Data</button>
                                </div>
                            </form>
                            <div class="fs-4 row">
                                <label for="inputakurasi" class="col-sm-2 col-form-label">Akurasi</label>
                                <div class="col-sm-2">
                                    <input type="percent" class="form-control" disabled id="inputakurasi">
                                </div>
                            </div>
                        </div>
                        <div class="d-flex justify-content-end mt-5">
                            <span class="svg-icon svg-icon-1">
                                <svg style="position: relative;left: 34px; top: 10px;" xmlns="http://www.w3.org/2000/svg"
                                    xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px"
                                    viewBox="0 0 24 24" version="1.1">
                                    <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                        <rect x="0" y="0" width="24" height="24"></rect>
                                        <path
                                            d="M14.2928932,16.7071068 C13.9023689,16.3165825 13.9023689,15.6834175 14.2928932,15.2928932 C14.6834175,14.9023689 15.3165825,14.9023689 15.7071068,15.2928932 L19.7071068,19.2928932 C20.0976311,19.6834175 20.0976311,20.3165825 19.7071068,20.7071068 C19.3165825,21.0976311 18.6834175,21.0976311 18.2928932,20.7071068 L14.2928932,16.7071068 Z"
                                            fill="#000000" fill-rule="nonzero" opacity="0.3"></path>
                                        <path
                                            d="M11,16 C13.7614237,16 16,13.7614237 16,11 C16,8.23857625 13.7614237,6 11,6 C8.23857625,6 6,8.23857625 6,11 C6,13.7614237 8.23857625,16 11,16 Z M11,18 C7.13400675,18 4,14.8659932 4,11 C4,7.13400675 7.13400675,4 11,4 C14.8659932,4 18,7.13400675 18,11 C18,14.8659932 14.8659932,18 11,18 Z"
                                            fill="#000000" fill-rule="nonzero"></path>
                                    </g>
                                </svg>
                            </span>

                            <input type="text" id="search_" class="form-control form-control-solid w-250px ps-15"
                                placeholder="Search">
                        </div>

                        <div class="container">
                            <div class="py-5 table-responsive">
                                <table id="kt_table_data"
                                    class="table table-striped table-rounded border border-gray-300 table-row-bordered table-row-gray-300">
                                    <thead class="text-center">
                                        <tr id="tThead" class="fw-bolder fs-6 text-gray-800">

                                        </tr>
                                    </thead>
                                    <tbody>
                                    </tbody>
                                </table>
                                <div id="data-kosong" class="fs-4 text-gray-400 text-center">Data masih kosong</div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
        <!--end::Container-->
    </div>
@endsection
@section('script')
    <script>
        let control = new Control();

        $(document).on('keyup', '#search_', function(e) {
            e.preventDefault();
            control.searchTable(this.value);
        })

        $(document).ready(function() {
            $(document).on('submit', '.form-data', function(e) {
                e.preventDefault(); // Menghentikan pengiriman form standar

                // Ambil nilai dari input field
                let informationGain = $('#informationgain').val();
                let crosValidation = $('#crosValidation').val();

                // Validasi input untuk memastikan tidak kosong
                if (informationGain === '') {
                    alert('Information Gain tidak boleh kosong.');
                    return;
                }

                // Validasi input untuk memastikan tidak kosong
                if (crosValidation === '') {
                    alert('Cross Validation tidak boleh kosong.');
                    return;
                }

                $('#data-kosong').empty();
                // Kosongkan elemen thead
                $('#tThead').empty();

                // Lakukan permintaan AJAX untuk mendapatkan label_head
                $.ajax({
                    url: `/prediksi/${informationGain}`,
                    method: 'GET',
                    success: function(res) {
                        if (res.data.length > 0) {
                            // Ambil nama kolom dari objek pertama (asumsi objek pertama memiliki semua kolom yang sama)
                            let label_head = Object.keys(res.data[0]);

                            // Buat elemen No dengan atribut class yang sesuai
                            let noColumn = '<th class="fw-bolder fs-6 text-gray-800">No</th>';

                            // Buat elemen thead
                            let html = noColumn;
                            label_head.forEach(key => {
                                html +=
                                    `<th class="fw-bolder fs-6 text-gray-800">${key}</th>`;
                            });

                            let tr = `<tr>${html}</tr>`;
                            // Tambahkan elemen thead yang baru
                            $('thead').html(tr);

                            // Setelah label_head diinisialisasi, buat kolom
                            let columns = [{
                                    data: null,
                                    render: function(data, type, row, meta) {
                                        return meta.row + 1;
                                    },
                                },
                                ...label_head.map((key) => {
                                    return {
                                        data: key,
                                    };
                                })
                            ];

                            // Panggil fungsi untuk menginisialisasi datatable
                            control.initDatatable(`/prediksi/${informationGain}`, columns);
                        }
                    },
                    error: function(error) {
                        console.error(error);
                    }
                });


                // Lakukan permintaan AJAX untuk memperoleh akurasi
                $.ajaxSetup({
                    headers: {
                        "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
                    },
                });

                $.ajax({
                    url: '/akurasi',
                    method: 'POST',
                    data: {
                        informationGain: informationGain,
                        crossValidation: crosValidation
                    },
                    success: function(response) {
                        console.log(response);
                        let data = parseFloat(response.data).toFixed(2);
                        $('#inputakurasi').val(data + ' %');
                    },
                    error: function(error) {
                        console.error(error);
                    }
                });

            });
        });
    </script>
@endsection
