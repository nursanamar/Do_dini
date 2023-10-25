@extends('layouts.layout')
@section('button')
    <div id="kt_toolbar_container" class="container-fluid d-flex flex-stack">
        <!--begin::Page title-->
        <div data-kt-swapper="true" data-kt-swapper-mode="prepend"
            data-kt-swapper-parent="{default: '#kt_content_container', 'lg': '#kt_toolbar_container'}"
            class="page-title d-flex align-items-center flex-wrap me-3 mb-5 mb-lg-0" style="gap: 10px">
            <!--begin::Title-->
            <button class="btn btn-primary btn-sm" data-kt-drawer-show="true" data-kt-drawer-target="#side_form"
                id="button-side-form"><i class="fa fa-plus-circle" style="color:#ffffff" aria-hidden="true"></i> Tambah
                Data</button>

            <form method="POST" action="{{ route('fileupload.delete') }}">
                @csrf
                @method('DELETE')
                <!-- Tombol atau elemen lainnya -->
                <button class="btn mr-2 btn-light btn-cancel btn-sm d-flex align-items-center"
                    style="background-color: #ea443e65; color: #EA443E"><i class="bi bi-trash-fill"
                        style="color: #EA443E"></i>Hapus Semua Data</button>
            </form>
            <!--end::Title-->
        </div>
        <!--end::Page title-->

    </div>
@endsection
@section('side-form')
    <div id="side_form" class="bg-white" data-kt-drawer="true" data-kt-drawer-activate="true"
        data-kt-drawer-toggle="#side_form_button" data-kt-drawer-close="#side_form_close" data-kt-drawer-width="500px">
        <!--begin::Card-->
        <div class="card w-100">
            <!--begin::Card header-->
            <div class="card-header pe-5">
                <!--begin::Title-->
                <div class="card-title">
                    <!--begin::User-->
                    <div class="d-flex justify-content-center flex-column me-3">
                        <a href="#"
                            class="fs-4 fw-bolder text-gray-900 text-hover-primary me-1 lh-1 title_side_form">Tambah Data
                            Mahasiswa</a>
                    </div>
                    <!--end::User-->
                </div>
                <!--end::Title-->
                <!--begin::Card toolbar-->
                <div class="card-toolbar">
                    <!--begin::Close-->
                    <div class="btn btn-sm btn-icon btn-active-light-primary" id="side_form_close">
                        <!--begin::Svg Icon | path: icons/duotone/Navigation/Close.svg-->
                        <span class="svg-icon svg-icon-2">
                            <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink"
                                width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
                                <g transform="translate(12.000000, 12.000000) rotate(-45.000000) translate(-12.000000, -12.000000) translate(4.000000, 4.000000)"
                                    fill="#000000">
                                    <rect fill="#000000" x="0" y="7" width="16" height="2" rx="1" />
                                    <rect fill="#000000" opacity="0.5"
                                        transform="translate(8.000000, 8.000000) rotate(-270.000000) translate(-8.000000, -8.000000)"
                                        x="0" y="7" width="16" height="2" rx="1" />
                                </g>
                            </svg>
                        </span>
                        <!--end::Svg Icon-->
                    </div>
                    <!--end::Close-->
                </div>
                <!--end::Card toolbar-->
            </div>
            <!--end::Card header-->
            <!--begin::Card body-->
            <div class="card-body hover-scroll-overlay-y">
                <form class="form-data" enctype="multipart/form-data">

                    <input type="hidden" name="id">
                    <input type="hidden" name="uuid">

                    <div class="mb-10">
                        <label for="file_excel" class="form-label">Tambahkan Data Mahsiswa</label>
                        <input class="form-control" accept=".xlsx, .csv" type="file" name="file_excel" id="file_excel">
                        <small class="text-danger file_excel_error"></small>
                    </div>

                    <div class="separator separator-dashed mt-8 mb-5"></div>
                    <div class="d-flex gap-5">
                        <button type="submit" class="btn btn-primary btn-sm btn-submit d-flex align-items-center"><i
                                class="bi bi-file-earmark-diff"></i> Simpan</button>
                        <button type="reset" id="side_form_close"
                            class="btn mr-2 btn-light btn-cancel btn-sm d-flex align-items-center"
                            style="background-color: #ea443e65; color: #EA443E"><i class="bi bi-trash-fill"
                                style="color: #EA443E"></i>Batal</button>
                    </div>
                </form>
            </div>
            <!--end::Card body-->
        </div>
        <!--end::Card-->
    </div>
@endsection
@section('content')
    <div class="post d-flex flex-column-fluid" id="kt_post">
        <!--begin::Container-->
        <div id="kt_content_container" class="container">
            <div class="row">

                <div class="card">
                    <div class="card-body p-0">

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
                                        <tr class="fw-bolder fs-6 text-gray-800">
                                            <th>No</th>
                                            <th>id masked</th>
                                            <th>kategori sekolah</th>
                                            <th>jenis kelamin</th>
                                            <th>jalur seleksi</th>
                                            <th>ips1</th>
                                            <th>sks1</th>
                                            <th>ips2</th>
                                            <th>sks2</th>
                                            <th>ips3</th>
                                            <th>sks3</th>
                                            <th>ips4</th>
                                            <th>sks4</th>
                                            <th>akidah akhlak</th>
                                            <th>algoritma pemrograman</th>
                                            <th>bahasa arab</th>
                                            <th>bahasa indonesia</th>
                                            <th>bahasa inggris</th>
                                            <th>basis data</th>
                                            <th>elektronika</th>
                                            <th>etika profesi</th>
                                            <th>fisika</th>
                                            <th>ilmu al quran</th>
                                            <th>ilmu fikih</th>
                                            <th>ilmu hadis</th>
                                            <th>interaksi manusia dan komputer</th>
                                            <th>jaringan komputer</th>
                                            <th>kecerdasan buatan</th>
                                            <th>kepemimpinan dan teamwork</th>
                                            <th>kewirausahaan</th>
                                            <th>logika informatika</th>
                                            <th>manajemen proyek teknologi informasi</th>
                                            <th>manajemen umum</th>
                                            <th>matematika diskrit</th>
                                            <th>matematika komputer</th>
                                            <th>matematika komputer dasar</th>
                                            <th>mikroprosesor</th>
                                            <th>organisasi dan arsitektur komputer</th>
                                            <th>pemrograman berorientasi objek</th>
                                            <th>pemrograman terstruktur</th>
                                            <th>pemrograman visual</th>
                                            <th>pemrograman web 2</th>
                                            <th>pend pancasila dan kewarganegaraan</th>
                                            <th>pengantar teknologi informasi</th>
                                            <th>probabilitas dan statistik</th>
                                            <th>rekayasa perangkat lunak</th>
                                            <th>sejarah peradaban islam</th>
                                            <th>sistem operasi komputer</th>
                                            <th>struktur data</th>
                                            <th>teknologi dan desain web</th>
                                            <th>teknologi informasi</th>
                                            <th>teknologi multimedia dan game</th>
                                            <th>teori bahasa dan automata</th>
                                            <th>class status</th>
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
        $(document).on('click', '#button-side-form', function() {
            control.overlay_form('Tambah', 'Data Mahasiswa');
        })

        $(document).on('submit', ".form-data", function(e) {
            e.preventDefault();
            let type = $(this).attr('data-type');
            if (type == 'add') {
                control.submitFormMultipartData('upload', 'Tambah', 'Data Mahasiswa',
                    'POST');
            } else {
                let uuid = $("input[name='uuid']").val();
                control.submitFormMultipartData('upload' + uuid, 'Update',
                    'Data Mahasiswa', 'POST');
            }
        });

        $(document).on('keyup', '#search_', function(e) {
            e.preventDefault();
            control.searchTable(this.value);
        })

        $(document).on('click', '#hapus-data', function(e) {
            e.preventDefault();
            if (confirm('Apakah Anda yakin ingin menghapus semua data?')) {
                let url = $(this).data('href');
                window.location.href = url;
            }
        })

        let columns = [{
                data: null,
                render: function(data, type, row, meta) {
                    console.log(row);
                    return meta.row + 1;
                }
            },
            {
                data: 'id_masked'
            },
            {
                data: 'kategori_sekolah'
            },
            {
                data: 'jenis_kelamin'
            },
            {
                data: 'jalur_seleksi'
            },
            {
                data: 'ips1'
            },
            {
                data: 'sks1'
            },
            {
                data: 'ips2'
            },
            {
                data: 'sks2'
            },
            {
                data: 'ips3'
            },
            {
                data: 'sks3'
            },
            {
                data: 'ips4'
            },
            {
                data: 'sks4'
            },
            {
                data: 'akidah_akhlak'
            },
            {
                data: 'algoritma_pemrograman'
            },
            {
                data: 'bahasa_arab'
            },
            {
                data: 'bahasa_indonesia'
            },
            {
                data: 'bahasa_inggris'
            },
            {
                data: 'basis_data'
            },
            {
                data: 'elektronika'
            },
            {
                data: 'etika_profesi'
            },
            {
                data: 'fisika'
            },
            {
                data: 'ilmu_al_quran'
            },
            {
                data: 'ilmu_fikih'
            },
            {
                data: 'ilmu_hadis'
            },
            {
                data: 'interaksi_manusia_dan_komputer'
            },
            {
                data: 'jaringan_komputer'
            },
            {
                data: 'kecerdasan_buatan'
            },
            {
                data: 'kepemimpinan_dan_teamwork'
            },
            {
                data: 'kewirausahaan'
            },
            {
                data: 'logika_informatika'
            },
            {
                data: 'manajemen_proyek_teknologi_informasi'
            },
            {
                data: 'manajemen_umum'
            },
            {
                data: 'matematika_diskrit'
            },
            {
                data: 'matematika_komputer'
            },
            {
                data: 'matematika_komputer_dasar'
            },
            {
                data: 'mikroprosesor'
            },
            {
                data: 'organisasi_dan_arsitektur_komputer'
            },
            {
                data: 'pemrograman_berorientasi_objek'
            },
            {
                data: 'pemrograman_terstruktur'
            },
            {
                data: 'pemrograman_visual'
            },
            {
                data: 'pemrograman_web_2'
            },
            {
                data: 'pend_pancasila_dan_kewarganegaraan'
            },
            {
                data: 'pengantar_teknologi_informasi'
            },
            {
                data: 'probabilitas_dan_statistik'
            },
            {
                data: 'rekayasa_perangkat_lunak'
            },
            {
                data: 'sejarah_peradaban_islam'
            },
            {
                data: 'sistem_operasi_komputer'
            },
            {
                data: 'struktur_data'
            },
            {
                data: 'teknologi_dan_desain_web'
            },
            {
                data: 'teknologi_informasi'
            },
            {
                data: 'teknologi_multimedia_dan_game'
            },
            {
                data: 'teori_bahasa_dan_automata'
            },
            {
                data: 'class_status'
            }
        ];

        $(function() {
            control.initDatatable('/get-all', columns);
        })
    </script>
@endsection
