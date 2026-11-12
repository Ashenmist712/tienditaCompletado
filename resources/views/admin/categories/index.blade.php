@extends('layouts.admin')

@section('content')
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css" rel="stylesheet">
<link href="https://cdn.datatables.net/buttons/2.4.2/css/buttons.bootstrap5.min.css" rel="stylesheet">

<style>
    #tablaCategorias th,
    #tablaCategorias td {
        padding: 0.8rem;
    }

    .dt-buttons .btn {
        margin-right: 0.35rem;
    }
</style>

<div class="col-12 col-xl-11 mx-auto mt-3">
    <div class="card shadow-sm mb-4">
        <div class="card-header bg-white py-3">
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
                <h1 class="h5 mb-0 fw-bold">Gestión de Categorías</h1>
                <button
                    type="button"
                    class="btn btn-primary text-nowrap"
                    data-bs-toggle="modal"
                    data-bs-target="#modalCrearCategoria"
                >
                    <i class="fas fa-plus me-1" aria-hidden="true"></i> Nueva Categoría
                </button>
            </div>
        </div>

        <div class="card-body">
            <div class="table-responsive">
                <table id="tablaCategorias" class="table table-striped table-hover align-middle w-100">
                    <thead class="table-light">
                        <tr>
                            <th scope="col">#</th>
                            <th scope="col">Nombre</th>
                            <th scope="col">Descripción</th>
                            <th scope="col">Productos Asociados</th>
                            <th scope="col">Estado</th>
                            <th scope="col">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($categories as $category)
                            <tr>
                                <td>{{ $category->id }}</td>
                                <td class="fw-semibold">{{ $category->nombre }}</td>
                                <td>{{ $category->descripcion ?: 'Sin descripción' }}</td>
                                <td>{{ $category->products_count }}</td>
                                <td><span class="badge text-bg-success">Activa</span></td>
                                <td class="text-nowrap">
                                    <button
                                        type="button"
                                        class="btn btn-sm btn-outline-primary btn-editar"
                                        data-id="{{ $category->id }}"
                                        data-update-url="{{ route('admin.categories.update', ['categoria' => '__CATEGORY_ID__']) }}"
                                        data-name="{{ $category->nombre }}"
                                        data-description="{{ $category->descripcion }}"
                                        aria-label="Editar {{ $category->nombre }}"
                                        title="Editar"
                                    >
                                        <i class="fas fa-pen" aria-hidden="true"></i>
                                    </button>
                                    <form
                                        action="{{ route('admin.categories.destroy', $category) }}"
                                        method="POST"
                                        class="d-inline"
                                    >
                                        @csrf
                                        @method('DELETE')
                                        <button
                                            type="button"
                                            class="btn btn-sm btn-outline-danger btn-eliminar"
                                            aria-label="Eliminar {{ $category->nombre }}"
                                            title="Eliminar"
                                        >
                                            <i class="fas fa-trash" aria-hidden="true"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modalCrearCategoria" tabindex="-1" aria-labelledby="modalCrearCategoriaLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <form action="{{ route('admin.categories.store') }}" method="POST">
                @csrf

                <div class="modal-header">
                    <h2 class="modal-title fs-5" id="modalCrearCategoriaLabel">Nueva Categoría</h2>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                </div>

                <div class="modal-body">
                    <div class="mb-3">
                        <label for="crearNombreCategoria" class="form-label">Nombre</label>
                        <input
                            type="text"
                            class="form-control"
                            id="crearNombreCategoria"
                            name="nombre"
                            value="{{ old('nombre') }}"
                            maxlength="255"
                            required
                        >
                    </div>
                    <div class="mb-3">
                        <label for="crearDescripcionCategoria" class="form-label">Descripción</label>
                        <textarea
                            class="form-control"
                            id="crearDescripcionCategoria"
                            name="descripcion"
                            rows="4"
                        >{{ old('descripcion') }}</textarea>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Guardar Categoría</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="modalEditarCategoria" tabindex="-1" aria-labelledby="modalEditarCategoriaLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <form id="formEditarCategoria" method="POST">
                @csrf
                @method('PUT')

                <div class="modal-header">
                    <h2 class="modal-title fs-5" id="modalEditarCategoriaLabel">Editar Categoría</h2>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                </div>

                <div class="modal-body">
                    <div class="mb-3">
                        <label for="editarNombreCategoria" class="form-label">Nombre</label>
                        <input
                            type="text"
                            class="form-control"
                            id="editarNombreCategoria"
                            name="nombre"
                            maxlength="255"
                            required
                        >
                    </div>
                    <div class="mb-3">
                        <label for="editarDescripcionCategoria" class="form-label">Descripción</label>
                        <textarea
                            class="form-control"
                            id="editarDescripcionCategoria"
                            name="descripcion"
                            rows="4"
                        ></textarea>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Guardar Cambios</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.2/js/dataTables.buttons.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.bootstrap5.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.html5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.print.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    $(function () {
        $('#tablaCategorias').DataTable({
            dom: "<'row align-items-center mb-3'<'col-md-6'B><'col-md-6'f>>" +
                "<'row'<'col-12'tr>>" +
                "<'row align-items-center mt-3'<'col-md-5'i><'col-md-7'p>>",
            buttons: [
                { extend: 'copyHtml5', text: 'Copiar', className: 'btn btn-secondary btn-sm' },
                { extend: 'csvHtml5', text: 'CSV', className: 'btn btn-secondary btn-sm', exportOptions: { columns: [0, 1, 2, 3, 4] } },
                { extend: 'excelHtml5', text: 'Excel', className: 'btn btn-success btn-sm', exportOptions: { columns: [0, 1, 2, 3, 4] } },
                { extend: 'pdfHtml5', text: 'PDF', className: 'btn btn-danger btn-sm', exportOptions: { columns: [0, 1, 2, 3, 4] } },
                { extend: 'print', text: 'Imprimir', className: 'btn btn-info btn-sm', exportOptions: { columns: [0, 1, 2, 3, 4] } }
            ],
            order: [[0, 'asc']],
            pageLength: 10,
            language: {
                search: 'Buscar:',
                searchPlaceholder: 'Buscar categoría...',
                lengthMenu: 'Mostrar _MENU_ registros',
                info: 'Mostrando _START_ a _END_ de _TOTAL_ categorías',
                infoEmpty: 'Mostrando 0 a 0 de 0 categorías',
                infoFiltered: '(filtrado de _MAX_ categorías en total)',
                zeroRecords: 'No se encontraron categorías',
                emptyTable: 'No hay categorías registradas',
                loadingRecords: 'Cargando...',
                processing: 'Procesando...',
                paginate: {
                    first: 'Primero',
                    last: 'Último',
                    next: 'Siguiente',
                    previous: 'Anterior'
                },
                buttons: {
                    copyTitle: 'Copiado al portapapeles',
                    copySuccess: {
                        _: '%d filas copiadas',
                        1: '1 fila copiada'
                    }
                }
            }
        });

        $('#tablaCategorias').on('click', '.btn-editar', function () {
            const button = this;
            const form = document.getElementById('formEditarCategoria');
            const categoryId = button.dataset.id;

            form.action = button.dataset.updateUrl.replace('__CATEGORY_ID__', encodeURIComponent(categoryId));
            form.elements.namedItem('nombre').value = button.dataset.name;
            form.elements.namedItem('descripcion').value = button.dataset.description || '';

            bootstrap.Modal.getOrCreateInstance(document.getElementById('modalEditarCategoria')).show();
        });

        $(document).on('click', '.btn-eliminar', function () {
            const form = this.closest('form');

            Swal.fire({
                icon: 'warning',
                title: '¿Está seguro de eliminar esta categoría?',
                text: 'Esta acción no se puede deshacer',
                showCancelButton: true,
                confirmButtonText: 'Sí, eliminar',
                cancelButtonText: 'Cancelar',
                confirmButtonColor: '#dc3545',
                cancelButtonColor: '#6c757d'
            }).then((result) => {
                if (result.isConfirmed) {
                    form.submit();
                }
            });
        });

        @if($errors->any())
            Swal.fire({
                toast: true,
                position: 'top-end',
                icon: 'error',
                title: @json($errors->first(), JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT),
                showConfirmButton: false,
                timer: 5000,
                timerProgressBar: true
            });
        @elseif(session('success'))
            Swal.fire({
                toast: true,
                position: 'top-end',
                icon: 'success',
                title: @json(session('success'), JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT),
                showConfirmButton: false,
                timer: 3500,
                timerProgressBar: true
            });
        @endif
    });
</script>
@endsection
