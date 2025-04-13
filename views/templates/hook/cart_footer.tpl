<div class="wacart-cart-footer">
    <div class="wacart-request-button">
        <a href="{$mpwacart_request_url}" class="btn btn-success btn-lg">
            <i class="fab fa-whatsapp"></i> {l s='Richiedi preventivo via WhatsApp' mod='mpwacart'}
        </a>
        <p class="wacart-info">{l s='Invia il tuo carrello via WhatsApp e ricevi un preventivo personalizzato' mod='mpwacart'}</p>
    </div>
</div>

{if isset($hide_checkout_button) && $hide_checkout_button}
<script type="text/javascript">
    document.addEventListener('DOMContentLoaded', function() {
        // Nascondi il pulsante di checkout
        // Selettore più specifico basato sulla struttura HTML fornita
        var checkoutButtons = document.querySelectorAll('.checkout.cart-detailed-actions a.btn-primary, .cart-detailed-actions.js-cart-detailed-actions a.btn-primary');
        if (checkoutButtons.length > 0) {
            checkoutButtons.forEach(function(button) {
                button.style.display = 'none';
            });
        } else {
            // Fallback: nascondi l'intero contenitore del pulsante se non troviamo il pulsante specifico
            var checkoutContainers = document.querySelectorAll('.checkout.cart-detailed-actions, .cart-detailed-actions.js-cart-detailed-actions');
            checkoutContainers.forEach(function(container) {
                container.style.display = 'none';
            });
        }
        
        console.log('MpWaCart: Tentativo di nascondere il pulsante checkout');
    });
</script>
{/if}
