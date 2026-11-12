@extends('layouts.admin')

@section('content')
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css" rel="stylesheet">
<link href="https://cdn.datatables.net/buttons/2.4.2/css/buttons.bootstrap5.min.css" rel="stylesheet">

<style>
    #tablaProductos th,
    #tablaProductos td {
        padding: 0.8rem;
    }

    .dt-buttons .btn {
        margin-right: 0.35rem;
    }

    .producto-thumbnail {
        width: 3rem;
        height: 3rem;
        object-fit: cover;
    }
</style>

<div class="col-12 col-xl-11 mx-auto mt-3">
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-header bg-white py-3">
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
                <h1 class="h5 mb-0 fw-bold">Gestión de Productos</h1>
                <button
                    type="button"
                    class="btn btn-primary text-nowrap"
                    data-bs-toggle="modal"
                    data-bs-target="#modalCrearProducto"
                >
                    <i class="fas fa-plus me-1" aria-hidden="true"></i> Nuevo Producto
                </button>
            </div>
        </div>

        <div class="card-body">
            <div class="table-responsive">
                <table id="tablaProductos" class="table table-striped table-hover align-middle w-100">
                    <thead class="table-light">
                        <tr>
                            <th scope="col">#</th>
                            <th scope="col">Imagen</th>
                            <th scope="col">Nombre</th>
                            <th scope="col">Categoría</th>
                            <th scope="col">Precio</th>
                            <th scope="col">Stock</th>
                            <th scope="col">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($products as $product)
                            <tr>
                                <td>{{ $product->id }}</td>
                                <td data-order="{{ $product->imagen ? 1 : 0 }}">
                                    @if($product->imagen)
                                        <img
                                            src="{{ asset('storage/' . $product->imagen) }}"
                                            alt="{{ $product->nombre }}"
                                            class="producto-thumbnail rounded shadow-sm"
                                        >
                                    @else
                                        <span class="producto-thumbnail rounded bg-light text-secondary d-inline-flex align-items-center justify-content-center">
                                            <i class="fas fa-image" aria-hidden="true"></i>
                                        </span>
                                    @endif
                                </td>
                                <td class="fw-semibold">{{ $product->nombre }}</td>
                                <td>
                                    <span class="badge text-bg-primary">
                                        {{ $product->category->nombre ?? 'Sin categoría' }}
                                    </span>
                                </td>
                                <td data-order="{{ $product->precio }}">${{ number_format($product->precio, 2) }}</td>
                                <td data-order="{{ $product->stock }}">
                                    @if($product->stock > 0)
                                        <span class="badge text-bg-success">{{ $product->stock }} unids.</span>
                                    @else
                                        <span class="badge text-bg-danger">Agotado</span>
                                    @endif
                                </td>
                                <td class="text-nowrap">
                                    <button
                                        type="button"
                                        class="btn btn-sm btn-outline-primary btn-editar"
                                        data-id="{{ $product->id }}"
                                        data-update-url="{{ route('admin.products.update', ['producto' => '__PRODUCT_ID__']) }}"
                                        data-name="{{ $product->nombre }}"
                                        data-category-id="{{ $product->category_id }}"
                                        data-price="{{ $product->precio }}"
                                        data-stock="{{ $product->stock }}"
                                        data-description="{{ $product->descripcion }}"
                                        data-image-url="{{ $product->imagen ? asset('storage/' . $product->imagen) : '' }}"
                                        aria-label="Editar {{ $product->nombre }}"
                                        title="Editar"
                                    >
                                        <i class="fas fa-pen" aria-hidden="true"></i>
                                    </button>
                                    <form
                                        action="{{ route('admin.products.destroy', $product) }}"
                                        method="POST"
                                        class="d-inline"
                                    >
                                        @csrf
                                        @method('DELETE')
                                        <button
                                            type="button"
                                            class="btn btn-sm btn-outline-danger btn-eliminar"
                                            aria-label="Eliminar {{ $product->nombre }}"
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

<div class="modal fade" id="modalCrearProducto" tabindex="-1" aria-labelledby="modalCrearProductoLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 shadow">
            <form action="{{ route('admin.products.store') }}" method="POST" enctype="multipart/form-data">
                @csrf

                <div class="modal-header">
                    <h2 class="modal-title fs-5" id="modalCrearProductoLabel">Nuevo Producto</h2>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                </div>

                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="crearNombreProducto" class="form-label">Nombre</label>
                            <input type="text" class="form-control" id="crearNombreProducto" name="nombre" value="{{ old('nombre') }}" maxlength="255" required>
                        </div>
                        <div class="col-md-6">
                            <label for="crearCategoriaProducto" class="form-label">Categoría</label>
                            <select class="form-select" id="crearCategoriaProducto" name="category_id" required>
                                <option value="">Selecciona una categoría</option>
                                @foreach($categories as $category)
                                    <option value="{{ $category->id }}" @selected(old('category_id') == $category->id)>{{ $category->nombre }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label for="crearPrecioProducto" class="form-label">Precio</label>
                            <input type="number" class="form-control" id="crearPrecioProducto" name="precio" value="{{ old('precio') }}" min="0" step="0.01" required>
                        </div>
                        <div class="col-md-6">
                            <label for="crearStockProducto" class="form-label">Stock</label>
                            <input type="number" class="form-control" id="crearStockProducto" name="stock" value="{{ old('stock', 1) }}" min="0" required>
                        </div>
                        <div class="col-12">
                            <label for="crearDescripcionProducto" class="form-label">Descripción</label>
                            <textarea class="form-control" id="crearDescripcionProducto" name="descripcion" rows="3">{{ old('descripcion') }}</textarea>
                        </div>
                        <div class="col-12">
                            <label for="crearImagenProducto" class="form-label">Imagen</label>
                            <input type="file" class="form-control" id="crearImagenProducto" name="imagen" accept="image/jpeg,image/png,image/jpg,image/webp">
                            <div class="form-text">JPG, PNG o WEBP. Tamaño máximo: 2 MB.</div>
                        </div>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Guardar Producto</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="modalEditarProducto" tabindex="-1" aria-labelledby="modalEditarProductoLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 shadow">
            <form
                id="formEditarProducto"
                method="POST"
                enctype="multipart/form-data"
                data-update-url="{{ route('admin.products.update', ['producto' => '__PRODUCT_ID__']) }}"
            >
                @csrf
                @method('PUT')
                <input type="hidden" name="producto_id" id="editarIdProducto">

                <div class="modal-header">
                    <h2 class="modal-title fs-5" id="modalEditarProductoLabel">Editar Producto</h2>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                </div>

                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="editarNombreProducto" class="form-label">Nombre</label>
                            <input type="text" class="form-control" id="editarNombreProducto" name="nombre" maxlength="255" required>
                        </div>
                        <div class="col-md-6">
                            <label for="editarCategoriaProducto" class="form-label">Categoría</label>
                            <select class="form-select" id="editarCategoriaProducto" name="category_id" required>
                                <option value="">Selecciona una categoría</option>
                                @foreach($categories as $category)
                                    <option value="{{ $category->id }}">{{ $category->nombre }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label for="editarPrecioProducto" class="form-label">Precio</label>
                            <input type="number" class="form-control" id="editarPrecioProducto" name="precio" min="0" step="0.01" required>
                        </div>
                        <div class="col-md-6">
                            <label for="editarStockProducto" class="form-label">Stock</label>
                            <input type="number" class="form-control" id="editarStockProducto" name="stock" min="0" required>
                        </div>
                        <div class="col-12">
                            <label for="editarDescripcionProducto" class="form-label">Descripción</label>
                            <textarea class="form-control" id="editarDescripcionProducto" name="descripcion" rows="3"></textarea>
                        </div>
                        <div class="col-12">
                            <label for="editarImagenProducto" class="form-label">Cambiar imagen (opcional)</label>
                            <input type="file" class="form-control" id="editarImagenProducto" name="imagen" accept="image/jpeg,image/png,image/jpg,image/webp">
                            <div class="form-text">JPG, PNG o WEBP. Tamaño máximo: 2 MB.</div>
                            <img id="editarVistaPreviaImagen" class="producto-thumbnail rounded mt-2 d-none" alt="Imagen actual del producto">
                        </div>
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
        $('#tablaProductos').DataTable({
            dom: "<'row align-items-center mb-3'<'col-md-6'B><'col-md-6'f>>" +
                "<'row'<'col-12'tr>>" +
                "<'row align-items-center mt-3'<'col-md-5'i><'col-md-7'p>>",
            buttons: [
                { extend: 'copyHtml5', text: 'Copiar', className: 'btn btn-secondary btn-sm', exportOptions: { columns: [0, 2, 3, 4, 5] } },
                { extend: 'csvHtml5', text: 'CSV', className: 'btn btn-secondary btn-sm', exportOptions: { columns: [0, 2, 3, 4, 5] } },
                { extend: 'excelHtml5', text: 'Excel', className: 'btn btn-success btn-sm', exportOptions: { columns: [0, 2, 3, 4, 5] } },
                { extend: 'pdfHtml5', text: 'PDF', className: 'btn btn-danger btn-sm', exportOptions: { columns: [0, 2, 3, 4, 5] } },
                { extend: 'print', text: 'Imprimir', className: 'btn btn-info btn-sm', exportOptions: { columns: [0, 2, 3, 4, 5] } }
            ],
            order: [[0, 'asc']],
            pageLength: 10,
            language: {
                search: 'Buscar:',
                searchPlaceholder: 'Buscar producto...',
                lengthMenu: 'Mostrar _MENU_ registros',
                info: 'Mostrando _START_ a _END_ de _TOTAL_ productos',
                infoEmpty: 'Mostrando 0 a 0 de 0 productos',
                infoFiltered: '(filtrado de _MAX_ productos en total)',
                zeroRecords: 'No se encontraron productos',
                emptyTable: 'No hay productos registrados',
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

        $('#tablaProductos').on('click', '.btn-editar', function () {
            const button = this;
            const form = document.getElementById('formEditarProducto');
            const productId = button.dataset.id;
            const preview = document.getElementById('editarVistaPreviaImagen');

            form.action = form.dataset.updateUrl.replace('__PRODUCT_ID__', encodeURIComponent(productId));
            form.elements.namedItem('producto_id').value = productId;
            form.elements.namedItem('nombre').value = button.dataset.name;
            form.elements.namedItem('category_id').value = button.dataset.categoryId;
            form.elements.namedItem('precio').value = button.dataset.price;
            form.elements.namedItem('stock').value = button.dataset.stock;
            form.elements.namedItem('descripcion').value = button.dataset.description || '';
            form.elements.namedItem('imagen').value = '';

            if (button.dataset.imageUrl) {
                preview.src = button.dataset.imageUrl;
                preview.classList.remove('d-none');
            } else {
                preview.removeAttribute('src');
                preview.classList.add('d-none');
            }

            bootstrap.Modal.getOrCreateInstance(document.getElementById('modalEditarProducto')).show();
        });

        $(document).on('click', '.btn-eliminar', function () {
            const form = this.closest('form');

            Swal.fire({
                icon: 'warning',
                title: '¿Está seguro de eliminar este producto?',
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

            @if(old('producto_id'))
                const failedEditForm = document.getElementById('formEditarProducto');
                const failedProductId = @json(old('producto_id'));
                failedEditForm.action = failedEditForm.dataset.updateUrl.replace('__PRODUCT_ID__', encodeURIComponent(failedProductId));
                failedEditForm.elements.namedItem('producto_id').value = failedProductId;
                failedEditForm.elements.namedItem('nombre').value = @json(old('nombre'));
                failedEditForm.elements.namedItem('category_id').value = @json(old('category_id'));
                failedEditForm.elements.namedItem('precio').value = @json(old('precio'));
                failedEditForm.elements.namedItem('stock').value = @json(old('stock'));
                failedEditForm.elements.namedItem('descripcion').value = @json(old('descripcion'));
                bootstrap.Modal.getOrCreateInstance(document.getElementById('modalEditarProducto')).show();
            @else
                bootstrap.Modal.getOrCreateInstance(document.getElementById('modalCrearProducto')).show();
            @endif
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
