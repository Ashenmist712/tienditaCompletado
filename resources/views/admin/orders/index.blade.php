@extends('layouts.admin')

@section('content')
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css" rel="stylesheet">
<link href="https://cdn.datatables.net/buttons/2.4.2/css/buttons.bootstrap5.min.css" rel="stylesheet">

<style>
    #tablaPedidos th,
    #tablaPedidos td {
        padding: 0.8rem;
    }

    .dt-buttons .btn {
        margin-right: 0.35rem;
    }

    .order-summary-card {
        min-height: 7rem;
    }

    .order-summary-icon {
        width: 2.75rem;
        height: 2.75rem;
    }

    #tablaPedidos {
        font-size: 0.925rem;
    }

    @media (max-width: 767.98px) {
        .orders-card-body {
            padding: 1rem;
        }

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
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-end gap-2 mb-4">
        <div>
            <h1 class="h3 fw-bold mb-1">Gestión de Pedidos</h1>
            <p class="text-muted mb-0">Consulta compras, pagos y actualiza el seguimiento de cada pedido.</p>
        </div>
        <span class="badge rounded-pill text-bg-light border fs-6">{{ $summary['total'] }} pedidos</span>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm order-summary-card">
                <div class="card-body d-flex align-items-center gap-3">
                    <span class="order-summary-icon rounded-circle bg-primary-subtle text-primary d-inline-flex align-items-center justify-content-center">
                        <i class="fas fa-receipt" aria-hidden="true"></i>
                    </span>
                    <div>
                        <div class="small text-muted">Total de pedidos</div>
                        <div class="h4 fw-bold mb-0">{{ $summary['total'] }}</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm order-summary-card">
                <div class="card-body d-flex align-items-center gap-3">
                    <span class="order-summary-icon rounded-circle bg-warning-subtle text-warning-emphasis d-inline-flex align-items-center justify-content-center">
                        <i class="fas fa-clock" aria-hidden="true"></i>
                    </span>
                    <div>
                        <div class="small text-muted">Pendientes</div>
                        <div class="h4 fw-bold mb-0">{{ $summary['pending'] }}</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm order-summary-card">
                <div class="card-body d-flex align-items-center gap-3">
                    <span class="order-summary-icon rounded-circle bg-info-subtle text-info-emphasis d-inline-flex align-items-center justify-content-center">
                        <i class="fas fa-box" aria-hidden="true"></i>
                    </span>
                    <div>
                        <div class="small text-muted">En preparación</div>
                        <div class="h4 fw-bold mb-0">{{ $summary['processing'] }}</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm order-summary-card">
                <div class="card-body d-flex align-items-center gap-3">
                    <span class="order-summary-icon rounded-circle bg-success-subtle text-success d-inline-flex align-items-center justify-content-center">
                        <i class="fas fa-circle-check" aria-hidden="true"></i>
                    </span>
                    <div>
                        <div class="small text-muted">Ventas pagadas</div>
                        <div class="h4 fw-bold mb-0">${{ number_format($summary['paid_total'], 2) }}</div>
                        <div class="small text-muted">{{ $summary['paid'] }} pedidos pagados</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="card border-0 shadow-sm mb-4">
        <div class="card-header bg-white py-3">
            <h2 class="h5 mb-0 fw-bold">Listado de pedidos</h2>
        </div>

        <div class="card-body orders-card-body">
            <div class="row g-3 mb-3">
                <div class="col-sm-6 col-lg-3">
                    <label for="filtroEstadoPedido" class="form-label">Estado del pedido</label>
                    <select id="filtroEstadoPedido" class="form-select">
                        <option value="">Todos</option>
                        <option value="pendiente">Pendiente</option>
                        <option value="procesando">Procesando</option>
                        <option value="completado">Completado</option>
                        <option value="cancelado">Cancelado</option>
                    </select>
                </div>
                <div class="col-sm-6 col-lg-3">
                    <label for="filtroPagoPedido" class="form-label">Estado del pago</label>
                    <select id="filtroPagoPedido" class="form-select">
                        <option value="">Todos</option>
                        <option value="pending">Pendiente</option>
                        <option value="paid">Pagado</option>
                        <option value="failed">Fallido</option>
                    </select>
                </div>
                <div class="col-sm-6 col-lg-3">
                    <label for="filtroFechaDesde" class="form-label">Desde</label>
                    <input type="date" id="filtroFechaDesde" class="form-control">
                </div>
                <div class="col-sm-6 col-lg-3">
                    <label for="filtroFechaHasta" class="form-label">Hasta</label>
                    <input type="date" id="filtroFechaHasta" class="form-control">
                </div>
                <div class="col-12 d-flex justify-content-end">
                    <button type="button" class="btn btn-sm btn-outline-secondary" id="limpiarFiltrosPedidos">
                        <i class="fas fa-filter-circle-xmark me-1" aria-hidden="true"></i> Limpiar filtros
                    </button>
                </div>
            </div>

            <div class="table-responsive">
                <table id="tablaPedidos" class="table table-striped table-hover align-middle w-100">
                    <thead class="table-light">
                        <tr>
                            <th scope="col">Pedido</th>
                            <th scope="col">Cliente</th>
                            <th scope="col">Envío</th>
                            <th scope="col">Fecha</th>
                            <th scope="col">Total</th>
                            <th scope="col">Pago</th>
                            <th scope="col">Estado</th>
                            <th scope="col">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($orders as $order)
                            @php
                                $customerName = $order->customer_name ?: ($order->user->name ?? 'Cliente sin cuenta');
                                $customerEmail = $order->customer_email ?: ($order->user->email ?? 'Sin correo');
                                $shippingSummary = collect([$order->shipping_municipality, $order->shipping_state])
                                    ->filter()
                                    ->implode(', ');
                            @endphp
                            <tr>
                                <td data-order="{{ $order->id }}">#{{ $order->id }}</td>
                                <td>
                                    <div class="fw-semibold">{{ $customerName }}</div>
                                    <div class="small text-muted">{{ $customerEmail }}</div>
                                </td>
                                <td>{{ $shippingSummary ?: 'Sin datos de envío' }}</td>
                                <td data-order="{{ $order->created_at?->toIso8601String() }}">
                                    {{ $order->created_at?->format('d/m/Y H:i') ?? '—' }}
                                </td>
                                <td data-order="{{ $order->total }}">${{ number_format($order->total, 2) }}</td>
                                <td data-search="{{ $order->payment_status }}">
                                    @switch($order->payment_status)
                                        @case('paid')
                                            <span class="badge text-bg-success">Pagado</span>
                                            @break
                                        @case('failed')
                                            <span class="badge text-bg-danger">Fallido</span>
                                            @break
                                        @default
                                            <span class="badge text-bg-warning">Pendiente</span>
                                    @endswitch
                                    <div class="small text-muted">{{ ucfirst($order->payment_provider ?? 'Sin método') }}</div>
                                </td>
                                <td data-search="{{ $order->estado }}">
                                    @switch($order->estado)
                                        @case('procesando')
                                            <span class="badge text-bg-info">Procesando</span>
                                            @break
                                        @case('completado')
                                            <span class="badge text-bg-success">Completado</span>
                                            @break
                                        @case('cancelado')
                                            <span class="badge text-bg-danger">Cancelado</span>
                                            @break
                                        @default
                                            <span class="badge text-bg-warning">Pendiente</span>
                                    @endswitch
                                </td>
                                <td class="text-nowrap">
                                    <button
                                        type="button"
                                        class="btn btn-sm btn-outline-primary btn-detalle"
                                        data-order-id="{{ $order->id }}"
                                        data-order-total="{{ $order->total }}"
                                        data-order-status="{{ $order->estado }}"
                                        data-update-url="{{ route('admin.orders.update', ['pedido' => '__ORDER_ID__']) }}"
                                        data-customer-name="{{ $customerName }}"
                                        data-customer-email="{{ $customerEmail }}"
                                        data-customer-phone="{{ $order->customer_phone ?? 'Sin teléfono' }}"
                                        data-shipping-address="{{ $order->shipping_address ?? 'Sin dirección de envío' }}"
                                        data-shipping-references="{{ $order->shipping_references ?? 'Sin referencias' }}"
                                        data-order-notes="{{ $order->notas ?? 'Sin notas' }}"
                                        data-payment-status="{{ $order->payment_status }}"
                                        data-payment-provider="{{ $order->payment_provider ?? 'Sin método' }}"
                                        data-payment-reference="{{ $order->payment_reference ?? 'Sin referencia' }}"
                                        data-order-details="{{ $order->details->map(fn ($detail) => ['product' => $detail->product->nombre ?? 'Producto eliminado', 'quantity' => $detail->cantidad, 'price' => $detail->precio, 'subtotal' => $detail->cantidad * $detail->precio])->toJson() }}"
                                        aria-label="Ver detalle del pedido {{ $order->id }}"
                                        title="Ver detalle"
                                    >
                                        <i class="fas fa-eye" aria-hidden="true"></i>
                                        <span class="visually-hidden">Ver detalle</span>
                                    </button>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modalDetallePedido" tabindex="-1" aria-labelledby="modalDetallePedidoLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 shadow">
            <div class="modal-header">
                <h2 class="modal-title fs-5" id="modalDetallePedidoLabel">Detalle del Pedido <span id="detallePedidoNumero"></span></h2>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>
            <div class="modal-body">
                <div class="row g-3 mb-4">
                    <div class="col-md-6">
                        <h3 class="h6">Cliente</h3>
                        <div id="detalleClienteNombre" class="fw-semibold"></div>
                        <div id="detalleClienteCorreo" class="text-muted"></div>
                        <div id="detalleClienteTelefono" class="text-muted"></div>
                    </div>
                    <div class="col-md-6">
                        <h3 class="h6">Envío</h3>
                        <div id="detalleDireccionEnvio"></div>
                        <div id="detalleReferenciasEnvio" class="small text-muted mt-1"></div>
                        <div id="detalleNotasPedido" class="small mt-1"></div>
                    </div>
                    <div class="col-12">
                        <h3 class="h6">Pago</h3>
                        <span id="detalleEstadoPago" class="badge"></span>
                        <span id="detalleMetodoPago" class="text-muted"></span>
                        <div id="detalleReferenciaPago" class="small text-muted mt-1"></div>
                    </div>
                </div>
                <form id="formEstadoPedido" method="POST" class="border rounded bg-light p-3 mb-4">
                    @csrf
                    @method('PUT')
                    <div class="d-flex flex-column flex-sm-row align-items-sm-end gap-2">
                        <div class="flex-grow-1">
                            <label for="nuevoEstadoPedido" class="form-label fw-semibold">Actualizar estado del pedido</label>
                            <select id="nuevoEstadoPedido" name="estado" class="form-select">
                                <option value="pendiente">Pendiente</option>
                                <option value="procesando">En preparación</option>
                                <option value="completado">Completado</option>
                                <option value="cancelado">Cancelado</option>
                            </select>
                        </div>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-check me-1" aria-hidden="true"></i> Guardar estado
                        </button>
                    </div>
                </form>
                <h3 class="h6">Productos</h3>
                <p class="fw-semibold">Total del pedido: <span id="detalleTotalPedido"></span></p>
                <div class="table-responsive">
                    <table class="table table-sm">
                        <thead>
                            <tr>
                                <th>Producto</th>
                                <th class="text-end">Cantidad</th>
                                <th class="text-end">Precio</th>
                                <th class="text-end">Subtotal</th>
                            </tr>
                        </thead>
                        <tbody id="detalleProductosPedido"></tbody>
                    </table>
                </div>
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
        const table = $('#tablaPedidos').DataTable({
            dom: "<'row align-items-center mb-3'<'col-md-6'B><'col-md-6'f>>" +
                "<'row'<'col-12'tr>>" +
                "<'row align-items-center mt-3'<'col-md-5'i><'col-md-7'p>>",
            buttons: [
                { extend: 'copyHtml5', text: 'Copiar', className: 'btn btn-secondary btn-sm', exportOptions: { columns: [0, 3, 4, 5, 6] } },
                { extend: 'csvHtml5', text: 'CSV', className: 'btn btn-secondary btn-sm', exportOptions: { columns: [0, 3, 4, 5, 6] } },
                { extend: 'excelHtml5', text: 'Excel', className: 'btn btn-success btn-sm', exportOptions: { columns: [0, 3, 4, 5, 6] } },
                { extend: 'pdfHtml5', text: 'PDF', className: 'btn btn-danger btn-sm', exportOptions: { columns: [0, 3, 4, 5, 6] } },
                { extend: 'print', text: 'Imprimir', className: 'btn btn-info btn-sm', exportOptions: { columns: [0, 3, 4, 5, 6] } }
            ],
            order: [[0, 'desc']],
            pageLength: 10,
            language: {
                search: 'Buscar:',
                searchPlaceholder: 'Buscar por pedido...',
                lengthMenu: 'Mostrar _MENU_ registros',
                info: 'Mostrando _START_ a _END_ de _TOTAL_ pedidos',
                infoEmpty: 'Mostrando 0 a 0 de 0 pedidos',
                infoFiltered: '(filtrado de _MAX_ pedidos en total)',
                zeroRecords: 'No hay pedidos que coincidan con los filtros seleccionados.',
                loadingRecords: 'Cargando...',
                processing: 'Procesando...',
                paginate: {
                    first: 'Primero',
                    last: 'Último',
                    next: 'Siguiente',
                    previous: 'Anterior'
                },
                emptyTable: '<div class="py-4 text-center"><i class="fas fa-box-open fa-2x text-muted mb-3" aria-hidden="true"></i><div class="fw-semibold">Aún no hay pedidos</div><div class="text-muted">Los pedidos aparecerán aquí cuando un cliente complete una compra.</div></div>'
            }
        });

        $('#filtroEstadoPedido').on('change', function () {
            table.column(6).search(this.value).draw();
        });

        $('#filtroPagoPedido').on('change', function () {
            table.column(5).search(this.value).draw();
        });

        const filterOrdersByDate = function () {
            table.draw();
        };
        $('#filtroFechaDesde, #filtroFechaHasta').on('change', filterOrdersByDate);

        $('#limpiarFiltrosPedidos').on('click', function () {
            $('#filtroEstadoPedido, #filtroPagoPedido, #filtroFechaDesde, #filtroFechaHasta').val('');
            table.search('').columns().search('').draw();
        });

        $.fn.dataTable.ext.search.push(function (settings, data, dataIndex) {
            if (settings.nTable.id !== 'tablaPedidos') {
                return true;
            }

            const row = table.row(dataIndex).node();
            const orderDate = row.cells[3].dataset.order || '';
            const date = orderDate.slice(0, 10);
            const from = document.getElementById('filtroFechaDesde').value;
            const to = document.getElementById('filtroFechaHasta').value;

            return (!from || date >= from) && (!to || date <= to);
        });

        $('#tablaPedidos').on('click', '.btn-detalle', function () {
            const button = this;
            const setText = (id, value) => {
                document.getElementById(id).textContent = value;
            };

            setText('detallePedidoNumero', `#${button.dataset.orderId}`);
            setText('detalleClienteNombre', button.dataset.customerName);
            setText('detalleClienteCorreo', button.dataset.customerEmail);
            setText('detalleClienteTelefono', button.dataset.customerPhone);
            setText('detalleDireccionEnvio', button.dataset.shippingAddress);
            setText('detalleReferenciasEnvio', button.dataset.shippingReferences);
            setText('detalleNotasPedido', `Notas: ${button.dataset.orderNotes}`);
            setText('detalleTotalPedido', `$${Number(button.dataset.orderTotal).toFixed(2)}`);
            setText('detalleMetodoPago', button.dataset.paymentProvider);
            setText('detalleReferenciaPago', `Referencia: ${button.dataset.paymentReference}`);
            const statusForm = document.getElementById('formEstadoPedido');
            statusForm.action = button.dataset.updateUrl.replace('__ORDER_ID__', encodeURIComponent(button.dataset.orderId));
            statusForm.elements.namedItem('estado').value = button.dataset.orderStatus;

            const paymentStatus = document.getElementById('detalleEstadoPago');
            const paymentLabels = { paid: 'Pagado', failed: 'Fallido', pending: 'Pendiente' };
            const paymentClasses = { paid: 'text-bg-success', failed: 'text-bg-danger', pending: 'text-bg-warning' };
            paymentStatus.textContent = paymentLabels[button.dataset.paymentStatus] || button.dataset.paymentStatus;
            paymentStatus.className = `badge ${paymentClasses[button.dataset.paymentStatus] || 'text-bg-secondary'} me-2`;

            const productsBody = document.getElementById('detalleProductosPedido');
            productsBody.replaceChildren();

            JSON.parse(button.dataset.orderDetails).forEach((item) => {
                const row = document.createElement('tr');
                [item.product, item.quantity, `$${Number(item.price).toFixed(2)}`, `$${Number(item.subtotal).toFixed(2)}`]
                    .forEach((value, index) => {
                        const cell = document.createElement('td');
                        cell.textContent = value;
                        if (index > 0) {
                            cell.className = 'text-end';
                        }
                        row.appendChild(cell);
                    });
                productsBody.appendChild(row);
            });

            bootstrap.Modal.getOrCreateInstance(document.getElementById('modalDetallePedido')).show();
        });

        @if(session('success'))
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
