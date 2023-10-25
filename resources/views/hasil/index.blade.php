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
                                    <label for="inputcrossvalidation" class="col-sm-2 col-form-label">Information
                                        Gain</label>
                                    <div class="col-sm-2">
                                        <input type="number" class="form-control" id="inputcrossvalidation">
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
                            <div class="py-5">
                                <table id="kt_table_data" class="table table-row-dashed table-row-gray-300 gy-7">
                                    <thead class="text-center">
                                        <tr class="fw-bolder fs-6 text-gray-800">
                                            <th>No</th>
                                            <th>NIM</th>
                                            <th>Nama</th>
                                            <th>Total Semester</th>
                                            <th>Total IPK</th>
                                            <th>Ket</th>
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
                e.preventDefault(); // Prevent the default form submission

                // Get the value from the input field
                let crossValidationValue = $('#inputcrossvalidation').val();

                // Validasi apakah input tidak kosong
                if (crossValidationValue === '') {
                    alert('The cross validation field is required.');
                    return;
                }

                let columns = [{
                        data: null,
                        render: function(data, type, row, meta) {
                            return meta.row + 1;
                        }
                    },
                    {
                        data: 'nim'
                    },
                    {
                        data: 'nama'
                    },
                    {
                        data: null,
                        render: function(data, type, row, meta) {
                            return row.semester + ' Semester';
                        }
                    },
                    {
                        data: 'ipk',
                        render: function(data, type, row, meta) {
                            // Format IPK to 2 decimal places
                            if (type === 'display') {
                                return parseFloat(data).toFixed(2);
                            }
                            return data;
                        }
                    },
                    {
                        data: 'keterangan'
                    },
                ];

                control.initDatatable(`/prediksi/${crossValidationValue}`, columns);

                // // Perform AJAX request for predictDOStatus
                // $.ajax({
                //     url: `/akurasi/${crossValidationValue}`, // Sesuaikan URL sesuai kebutuhan Anda
                //     method: 'GET', // Tentukan metode HTTP
                //     success: function(response) {
                //         // Response dari fungsi predictDOStatus dapat diolah di sini
                //         let data = parseFloat(response.data).toFixed(2)
                //         // Panggil fungsi untuk mengambil akurasi setelah mendapatkan respons dari predictDOStatus
                //         $('#inputakurasi').val(data + ' %')
                //     },
                //     error: function(error) {
                //         console.error(error);
                //     }
                // });

            });

        });
    </script>
@endsection
