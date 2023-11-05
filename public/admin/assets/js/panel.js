function setCheckedRadioValue(radioName, value) {
    $('input[name="' + radioName + '"]').each(function () {
        if ($(this).val() == value) {
            $(this).prop('checked', true);
        } else {
            $(this).prop('checked', false);
        }
    });
}
class Control {
    constructor(type = null) {
        this.type = type;
        this.table = $("#kt_table_data");
        // this.formData = new FormData();
    }

    searchTable(data) {
        this.table.DataTable().search(data).draw();
    }

    overlay_form(type, module, url = null, role = null) {
        $(".title_side_form").html(`${type} ${module}`);
        $(".text-danger").html("");
        if (type == "Tambah") {
            $(".form-data")[0].reset();
            $("#from_select").val(null).trigger("change");
            $(".form-data").attr("data-type", "add");
        } else {
            $(".form-data").attr("data-type", "update");
            $.ajax({
                url: url,
                method: "GET",
                success: function (res) {
                    if (res.success == true) {
                        console.log(res.data);
                        $.each(res.data, function (x, y) {
                            if ($("input[name='" + x + "']").is(":radio")) {
                                $("input[name='" + x + "'][value='" + y + "']").prop("checked", true);
                            } else {
                                $("input[name='" + x + "']").val(y);
                                $("select[name='" + x + "']").val(y);
                                $("textarea[name='" + x + "']").val(y);
                                $("select[name='" + x + "']").trigger("change");
                            }
                        });
                    }
                },
                error: function (xhr) {
                    alert("gagal");
                },
            });
        }
        // this._offcanvasObject.show();
    }

    submitFormMultipart(url, role_data = null, module = null, method) {
        let this_ = this;
        let table_ = this.table;

        $.ajaxSetup({
            headers: {
                "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
            },
        });

        $.ajax({
            type: method,
            url: url,
            data: $(".form-data").serialize(),
            success: function (response) {
                console.log(response);
                $(".text-danger").html("");
                if (response.success == true) {
                    swal
                        .fire({
                            text: `${module} berhasil di ${role_data}`,
                            icon: "success",
                            showConfirmButton: false,
                            timer: 1500
                        })
                        .then(function () {
                            $("#side_form_close").trigger("click");
                            table_.DataTable().ajax.reload();
                            $("form")[0].reset();
                            $("#from_select").val(null).trigger("change");
                        });
                } else {
                    $("form")[0].reset();
                    $("#from_select").val(null).trigger("change");
                    Swal.fire("Gagal Memproses data!", `${response.message}`, "warning");
                }
            },
            error: function (xhr) {
                $(".text-danger").html("");
                $.each(xhr.responseJSON["errors"], function (key, value) {
                    $(`.${key}_error`).html(value);
                });
            },
        });
    }

    submitFormMultipartData(url, role_data = null, module = null, method) {
        let this_ = this;
        let table_ = this.table;

        $.ajaxSetup({
            headers: {
                "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
            },
        });

        let formData = new FormData($(".form-data")[0]); // Create FormData object from the form

        $.ajax({
            type: method,
            url: url,
            data: formData,
            contentType: false,
            processData: false,
            success: function (response) {
                console.log(response);
                $(".text-danger").html("");
                if (response.success) { // You can directly check the boolean value
                    $('#button-result').prop('disabled', false);
                    swal.fire({
                        text: `${module} berhasil di ${role_data}`,
                        icon: "success",
                        showConfirmButton: false,
                        timer: 1500,
                    }).then(function () {
                        $("#side_form_close").trigger("click");
                        table_.DataTable().ajax.reload();
                        $(".form-data")[0].reset(); // Use form class selector directly
                        $("#from_select").val(null).trigger("change");
                    });
                } else {
                    $(".form-data")[0].reset();
                    $("#from_select").val(null).trigger("change");
                    Swal.fire("Gagal Memproses data!", `${response.message}`, "warning");
                }
            },
            error: function (xhr) {
                $(".text-danger").html("");
                $.each(xhr.responseJSON.errors, function (key, value) {
                    $(`.${key}_error`).html(value);
                });
            },
        });
    }


    ajaxDelete(url, label) {
        let token = $("meta[name='csrf-token']").attr("content");
        let table_ = this.table;
        Swal.fire({
            title: `Apakah anda yakin akan menghapus data ${label} ?`,
            text: "Anda tidak akan dapat mengembalikan ini!",
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#3085d6",
            cancelButtonColor: "#d33",
            confirmButtonText: "Ya, hapus itu!",
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: url,
                    type: "DELETE",
                    data: {
                        id: $(this).attr("data-id"),
                        _token: token,
                    },
                    success: function () {
                        swal
                            .fire({
                                title: "Menghapus!",
                                text: "Data Anda telah dihapus.",
                                icon: "success",
                                showConfirmButton: false,
                                timer: 1500
                            })
                        table_.DataTable().ajax.reload();
                    },
                });
            }
        });
    }

    push_select(url, element) {
        $.ajax({
            url: url,
            method: "GET",
            success: function (res) {
                $(element).html("");
                let html = "<option selected disabled>Pilih</option>";
                $.each(res.data, function (x, y) {
                    html += `<option value="${y.id}">${y.value}</option>`;
                });
                $(element).html(html);
            },
            error: function (xhr) {
                alert("gagal");
            },
        });
    }

    push_select2(url, element) {
        $.ajax({
            url: url,
            method: "GET",
            success: function (res) {
                $(element).html("");
                let html = "<option selected disabled>Pilih</option>";
                $.each(res.data, function (x, y) {
                    html += `<option value="${y.id}">${y.text}</option>`;
                });
                $(element).html(html);
            },
            error: function (xhr) {
                alert("gagal");
            },
        });
    }

    push_select3(data, element) {
        $(element).html("");
        let html = "<option selected disabled>Pilih</option>";
        $.each(data, function (x, y) {
            html += `<option value="${y.text}">${y.text}</option>`;
        });
        $(element).html(html);
    }

    push_radio(data, radio, element) {
        $(element).empty();
        let html = '';
        $.each(data, function (x, y) {
            html += `<label class="form-label radio-button"><input type="radio" name="${radio}" value="${y.text}">${y.text}</label>`;
        });
        $(element).html(html);
    }

    async initDatatable(url, columns, columnDefs) {
        this.table.DataTable().clear().destroy();

        // await this.table.dataTable().clear().draw();
        await this.table.dataTable().fnClearTable();
        await this.table.dataTable().fnDraw();
        await this.table.dataTable().fnDestroy();
        this.table.DataTable({
            responsive: true,
            pageLength: 10,
            order: [[0, "asc"]],
            processing: true,
            // serverSide: true,
            ajax: url,
            columns: columns,
            // columnDefs: columnDefs,
            rowCallback: function (row, data, index) {
                var api = this.api();
                var startIndex = api.context[0]._iDisplayStart;
                var rowIndex = startIndex + index + 1;
                $('td', row).eq(0).html(rowIndex);
            },
        });
    }

    async initDatatable1(url, columns, columnDefs) {
        this.table.DataTable().clear().destroy();

        // await this.table.dataTable().clear().draw();
        await this.table.dataTable().fnClearTable();
        await this.table.dataTable().fnDraw();
        await this.table.dataTable().fnDestroy();
        this.table.DataTable({
            responsive: true,
            pageLength: 10,
            order: [[0, "asc"]],
            processing: true,
            // serverSide: true,
            ajax: url,
            columns: columns,
            // columnDefs: columnDefs,
            rowCallback: function (row, data, index) {
                var api = this.api();
                var startIndex = api.context[0]._iDisplayStart;
                var rowIndex = startIndex + index + 1;
                $('td', row).eq(0).html(rowIndex);
            },
        });
    }
}
