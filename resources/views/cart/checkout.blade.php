@extends('layouts.app')

@section('content')

<div class="checkout-container">
    <div class="checkout-form">
        <h1 class="checkout-title">Datos de Envío y Pago</h1>
        <p style="color: var(--text-secondary); margin-bottom: 2rem;">Completa tus datos para procesar el pedido.</p>
        
        <form
            id="checkout-form"
            action="{{ route('payments.start', ['provider' => '__provider__']) }}"
            method="POST"
            data-paypal-enabled="{{ config('services.paypal.client_id') ? 'true' : 'false' }}"
        >
            @csrf
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem;">
                <div class="form-group floating-field">
                    <input id="nombre" type="text" name="nombre" class="form-control" required placeholder=" ">
                    <label class="form-label" for="nombre">Nombre <span class="required-marker" aria-hidden="true">*</span></label>
                </div>
                <div class="form-group floating-field">
                    <input id="apellidos" type="text" name="apellidos" class="form-control" required placeholder=" ">
                    <label class="form-label" for="apellidos">Apellidos <span class="required-marker" aria-hidden="true">*</span></label>
                </div>
            </div>

            <div class="form-group floating-field">
                <input id="email" type="email" name="email" class="form-control" required placeholder=" " autocomplete="email">
                <label class="form-label" for="email">Correo Electrónico <span class="required-marker" aria-hidden="true">*</span></label>
            </div>

            <div class="form-group">
                <label class="form-label" for="phone_number">Teléfono <span class="required-marker" aria-hidden="true">*</span></label>
                <div class="phone-input-group">
                    <select id="phone_code" name="phone_code" class="form-control phone-country-code" aria-label="Código de país" required>
                        <option value="+52" selected>🇲🇽 México (+52)</option>
                        <option value="+1">🇺🇸 Estados Unidos / Canadá (+1)</option>
                        <option value="+34">🇪🇸 España (+34)</option>
                        <option value="+57">🇨🇴 Colombia (+57)</option>
                        <option value="+54">🇦🇷 Argentina (+54)</option>
                        <option value="+56">🇨🇱 Chile (+56)</option>
                        <option value="+51">🇵🇪 Perú (+51)</option>
                        <option value="+55">🇧🇷 Brasil (+55)</option>
                        <option value="+44">🇬🇧 Reino Unido (+44)</option>
                        <option value="+33">🇫🇷 Francia (+33)</option>
                        <option value="+49">🇩🇪 Alemania (+49)</option>
                        <option value="+39">🇮🇹 Italia (+39)</option>
                        <option value="+81">🇯🇵 Japón (+81)</option>
                        <option value="+86">🇨🇳 China (+86)</option>
                        <option value="+91">🇮🇳 India (+91)</option>
                    </select>
                    <div class="floating-field phone-local-field">
                        <input
                            id="phone_number"
                            type="tel"
                            name="phone_number"
                            class="form-control phone-local-number"
                            inputmode="numeric"
                            autocomplete="tel-national"
                            pattern="[0-9]{10}"
                            minlength="10"
                            maxlength="10"
                            required
                            placeholder=" "
                            aria-describedby="phone-help"
                        >
                        <label class="form-label" for="phone_number">10 dígitos</label>
                    </div>
                </div>
                <small id="phone-help" class="phone-help">Ingresa los 10 dígitos locales, sin espacios ni guiones.</small>
            </div>

            <fieldset class="shipping-address">
                <legend>Dirección de Envío</legend>
                <div class="shipping-address-grid">
                    <div class="form-group floating-field">
                        <input id="shipping_street" type="text" name="shipping_street" class="form-control" maxlength="150" autocomplete="address-line1" required placeholder=" ">
                        <label class="form-label" for="shipping_street">Calle / Avenida <span class="required-marker" aria-hidden="true">*</span></label>
                    </div>
                    <div class="form-group floating-field">
                        <input id="shipping_exterior_number" type="text" name="shipping_exterior_number" class="form-control" maxlength="30" required placeholder=" ">
                        <label class="form-label" for="shipping_exterior_number">Número Exterior <span class="required-marker" aria-hidden="true">*</span></label>
                    </div>
                    <div class="form-group floating-field">
                        <input id="shipping_interior_number" type="text" name="shipping_interior_number" class="form-control" maxlength="30" autocomplete="address-line2" placeholder=" ">
                        <label class="form-label" for="shipping_interior_number">Número Interior / Departamento / Piso <span>(opcional)</span></label>
                    </div>
                    <div class="form-group floating-field">
                        <input id="shipping_neighborhood" type="text" name="shipping_neighborhood" class="form-control" maxlength="120" required placeholder=" ">
                        <label class="form-label" for="shipping_neighborhood">Colonia / Barrio <span class="required-marker" aria-hidden="true">*</span></label>
                    </div>
                    <div class="form-group floating-field">
                        <input id="shipping_postal_code" type="text" name="shipping_postal_code" class="form-control" inputmode="numeric" autocomplete="postal-code" pattern="[0-9]{5}" minlength="5" maxlength="5" required placeholder=" " aria-describedby="postal-code-help">
                        <label class="form-label" for="shipping_postal_code">Código Postal <span class="required-marker" aria-hidden="true">*</span></label>
                        <small id="postal-code-help" class="phone-help">Ingresa los 5 dígitos del código postal.</small>
                    </div>
                    <div class="form-group floating-field">
                        <input id="shipping_municipality" type="text" name="shipping_municipality" class="form-control" maxlength="120" required placeholder=" ">
                        <label class="form-label" for="shipping_municipality">Alcaldía / Municipio <span class="required-marker" aria-hidden="true">*</span></label>
                    </div>
                    <div class="form-group floating-field floating-select">
                        <select id="shipping_state" name="shipping_state" class="form-control" autocomplete="address-level1" required>
                            <option value="Aguascalientes">Aguascalientes</option>
                            <option value="Baja California">Baja California</option>
                            <option value="Baja California Sur">Baja California Sur</option>
                            <option value="Campeche">Campeche</option>
                            <option value="Chiapas">Chiapas</option>
                            <option value="Chihuahua">Chihuahua</option>
                            <option value="Ciudad de México" selected>Ciudad de México</option>
                            <option value="Coahuila">Coahuila</option>
                            <option value="Colima">Colima</option>
                            <option value="Durango">Durango</option>
                            <option value="Estado de México">Estado de México</option>
                            <option value="Guanajuato">Guanajuato</option>
                            <option value="Guerrero">Guerrero</option>
                            <option value="Hidalgo">Hidalgo</option>
                            <option value="Jalisco">Jalisco</option>
                            <option value="Michoacán">Michoacán</option>
                            <option value="Morelos">Morelos</option>
                            <option value="Nayarit">Nayarit</option>
                            <option value="Nuevo León">Nuevo León</option>
                            <option value="Oaxaca">Oaxaca</option>
                            <option value="Puebla">Puebla</option>
                            <option value="Querétaro">Querétaro</option>
                            <option value="Quintana Roo">Quintana Roo</option>
                            <option value="San Luis Potosí">San Luis Potosí</option>
                            <option value="Sinaloa">Sinaloa</option>
                            <option value="Sonora">Sonora</option>
                            <option value="Tabasco">Tabasco</option>
                            <option value="Tamaulipas">Tamaulipas</option>
                            <option value="Tlaxcala">Tlaxcala</option>
                            <option value="Veracruz">Veracruz</option>
                            <option value="Yucatán">Yucatán</option>
                            <option value="Zacatecas">Zacatecas</option>
                        </select>
                        <label class="form-label" for="shipping_state">Estado <span class="required-marker" aria-hidden="true">*</span></label>
                    </div>
                    <div class="form-group floating-field floating-textarea shipping-address-references">
                        <textarea id="shipping_references" name="shipping_references" class="form-control" rows="3" maxlength="1000" placeholder=" "></textarea>
                        <label class="form-label" for="shipping_references">Entre calles / Referencias <span>(opcional)</span></label>
                    </div>
                </div>
            </fieldset>

            <fieldset class="payment-methods">
                <legend>Método de Pago</legend>
                <div class="payment-method-options">
                    <label class="payment-method-card">
                        <input class="payment-method-radio" type="radio" name="metodo_pago" value="paypal" checked>
                        <span class="payment-method-logo paypal-logo">
                            <img src="https://cdn.simpleicons.org/paypal/003087" alt="Logo de PayPal" width="48" height="48">
                        </span>
                        <span class="payment-method-copy">
                            <strong>PayPal</strong>
                            <small>Paga de forma segura</small>
                        </span>
                        <span class="payment-method-check" aria-hidden="true"></span>
                    </label>
                </div>
            </fieldset>

            <section id="paypal-panel" class="payment-panel" aria-labelledby="paypal-title">
                <h3 id="paypal-title">Paga con PayPal</h3>
                <div id="paypal-button-container"></div>
                <p id="paypal-message" class="payment-message" role="status" aria-live="polite"></p>
            </section>

        </form>
    </div>

    <aside class="order-summary">
        <h3 style="font-size: 1.5rem; margin-bottom: 2rem; border-bottom: 1px solid var(--surface-border); padding-bottom: 1rem;">Tu Pedido</h3>
        
        @php $total = 0; @endphp
        @foreach($cart as $details)
            @php $total += $details['price'] * $details['quantity']; @endphp
            <div class="order-item">
                <div style="flex-grow: 1;">
                    <div style="font-weight: 600; color: var(--text-primary);">{{ $details['name'] }}</div>
                    <div style="color: var(--text-secondary); font-size: 0.9rem;">Cant: {{ $details['quantity'] }}</div>
                </div>
                <div style="font-weight: 600; color: var(--accent-primary);">
                    ${{ number_format($details['price'] * $details['quantity'], 2) }}
                </div>
            </div>
        @endforeach

        <div style="display: flex; justify-content: space-between; margin-top: 2rem; padding-top: 1rem; border-top: 1px solid var(--surface-border); font-size: 1.5rem; font-weight: 700;">
            <span>Total</span>
            <span style="color: var(--accent-primary);">${{ number_format($total, 2) }}</span>
        </div>
        
        <p style="font-size: 0.85rem; color: var(--text-secondary); margin-top: 1.5rem; text-align: center;">
            Los impuestos y gastos de envío se calcularán en el siguiente paso.
        </p>
    </aside>
</div>

@endsection

@push('scripts')
    @if (config('services.paypal.client_id'))
        <script
            src="https://www.paypal.com/sdk/js?client-id={{ urlencode(config('services.paypal.client_id')) }}&currency={{ urlencode(config('services.payments.currency')) }}&intent=capture"
            defer
        ></script>
    @endif
    <script src="{{ asset('js/checkout.js') }}" defer></script>
@endpush

@push('styles')
    <style>
        .payment-methods {
            border: 0;
            margin: 2rem 0 1rem;
        }

        .required-marker {
            color: #dc3545;
            font-weight: 700;
        }

        .floating-field {
            position: relative;
        }

        .floating-field > .form-control {
            height: 3.75rem;
            padding: 1.35rem 1rem 0.5rem;
        }

        .floating-field > .form-label {
            color: var(--text-secondary);
            left: 1rem;
            margin: 0;
            max-width: calc(100% - 2rem);
            overflow: hidden;
            pointer-events: none;
            position: absolute;
            text-overflow: ellipsis;
            top: 1.1rem;
            transition: top 0.18s ease, color 0.18s ease, font-size 0.18s ease;
            white-space: nowrap;
        }

        .floating-field > .form-control:focus + .form-label,
        .floating-field > .form-control:not(:placeholder-shown) + .form-label,
        .floating-select > .form-label {
            color: var(--accent-primary);
            font-size: 0.75rem;
            top: 0.35rem;
        }

        .floating-field > .form-control:focus + .form-label .required-marker,
        .floating-field > .form-control:not(:placeholder-shown) + .form-label .required-marker,
        .floating-select > .form-label .required-marker {
            color: #dc3545;
        }

        .floating-textarea > .form-control {
            height: auto;
            min-height: 7rem;
            padding-top: 1.65rem;
            resize: vertical;
        }

        .floating-textarea > .form-label {
            top: 1.1rem;
        }

        .floating-textarea > .form-control:focus + .form-label,
        .floating-textarea > .form-control:not(:placeholder-shown) + .form-label {
            top: 0.35rem;
        }

        .phone-input-group {
            display: flex;
            gap: 0.5rem;
        }

        .phone-country-code {
            flex: 0 0 48%;
            max-width: 13rem;
            height: 3.75rem;
        }

        .phone-local-field {
            flex: 1;
            min-width: 0;
        }

        .phone-local-number {
            min-width: 0;
        }

        .phone-local-field > .form-label {
            left: 1rem;
        }

        .phone-help {
            color: var(--text-secondary);
            display: block;
            margin-top: 0.4rem;
        }

        .shipping-address {
            border: 0;
            margin: 1.5rem 0;
            padding: 0;
        }

        .shipping-address legend {
            font-size: 1.5rem;
            font-weight: 600;
            margin-bottom: 1rem;
        }

        .shipping-address-grid {
            display: grid;
            gap: 0 1.25rem;
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }

        .shipping-address-references {
            grid-column: 1 / -1;
        }

        .shipping-address label span:not(.required-marker) {
            color: var(--text-secondary);
            font-size: 0.9em;
            font-weight: 400;
        }

        .shipping-address .required-marker {
            color: #dc3545;
        }

        .payment-methods legend {
            font-size: 1.5rem;
            font-weight: 600;
            margin-bottom: 1rem;
        }

        .payment-method-options {
            display: grid;
            gap: 0.875rem;
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }

        .payment-method-card {
            align-items: center;
            background: var(--surface-color);
            border: 2px solid var(--surface-border);
            border-radius: 12px;
            cursor: pointer;
            display: flex;
            gap: 0.875rem;
            min-height: 5.5rem;
            padding: 1rem;
            position: relative;
            transition: border-color 0.2s ease, background-color 0.2s ease, box-shadow 0.2s ease;
        }

        .payment-method-card:has(.payment-method-radio:checked) {
            background: rgba(14, 165, 233, 0.06);
            border-color: var(--accent-primary);
            box-shadow: 0 0 0 3px rgba(14, 165, 233, 0.12);
        }

        .payment-method-card:focus-within {
            outline: 3px solid rgba(14, 165, 233, 0.3);
            outline-offset: 2px;
        }

        .payment-method-radio {
            accent-color: var(--accent-primary);
            cursor: pointer;
            flex: 0 0 auto;
            height: 1.1rem;
            margin: 0;
            width: 1.1rem;
        }

        .payment-method-logo {
            align-items: center;
            background: #fff;
            border: 1px solid var(--surface-border);
            border-radius: 9px;
            display: flex;
            flex: 0 0 3.25rem;
            height: 3.25rem;
            justify-content: center;
            width: 3.25rem;
        }

        .payment-method-logo img {
            display: block;
            height: 2.25rem;
            object-fit: contain;
            width: 2.25rem;
        }

        .payment-method-copy {
            display: flex;
            flex: 1;
            flex-direction: column;
            gap: 0.125rem;
            min-width: 0;
        }

        .payment-method-copy strong {
            color: var(--text-primary);
            font-size: 1rem;
        }

        .payment-method-copy small {
            color: var(--text-secondary);
            font-size: 0.825rem;
        }

        .payment-method-check {
            align-items: center;
            border: 1px solid var(--surface-border);
            border-radius: 50%;
            color: #fff;
            display: flex;
            flex: 0 0 1.25rem;
            height: 1.25rem;
            justify-content: center;
            width: 1.25rem;
        }

        .payment-method-card:has(.payment-method-radio:checked) .payment-method-check {
            background: var(--accent-primary);
            border-color: var(--accent-primary);
        }

        .payment-method-card:has(.payment-method-radio:checked) .payment-method-check::after {
            content: "✓";
            font-size: 0.8rem;
            font-weight: 700;
        }

        .payment-panel {
            margin-top: 1.5rem;
        }

        .payment-panel h3 {
            margin-bottom: 0.5rem;
        }

        .payment-panel p,
        .payment-message {
            color: var(--text-secondary);
            margin-top: 0.75rem;
        }

        .payment-message:empty {
            display: none;
        }

        .checkout-container > .order-summary {
            top: 1.5rem;
        }

        @media (max-width: 480px) {
            .payment-method-options {
                grid-template-columns: minmax(0, 1fr);
            }

            .phone-input-group {
                flex-direction: column;
            }

            .phone-country-code {
                flex-basis: auto;
                max-width: none;
            }

            .phone-local-field {
                flex: 1 1 auto;
            }

            .shipping-address-grid {
                grid-template-columns: minmax(0, 1fr);
            }

            .shipping-address-references {
                grid-column: auto;
            }
        }
    </style>
@endpush
