<div class="panel">
    <div class="panel-heading">
        <i class="icon-whatsapp"></i> {l s='Dettagli richiesta WhatsApp' mod='mpwacart'} #{$request->id}
    </div>
    
    <div class="row">
        <div class="col-lg-6">
            <div class="panel">
                <div class="panel-heading">
                    <i class="icon-user"></i> {l s='Informazioni cliente' mod='mpwacart'}
                </div>
                <div class="form-horizontal">
                    <div class="form-group">
                        <label class="control-label col-lg-3">{l s='Nome' mod='mpwacart'}</label>
                        <div class="col-lg-9">
                            <p class="form-control-static">
                                <a href="{$link->getAdminLink('AdminCustomers')}&id_customer={$customer->id}&viewcustomer">
                                    {$customer->firstname} {$customer->lastname}
                                </a>
                            </p>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label col-lg-3">{l s='Email' mod='mpwacart'}</label>
                        <div class="col-lg-9">
                            <p class="form-control-static">
                                <a href="mailto:{$customer->email}">{$customer->email}</a>
                            </p>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label col-lg-3">{l s='Telefono WhatsApp' mod='mpwacart'}</label>
                        <div class="col-lg-9">
                            <p class="form-control-static">
                                <a href="{$whatsapp_link}" target="_blank" class="btn btn-xs btn-success">
                                    <i class="icon-whatsapp"></i> {$request->phone_number}
                                </a>
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-lg-6">
            <div class="panel">
                <div class="panel-heading">
                    <i class="icon-info-circle"></i> {l s='Informazioni richiesta' mod='mpwacart'}
                </div>
                <div class="form-horizontal">
                    <div class="form-group">
                        <label class="control-label col-lg-3">{l s='Data' mod='mpwacart'}</label>
                        <div class="col-lg-9">
                            <p class="form-control-static">
                                {$request->date_add|date_format:"%d/%m/%Y %H:%M:%S"}
                            </p>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label col-lg-3">{l s='ID Carrello' mod='mpwacart'}</label>
                        <div class="col-lg-9">
                            <p class="form-control-static">
                                <a href="{$link->getAdminLink('AdminCarts')}&id_cart={$cart->id}&viewcart">
                                    #{$cart->id}
                                </a>
                            </p>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label col-lg-3">{l s='Stato' mod='mpwacart'}</label>
                        <div class="col-lg-9">
                            <select id="wacart-status" class="form-control">
                                {foreach from=$statuses key=status_key item=status_label}
                                    <option value="{$status_key}" {if $status_key == $current_status}selected{/if}>
                                        {$status_label}
                                    </option>
                                {/foreach}
                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label col-lg-3">{l s='PDF' mod='mpwacart'}</label>
                        <div class="col-lg-9">
                            <p class="form-control-static">
                                <a href="{$pdf_url}" target="_blank" class="btn btn-default">
                                    <i class="icon-file-pdf-o"></i> {l s='Visualizza PDF' mod='mpwacart'}
                                </a>
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="panel">
        <div class="panel-heading">
            <i class="icon-shopping-cart"></i> {l s='Prodotti nel carrello' mod='mpwacart'}
        </div>
        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th>{l s='Prodotto' mod='mpwacart'}</th>
                        <th>{l s='Riferimento' mod='mpwacart'}</th>
                        <th class="text-center">{l s='Quantità' mod='mpwacart'}</th>
                        <th class="text-right">{l s='Prezzo unitario' mod='mpwacart'}</th>
                        <th class="text-right">{l s='Totale' mod='mpwacart'}</th>
                    </tr>
                </thead>
                <tbody>
                    {foreach from=$products item=product}
                        <tr>
                            <td>
                                <a href="{$link->getAdminLink('AdminProducts')}&id_product={$product.id_product}&updateproduct">
                                    {$product.name}
                                </a>
                                {if isset($product.attributes) && $product.attributes}
                                    <br><small>{$product.attributes}</small>
                                {/if}
                            </td>
                            <td>{$product.reference}</td>
                            <td class="text-center">{$product.cart_quantity}</td>
                            <td class="text-right">{$product.price|currency}</td>
                            <td class="text-right">{$product.total|currency}</td>
                        </tr>
                    {/foreach}
                </tbody>
                <tfoot>
                    <tr>
                        <td colspan="4" class="text-right"><strong>{l s='Totale prodotti' mod='mpwacart'}</strong></td>
                        <td class="text-right">{$request->total_products|currency}</td>
                    </tr>
                    <tr>
                        <td colspan="4" class="text-right"><strong>{l s='Spedizione' mod='mpwacart'}</strong></td>
                        <td class="text-right">{$request->total_shipping|currency}</td>
                    </tr>
                    <tr>
                        <td colspan="4" class="text-right"><strong>{l s='Totale' mod='mpwacart'}</strong></td>
                        <td class="text-right">{$request->total|currency}</td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
    
    <div class="panel">
        <div class="panel-heading">
            <i class="icon-comments"></i> {l s='Conversazione WhatsApp' mod='mpwacart'}
        </div>
        <div class="wacart-conversation">
            {if $conversations|count > 0}
                <div class="wacart-messages">
                    {foreach from=$conversations item=conversation}
                        <div class="wacart-message {if $conversation.direction == 'outgoing'}wacart-message-outgoing{else}wacart-message-incoming{/if}">
                            <div class="wacart-message-content">
                                {$conversation.message|nl2br}
                            </div>
                            <div class="wacart-message-time">
                                {$conversation.date_add|date_format:"%d/%m/%Y %H:%M"}
                            </div>
                        </div>
                    {/foreach}
                </div>
            {else}
                <div class="alert alert-info">
                    {l s='Nessun messaggio nella conversazione.' mod='mpwacart'}
                </div>
            {/if}
            
            <div class="wacart-message-form">
                <div class="form-group">
                    <textarea id="wacart-new-message" class="form-control" rows="3" placeholder="{l s='Scrivi un messaggio...' mod='mpwacart'}"></textarea>
                </div>
                <div class="form-group">
                    <button id="wacart-send-message" class="btn btn-primary">
                        <i class="icon-paper-plane"></i> {l s='Invia messaggio' mod='mpwacart'}
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">
    $(document).ready(function() {
        // Aggiorna lo stato
        $('#wacart-status').change(function() {
            var status = $(this).val();
            $.ajax({
                url: '{$link->getAdminLink('AdminWaCartRequests')|addslashes}',
                type: 'POST',
                dataType: 'json',
                data: {
                    ajax: 1,
                    action: 'UpdateStatus',
                    id_request: {$request->id},
                    status: status
                },
                success: function(response) {
                    if (response.success) {
                        showSuccessMessage(response.message);
                    } else {
                        showErrorMessage(response.message);
                    }
                },
                error: function() {
                    showErrorMessage('{l s='Si è verificato un errore durante l\'aggiornamento dello stato.' mod='mpwacart' js=1}');
                }
            });
        });
        
        // Invia messaggio
        $('#wacart-send-message').click(function() {
            var message = $('#wacart-new-message').val();
            if (!message.trim()) {
                showErrorMessage('{l s='Inserisci un messaggio da inviare.' mod='mpwacart' js=1}');
                return;
            }
            
            $.ajax({
                url: '{$link->getAdminLink('AdminWaCartRequests')|addslashes}',
                type: 'POST',
                dataType: 'json',
                data: {
                    ajax: 1,
                    action: 'SendMessage',
                    id_request: {$request->id},
                    message: message
                },
                success: function(response) {
                    if (response.success) {
                        // Aggiungi il messaggio alla conversazione
                        var messageHtml = '<div class="wacart-message wacart-message-outgoing">' +
                            '<div class="wacart-message-content">' + message.replace(/\n/g, '<br>') + '</div>' +
                            '<div class="wacart-message-time">' + new Date().toLocaleString() + '</div>' +
                            '</div>';
                        $('.wacart-messages').append(messageHtml);
                        
                        // Pulisci il campo di input
                        $('#wacart-new-message').val('');
                        
                        // Aggiorna lo stato se necessario
                        if ($('#wacart-status').val() == 'pending' || $('#wacart-status').val() == 'sent') {
                            $('#wacart-status').val('replied').trigger('change');
                        }
                        
                        showSuccessMessage('{l s='Messaggio inviato con successo.' mod='mpwacart' js=1}');
                    } else {
                        showErrorMessage(response.message);
                    }
                },
                error: function() {
                    showErrorMessage('{l s='Si è verificato un errore durante l\'invio del messaggio.' mod='mpwacart' js=1}');
                }
            });
        });
    });
</script>

<style type="text/css">
    .wacart-conversation {
        background-color: #f5f5f5;
        border-radius: 5px;
        padding: 15px;
    }
    
    .wacart-messages {
        max-height: 400px;
        overflow-y: auto;
        margin-bottom: 20px;
    }
    
    .wacart-message {
        margin-bottom: 15px;
        max-width: 80%;
        clear: both;
    }
    
    .wacart-message-incoming {
        float: left;
    }
    
    .wacart-message-outgoing {
        float: right;
    }
    
    .wacart-message-content {
        padding: 10px 15px;
        border-radius: 10px;
        word-wrap: break-word;
    }
    
    .wacart-message-incoming .wacart-message-content {
        background-color: #fff;
        border: 1px solid #ddd;
    }
    
    .wacart-message-outgoing .wacart-message-content {
        background-color: #dcf8c6;
        border: 1px solid #c8e6c9;
    }
    
    .wacart-message-time {
        font-size: 11px;
        color: #999;
        margin-top: 5px;
        text-align: right;
    }
    
    .wacart-message-form {
        clear: both;
        padding-top: 15px;
        border-top: 1px solid #ddd;
    }
</style>
