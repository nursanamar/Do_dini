@php
    $path = explode('/', Request::path());
    // $role = auth()->user()->role;
@endphp
<div class="aside-menu aside-menu-custom flex-column-fluid">
    <!--begin::Aside Menu-->
    <div class="hover-scroll-overlay-y mb-5 mb-lg-5" id="kt_aside_menu_wrapper" data-kt-scroll="true"
        data-kt-scroll-activate="{default: false, lg: true}" data-kt-scroll-height="auto"
        data-kt-scroll-dependencies="#kt_aside_logo, #kt_aside_footer" data-kt-scroll-wrappers="#kt_aside_menu"
        data-kt-scroll-offset="0">
        <div class="menu-title" style="background-color: #452393; padding: 0 25px; color: #FFFFFF; font-size: 12px">Uji
            Data</div>
        <!--begin::Menu-->
        <div class="menu menu-column mt-2 menu-title-gray-800 menu-state-title-primary menu-state-icon-primary menu-state-bullet-primary menu-arrow-gray-500"
            id="#kt_aside_menu" data-kt-menu="true">

            <div class="menu-item">
                <a class="menu-link  {{ $path[0] == 'fileupload' ? 'active' : '' }}"
                    href="{{ route('fileupload.index') }}">
                    <span class="menu-icon">
                        <!--begin::Svg Icon | path: icons/duotune/general/gen025.svg-->
                        <span class="svg-icon svg-icon-2">
                            <img src="{{ $path[0] == 'fileupload' ? url('admin/assets/media/icons/aside/dashboardact.svg') : url('/admin/assets/media/icons/aside/dashboarddef.svg') }}"
                                alt="">
                        </span>
                        <!--end::Svg Icon-->
                    </span>
                    <span class="menu-title"
                        style="{{ $path[0] == 'fileupload' ? 'color: #F4BE2A' : 'color: #FFFFFF' }}">File
                        Upload</span>
                </a>
                <a class="menu-link  {{ $path[0] == 'hasil' ? 'active' : '' }}"
                    href="{{ route('hasil.index') }}">
                    <span class="menu-icon">
                        <!--begin::Svg Icon | path: icons/duotune/general/gen025.svg-->
                        <span class="svg-icon svg-icon-2">
                            <img src="{{ $path[0] == 'hasil' ? url('admin/assets/media/icons/aside/dashboardact.svg') : url('/admin/assets/media/icons/aside/dashboarddef.svg') }}"
                                alt="">
                        </span>
                        <!--end::Svg Icon-->
                    </span>
                    <span class="menu-title"
                        style="{{ $path[0] == 'hasil' ? 'color: #F4BE2A' : 'color: #FFFFFF' }}">Hasil</span>
                </a>
            </div>

        </div>
        <!--end::Menu-->
    </div>
</div>

@section('script')
    <script>
        $(document).ready(function() {
            // $(".menu-link").mousemove(function(){
            //     $(this).css("background", "#282EAD");
            //     }, function(){
            //     $(this).css("background", "none");
            // });
        });
    </script>
@endsection
