document.addEventListener('DOMContentLoaded', function () {
    const form = document.getElementById('checkout-form');

    if (!form) {
        return;
    }

    const paymentOptions = form.querySelectorAll('input[name="metodo_pago"]');
    const paypalPanel = document.getElementById('paypal-panel');
    const paypalContainer = document.getElementById('paypal-button-container');
    const paypalMessage = document.getElementById('paypal-message');
    let paypalButtonsRendered = false;
    let paypalFlow = null;

    function showPaypalMessage(message) {
        paypalMessage.textContent = message;
    }

    function requestError(response, body) {
        if (body.errors) {
            const firstError = Object.values(body.errors).flat()[0];

            if (firstError) {
                return firstError;
            }
        }

        return body.message || `No se pudo iniciar el pago (HTTP ${response.status}).`;
    }

    async function postPayment(provider) {
        const url = form.action.replace('__provider__', encodeURIComponent(provider));
        const csrfToken = form.querySelector('input[name="_token"]').value;
        const response = await fetch(url, {
            method: 'POST',
            headers: {
                Accept: 'application/json',
                'X-CSRF-TOKEN': csrfToken,
            },
            body: new FormData(form),
        });
        const body = await response.json();

        if (!response.ok) {
            throw new Error(requestError(response, body));
        }

        return body;
    }

    function renderPaypalButtons() {
        if (form.dataset.paypalEnabled !== 'true') {
            showPaypalMessage('PayPal no está configurado. Inténtalo más tarde.');
            return;
        }

        if (!window.paypal) {
            showPaypalMessage('No se pudo cargar PayPal. Actualiza la página e inténtalo de nuevo.');
            return;
        }

        if (paypalButtonsRendered) {
            return;
        }

        const buttons = window.paypal.Buttons({
            createOrder: async function () {
                if (!form.reportValidity()) {
                    throw new Error('Completa los datos de envío requeridos.');
                }

                showPaypalMessage('Conectando con PayPal…');
                try {
                    paypalFlow = await postPayment('paypal');
                    return paypalFlow.order_id;
                } catch (error) {
                    showPaypalMessage(error.message || 'No se pudo crear la orden de PayPal.');
                    throw error;
                }
            },
            onApprove: async function (data) {
                if (!paypalFlow) {
                    showPaypalMessage('No se encontró la orden de PayPal para confirmar.');
                    return;
                }

                showPaypalMessage('Confirmando el pago de forma segura…');

                try {
                    const csrfToken = form.querySelector('input[name="_token"]').value;
                    const response = await fetch(paypalFlow.capture_url, {
                        method: 'POST',
                        headers: {
                            Accept: 'application/json',
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': csrfToken,
                        },
                        body: JSON.stringify({ paypal_order_id: data.orderID }),
                    });
                    const body = await response.json();

                    if (!response.ok) {
                        throw new Error(requestError(response, body));
                    }

                    window.location.assign(body.success_url);
                } catch (error) {
                    showPaypalMessage(error.message || 'No se pudo confirmar el pago. Contacta con soporte antes de volver a intentarlo.');
                }
            },
            onCancel: function () {
                showPaypalMessage('El pago fue cancelado. Tu pedido sigue pendiente y no se marcó como pagado.');
            },
            onError: function (error) {
                console.error('PayPal checkout error:', error);
                showPaypalMessage('Ocurrió un error con PayPal. El pedido no se marcó como pagado.');
            },
        });

        if (!buttons.isEligible()) {
            showPaypalMessage('PayPal no está disponible en este navegador.');
            return;
        }

        paypalButtonsRendered = true;

        buttons.render(paypalContainer).catch(function (error) {
            paypalButtonsRendered = false;
            console.error('PayPal buttons could not be rendered:', error);
            showPaypalMessage('No se pudieron mostrar los botones de PayPal. Inténtalo más tarde.');
        });
    }

    function updatePaymentMethod() {
        const selectedOption = form.querySelector('input[name="metodo_pago"]:checked');

        if (selectedOption?.value === 'paypal') {
            paypalPanel.hidden = false;
            paypalPanel.setAttribute('aria-hidden', 'false');
            renderPaypalButtons();
        }
    }

    paymentOptions.forEach(function (option) {
        option.addEventListener('change', updatePaymentMethod);
    });

    form.addEventListener('submit', function (event) {
        event.preventDefault();
    });

    updatePaymentMethod();
});
