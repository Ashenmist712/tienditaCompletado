@extends('layouts.admin')

@section('content')
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css" rel="stylesheet">
<link href="https://cdn.datatables.net/buttons/2.4.2/css/buttons.bootstrap5.min.css" rel="stylesheet">

<style>
    #tablaUsuarios th,
    #tablaUsuarios td {
        padding: 0.8rem;
    }

    .dt-buttons .btn {
        margin-right: 0.35rem;
    }

    .user-summary-card {
        min-height: 7rem;
    }

    .user-summary-icon {
        width: 2.75rem;
        height: 2.75rem;
    }

    #tablaUsuarios {
        font-size: 0.925rem;
    }

    @media (max-width: 767.98px) {
        .dt-buttons {
            display: flex;
            flex-wrap: wrap;
            gap: 0.35rem;
        }

        .dt-buttons .btn {
            margin-right: 0;
        }
    }
</style>

<div class="col-12 col-xl-11 mx-auto mt-3">
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-end gap-3 mb-4">
        <div>
            <h1 class="h3 fw-bold mb-1">Gestión de Usuarios</h1>
            <p class="text-muted mb-0">Administra cuentas, permisos y actividad de compra.</p>
        </div>
        <button
            type="button"
            class="btn btn-primary text-nowrap"
            data-bs-toggle="modal"
            data-bs-target="#modalCrearUsuario"
        >
            <i class="fas fa-plus me-1" aria-hidden="true"></i> Nuevo Usuario
        </button>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm user-summary-card">
                <div class="card-body d-flex align-items-center gap-3">
                    <span class="user-summary-icon rounded-circle bg-primary-subtle text-primary d-inline-flex align-items-center justify-content-center">
                        <i class="fas fa-users" aria-hidden="true"></i>
                    </span>
                    <div>
                        <div class="small text-muted">Total de usuarios</div>
                        <div class="h4 fw-bold mb-0">{{ $summary['total'] }}</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm user-summary-card">
                <div class="card-body d-flex align-items-center gap-3">
                    <span class="user-summary-icon rounded-circle bg-danger-subtle text-danger d-inline-flex align-items-center justify-content-center">
                        <i class="fas fa-user-shield" aria-hidden="true"></i>
                    </span>
                    <div>
                        <div class="small text-muted">Administradores</div>
                        <div class="h4 fw-bold mb-0">{{ $summary['admins'] }}</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm user-summary-card">
                <div class="card-body d-flex align-items-center gap-3">
                    <span class="user-summary-icon rounded-circle bg-success-subtle text-success d-inline-flex align-items-center justify-content-center">
                        <i class="fas fa-envelope-circle-check" aria-hidden="true"></i>
                    </span>
                    <div>
                        <div class="small text-muted">Correos verificados</div>
                        <div class="h4 fw-bold mb-0">{{ $summary['verified'] }}</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm user-summary-card">
                <div class="card-body d-flex align-items-center gap-3">
                    <span class="user-summary-icon rounded-circle bg-info-subtle text-info-emphasis d-inline-flex align-items-center justify-content-center">
                        <i class="fas fa-bag-shopping" aria-hidden="true"></i>
                    </span>
                    <div>
                        <div class="small text-muted">Con pedidos</div>
                        <div class="h4 fw-bold mb-0">{{ $summary['with_orders'] }}</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="card border-0 shadow-sm mb-4">
        <div class="card-header bg-white py-3">
            <h2 class="h5 mb-0 fw-bold">Listado de usuarios</h2>
        </div>
        <div class="card-body">
            <div class="row g-3 mb-3">
                <div class="col-sm-6 col-lg-4">
                    <label for="filtroRolUsuario" class="form-label">Rol</label>
                    <select id="filtroRolUsuario" class="form-select">
                        <option value="">Todos</option>
                        <option value="Administrador">Administrador</option>
                        <option value="Cliente">Cliente</option>
                    </select>
                </div>
                <div class="col-sm-6 col-lg-4">
                    <label for="filtroVerificacionUsuario" class="form-label">Correo</label>
                    <select id="filtroVerificacionUsuario" class="form-select">
                        <option value="">Todos</option>
                        <option value="Verificado">Verificado</option>
                        <option value="Pendiente">Pendiente</option>
                    </select>
                </div>
                <div class="col-12 col-lg-4 d-flex align-items-end justify-content-lg-end">
                    <button type="button" class="btn btn-sm btn-outline-secondary" id="limpiarFiltrosUsuarios">
                        <i class="fas fa-filter-circle-xmark me-1" aria-hidden="true"></i> Limpiar filtros
                    </button>
                </div>
            </div>

            <div class="table-responsive">
                <table id="tablaUsuarios" class="table table-striped table-hover align-middle w-100">
                    <thead class="table-light">
                        <tr>
                            <th scope="col">Usuario</th>
                            <th scope="col">Rol</th>
                            <th scope="col">Correo</th>
                            <th scope="col">Registro</th>
                            <th scope="col">Pedidos</th>
                            <th scope="col">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($users as $user)
                            @php
                                $orderData = $user->orders->map(fn ($order) => [
                                    'id' => $order->id,
                                    'date' => $order->created_at?->format('d/m/Y H:i') ?? '—',
                                    'status' => $order->estado,
                                    'total' => $order->total,
                                ]);
                            @endphp
                            <tr>
                                <td>
                                    <div class="fw-semibold">{{ $user->name }}</div>
                                    <div class="small text-muted">ID #{{ $user->id }}</div>
                                </td>
                                <td data-search="{{ $user->is_admin ? 'Administrador' : 'Cliente' }}">
                                    @if($user->is_admin)
                                        <span class="badge text-bg-primary">Administrador</span>
                                    @else
                                        <span class="badge text-bg-light border text-dark">Cliente</span>
                                    @endif
                                </td>
                                <td data-search="{{ $user->email_verified_at ? 'Verificado' : 'Pendiente' }}">
                                    <div>{{ $user->email }}</div>
                                    @if($user->email_verified_at)
                                        <span class="small text-success"><i class="fas fa-circle-check me-1" aria-hidden="true"></i>Verificado</span>
                                    @else
                                        <span class="small text-warning-emphasis"><i class="fas fa-clock me-1" aria-hidden="true"></i>Pendiente</span>
                                    @endif
                                </td>
                                <td data-order="{{ $user->created_at?->toIso8601String() }}">
                                    {{ $user->created_at?->format('d/m/Y') ?? '—' }}
                                </td>
                                <td data-order="{{ $user->orders_count }}">{{ $user->orders_count }}</td>
                                <td class="text-nowrap">
                                    <button
                                        type="button"
                                        class="btn btn-sm btn-outline-secondary btn-detalle-usuario"
                                        data-user-id="{{ $user->id }}"
                                        data-user-name="{{ $user->name }}"
                                        data-user-email="{{ $user->email }}"
                                        data-user-admin="{{ $user->is_admin ? '1' : '0' }}"
                                        data-user-verified="{{ $user->email_verified_at?->format('d/m/Y H:i') ?? '' }}"
                                        data-user-created="{{ $user->created_at?->format('d/m/Y H:i') ?? '—' }}"
                                        data-user-orders-count="{{ $user->orders_count }}"
                                        data-user-orders="{{ $orderData->toJson() }}"
                                        aria-label="Ver detalle de {{ $user->name }}"
                                        title="Ver detalle"
                                    >
                                        <i class="fas fa-eye" aria-hidden="true"></i>
                                    </button>
                                    <button
                                        type="button"
                                        class="btn btn-sm btn-outline-primary btn-editar-usuario"
                                        data-user-id="{{ $user->id }}"
                                        data-user-name="{{ $user->name }}"
                                        data-user-email="{{ $user->email }}"
                                        data-user-admin="{{ $user->is_admin ? '1' : '0' }}"
                                        data-user-self="{{ auth()->id() === $user->id ? '1' : '0' }}"
                                        data-user-orders-count="{{ $user->orders_count }}"
                                        aria-label="Editar {{ $user->name }}"
                                        title="Editar"
                                    >
                                        <i class="fas fa-pen" aria-hidden="true"></i>
                                    </button>
                                    @if(auth()->id() !== $user->id)
                                        <form action="{{ route('admin.users.destroy', $user) }}" method="POST" class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button
                                                type="button"
                                                class="btn btn-sm btn-outline-danger btn-eliminar-usuario"
                                                data-user-name="{{ $user->name }}"
                                                data-user-admin="{{ $user->is_admin ? '1' : '0' }}"
                                                data-user-orders-count="{{ $user->orders_count }}"
                                                aria-label="Eliminar {{ $user->name }}"
                                                title="Eliminar"
                                            >
                                                <i class="fas fa-trash" aria-hidden="true"></i>
                                            </button>
                                        </form>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modalCrearUsuario" tabindex="-1" aria-labelledby="modalCrearUsuarioLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <form action="{{ route('admin.users.store') }}" method="POST" id="formCrearUsuario">
                @csrf
                <input type="hidden" name="form_type" value="create">
                <div class="modal-header">
                    <h2 class="modal-title fs-5" id="modalCrearUsuarioLabel">Nuevo Usuario</h2>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                </div>
                <div class="modal-body">
                    <div class="alert alert-info py-2">
                        Al crear la cuenta, se solicitará un correo con un enlace para verificar su dirección.
                    </div>
                    <div class="mb-3">
                        <label for="crearNombreUsuario" class="form-label">Nombre</label>
                        <input type="text" class="form-control" id="crearNombreUsuario" name="name" value="{{ old('form_type') === 'create' ? old('name') : '' }}" maxlength="255" required>
                    </div>
                    <div class="mb-3">
                        <label for="crearEmailUsuario" class="form-label">Correo electrónico</label>
                        <input type="email" class="form-control" id="crearEmailUsuario" name="email" value="{{ old('form_type') === 'create' ? old('email') : '' }}" maxlength="255" required>
                    </div>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="crearPasswordUsuario" class="form-label">Contraseña</label>
                            <input type="password" class="form-control" id="crearPasswordUsuario" name="password" minlength="8" autocomplete="new-password" required>
                        </div>
                        <div class="col-md-6">
                            <label for="crearPasswordConfirmacion" class="form-label">Confirmar contraseña</label>
                            <input type="password" class="form-control" id="crearPasswordConfirmacion" name="password_confirmation" minlength="8" autocomplete="new-password" required>
                        </div>
                    </div>
                    <div class="form-check mt-3">
                        <input class="form-check-input" type="checkbox" id="crearAdminUsuario" name="is_admin" value="1" @checked(old('form_type') === 'create' && old('is_admin'))>
                        <label class="form-check-label" for="crearAdminUsuario">Dar permisos de administrador</label>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Crear Usuario</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="modalEditarUsuario" tabindex="-1" aria-labelledby="modalEditarUsuarioLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <form id="formEditarUsuario" method="POST" data-update-url="{{ route('admin.users.update', ['usuario' => '__USER_ID__']) }}">
                @csrf
                @method('PUT')
                <input type="hidden" name="form_type" value="edit">
                <input type="hidden" name="user_id" id="editarIdUsuario" value="{{ old('form_type') === 'edit' ? old('user_id') : '' }}">
                <div class="modal-header">
                    <h2 class="modal-title fs-5" id="modalEditarUsuarioLabel">Editar Usuario</h2>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="editarNombreUsuario" class="form-label">Nombre</label>
                        <input type="text" class="form-control" id="editarNombreUsuario" name="name" value="{{ old('form_type') === 'edit' ? old('name') : '' }}" maxlength="255" required>
                    </div>
                    <div class="mb-3">
                        <label for="editarEmailUsuario" class="form-label">Correo electrónico</label>
                        <input type="email" class="form-control" id="editarEmailUsuario" name="email" value="{{ old('form_type') === 'edit' ? old('email') : '' }}" maxlength="255" required>
                    </div>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="editarPasswordUsuario" class="form-label">Nueva contraseña</label>
                            <input type="password" class="form-control" id="editarPasswordUsuario" name="password" minlength="8" autocomplete="new-password">
                        </div>
                        <div class="col-md-6">
                            <label for="editarPasswordConfirmacion" class="form-label">Confirmar contraseña</label>
                            <input type="password" class="form-control" id="editarPasswordConfirmacion" name="password_confirmation" minlength="8" autocomplete="new-password">
                        </div>
                    </div>
                    <div class="form-text">Deja ambos campos vacíos para conservar la contraseña actual.</div>
                    <div class="alert alert-info py-2 mt-3 mb-0">
                        Si cambias el correo, se invalidará la verificación anterior y se solicitará un nuevo enlace para la dirección actualizada.
                    </div>
                    <div class="form-check mt-3">
                        <input type="hidden" name="is_admin" id="editarAdminProtegido" value="1" disabled>
                        <input class="form-check-input" type="checkbox" id="editarAdminUsuario" name="is_admin" value="1" @checked(old('form_type') === 'edit' && old('is_admin'))>
                        <label class="form-check-label" for="editarAdminUsuario">Administrador</label>
                    </div>
                    <div id="avisoAutoadministracion" class="alert alert-info py-2 mt-3 d-none mb-0">
                        No es posible quitarte tu propio acceso de administrador.
                    </div>
                    <div id="avisoUltimoAdministrador" class="alert alert-warning py-2 mt-3 d-none mb-0">
                        No es posible eliminar o degradar al último administrador.
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

<div class="modal fade" id="modalDetalleUsuario" tabindex="-1" aria-labelledby="modalDetalleUsuarioLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 shadow">
            <div class="modal-header">
                <h2 class="modal-title fs-5" id="modalDetalleUsuarioLabel">Detalle de Usuario</h2>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>
            <div class="modal-body">
                <div class="row g-3 mb-4">
                    <div class="col-md-6">
                        <h3 class="h6">Cuenta</h3>
                        <div id="detalleNombreUsuario" class="fw-semibold"></div>
                        <div id="detalleEmailUsuario" class="text-muted"></div>
                    </div>
                    <div class="col-md-6">
                        <h3 class="h6">Acceso y registro</h3>
                        <div>Rol: <span id="detalleRolUsuario" class="badge"></span></div>
                        <div id="detalleVerificacionUsuario" class="small text-muted mt-1"></div>
                        <div id="detalleRegistroUsuario" class="small text-muted"></div>
                    </div>
                </div>
                <h3 class="h6">Pedidos recientes <span id="detalleCantidadPedidos" class="text-muted fw-normal"></span></h3>
                <div class="table-responsive">
                    <table class="table table-sm">
                        <thead>
                            <tr>
                                <th>Pedido</th>
                                <th>Fecha</th>
                                <th>Estado</th>
                                <th class="text-end">Total</th>
                            </tr>
                        </thead>
                        <tbody id="detallePedidosUsuario"></tbody>
                    </table>
                </div>
                <div id="sinPedidosUsuario" class="text-muted small d-none">Este usuario aún no tiene pedidos.</div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cerrar</button>
            </div>
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
        const table = $('#tablaUsuarios').DataTable({
            dom: "<'row align-items-center mb-3'<'col-md-6'B><'col-md-6'f>>" +
                "<'row'<'col-12'tr>>" +
                "<'row align-items-center mt-3'<'col-md-5'i><'col-md-7'p>>",
            buttons: [
                { extend: 'copyHtml5', text: 'Copiar', className: 'btn btn-secondary btn-sm', exportOptions: { columns: [0, 1, 2, 3, 4] } },
                { extend: 'csvHtml5', text: 'CSV', className: 'btn btn-secondary btn-sm', exportOptions: { columns: [0, 1, 2, 3, 4] } },
                { extend: 'excelHtml5', text: 'Excel', className: 'btn btn-success btn-sm', exportOptions: { columns: [0, 1, 2, 3, 4] } },
                { extend: 'pdfHtml5', text: 'PDF', className: 'btn btn-danger btn-sm', exportOptions: { columns: [0, 1, 2, 3, 4] } },
                { extend: 'print', text: 'Imprimir', className: 'btn btn-info btn-sm', exportOptions: { columns: [0, 1, 2, 3, 4] } }
            ],
            order: [[3, 'desc']],
            pageLength: 10,
            language: {
                search: 'Buscar:',
                searchPlaceholder: 'Buscar por nombre o correo...',
                lengthMenu: 'Mostrar _MENU_ registros',
                info: 'Mostrando _START_ a _END_ de _TOTAL_ usuarios',
                infoEmpty: 'Mostrando 0 a 0 de 0 usuarios',
                infoFiltered: '(filtrado de _MAX_ usuarios en total)',
                zeroRecords: 'No hay usuarios que coincidan con los filtros seleccionados.',
                emptyTable: '<div class="py-4 text-center"><i class="fas fa-users fa-2x text-muted mb-3" aria-hidden="true"></i><div class="fw-semibold">Aún no hay usuarios</div><div class="text-muted">Crea una cuenta para que aparezca en este listado.</div></div>',
                loadingRecords: 'Cargando...',
                processing: 'Procesando...',
                paginate: {
                    first: 'Primero',
                    last: 'Último',
                    next: 'Siguiente',
                    previous: 'Anterior'
                }
            }
        });

        $('#filtroRolUsuario').on('change', function () {
            table.column(1).search(this.value).draw();
        });

        $('#filtroVerificacionUsuario').on('change', function () {
            table.column(2).search(this.value).draw();
        });

        $('#limpiarFiltrosUsuarios').on('click', function () {
            $('#filtroRolUsuario, #filtroVerificacionUsuario').val('');
            table.search('').columns().search('').draw();
        });

        $('#tablaUsuarios').on('click', '.btn-editar-usuario', function () {
            const button = this;
            const form = document.getElementById('formEditarUsuario');
            const isSelf = button.dataset.userSelf === '1';
            const isAdmin = button.dataset.userAdmin === '1';
            const hasOtherAdmins = Number(@json($summary['admins'])) > 1 || ! isAdmin;

            form.action = form.dataset.updateUrl.replace('__USER_ID__', encodeURIComponent(button.dataset.userId));
            form.elements.namedItem('user_id').value = button.dataset.userId;
            form.elements.namedItem('name').value = button.dataset.userName;
            form.elements.namedItem('email').value = button.dataset.userEmail;
            form.elements.namedItem('password').value = '';
            form.elements.namedItem('password_confirmation').value = '';
            form.elements.namedItem('is_admin').checked = isAdmin;

            const adminCheckbox = form.elements.namedItem('is_admin');
            const protectedAdminInput = document.getElementById('editarAdminProtegido');
            adminCheckbox.disabled = isSelf && isAdmin;
            protectedAdminInput.disabled = ! (isSelf && isAdmin);
            document.getElementById('avisoAutoadministracion').classList.toggle('d-none', ! (isSelf && isAdmin));
            document.getElementById('avisoUltimoAdministrador').classList.toggle('d-none', ! (isAdmin && ! hasOtherAdmins && ! isSelf));

            bootstrap.Modal.getOrCreateInstance(document.getElementById('modalEditarUsuario')).show();
        });

        $('#tablaUsuarios').on('click', '.btn-detalle-usuario', function () {
            const button = this;
            const setText = (id, value) => {
                document.getElementById(id).textContent = value;
            };

            setText('detalleNombreUsuario', button.dataset.userName);
            setText('detalleEmailUsuario', button.dataset.userEmail);
            setText('detalleRegistroUsuario', `Registro: ${button.dataset.userCreated}`);
            setText('detalleCantidadPedidos', `(${button.dataset.userOrdersCount} en total)`);
            setText(
                'detalleVerificacionUsuario',
                button.dataset.userVerified ? `Correo verificado: ${button.dataset.userVerified}` : 'Correo pendiente de verificación'
            );

            const role = document.getElementById('detalleRolUsuario');
            role.textContent = button.dataset.userAdmin === '1' ? 'Administrador' : 'Cliente';
            role.className = `badge ${button.dataset.userAdmin === '1' ? 'text-bg-primary' : 'text-bg-light border text-dark'}`;

            const body = document.getElementById('detallePedidosUsuario');
            const orders = JSON.parse(button.dataset.userOrders);
            body.replaceChildren();
            document.getElementById('sinPedidosUsuario').classList.toggle('d-none', orders.length > 0);

            orders.forEach((order) => {
                const row = document.createElement('tr');
                const statusLabels = {
                    pendiente: 'Pendiente',
                    procesando: 'Procesando',
                    completado: 'Completado',
                    cancelado: 'Cancelado'
                };
                [
                    `#${order.id}`,
                    order.date,
                    statusLabels[order.status] || order.status,
                    `$${Number(order.total).toFixed(2)}`
                ].forEach((value, index) => {
                    const cell = document.createElement('td');
                    cell.textContent = value;
                    if (index === 3) {
                        cell.className = 'text-end';
                    }
                    row.appendChild(cell);
                });
                body.appendChild(row);
            });

            bootstrap.Modal.getOrCreateInstance(document.getElementById('modalDetalleUsuario')).show();
        });

        $(document).on('click', '.btn-eliminar-usuario', function () {
            const form = this.closest('form');
            const userName = this.dataset.userName;
            const isAdmin = this.dataset.userAdmin === '1';
            const hasOtherAdmins = Number(@json($summary['admins'])) > 1;
            const hasOrders = Number(this.dataset.userOrdersCount) > 0;

            Swal.fire({
                icon: 'warning',
                title: `¿Eliminar a ${userName}?`,
                text: hasOrders
                    ? 'La cuenta se eliminará, pero sus pedidos conservarán su historial y dejarán de estar asociados a este usuario.'
                    : isAdmin && ! hasOtherAdmins
                        ? 'No se puede eliminar al último administrador.'
                        : 'Esta acción no se puede deshacer.',
                showCancelButton: true,
                confirmButtonText: 'Sí, eliminar',
                cancelButtonText: 'Cancelar',
                confirmButtonColor: '#dc3545',
                cancelButtonColor: '#6c757d',
                preConfirm: () => {
                    if (isAdmin && ! hasOtherAdmins) {
                        Swal.showValidationMessage('No se puede eliminar al último administrador.');
                        return false;
                    }
                }
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

            @if(old('form_type') === 'edit' && old('user_id'))
                const failedEditForm = document.getElementById('formEditarUsuario');
                const failedUserId = @json(old('user_id'));
                failedEditForm.action = failedEditForm.dataset.updateUrl.replace('__USER_ID__', encodeURIComponent(failedUserId));
                failedEditForm.elements.namedItem('user_id').value = failedUserId;
                failedEditForm.elements.namedItem('name').value = @json(old('name'));
                failedEditForm.elements.namedItem('email').value = @json(old('email'));
                const failedEditIsSelf = Number(failedUserId) === Number(@json(auth()->id()));
                const failedEditIsAdmin = @json((bool) old('is_admin'));
                failedEditForm.elements.namedItem('is_admin').checked = failedEditIsAdmin;
                failedEditForm.elements.namedItem('is_admin').disabled = failedEditIsSelf && failedEditIsAdmin;
                document.getElementById('editarAdminProtegido').disabled = ! (failedEditIsSelf && failedEditIsAdmin);
                bootstrap.Modal.getOrCreateInstance(document.getElementById('modalEditarUsuario')).show();
            @else
                bootstrap.Modal.getOrCreateInstance(document.getElementById('modalCrearUsuario')).show();
            @endif
        @elseif(session('error'))
            Swal.fire({
                icon: 'error',
                title: 'No se pudo completar la acción',
                text: @json(session('error'), JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT),
                confirmButtonColor: '#0d6efd'
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
