{extends file='customer/page.tpl'}

{block name='page_title'}
    {l s='Dettagli preventivo WhatsApp' mod='mpwacart'} #{$request->id}
{/block}

{block name='page_content'}
    <div class="wacart-detail">
        <div class="row">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">{l s='Informazioni richiesta' mod='mpwacart'}</h3>
                    </div>
                    <div class="card-body">
                        <div class="wacart-detail-item">
                            <span class="wacart-detail-label">{l s='Data:' mod='mpwacart'}</span>
                            <span class="wacart-detail-value">{$request->date_add|date_format:"%d/%m/%Y %H:%M"}</span>
                        </div>
                        <div class="wacart-detail-item">
                            <span class="wacart-detail-label">{l s='Stato:' mod='mpwacart'}</span>
                            <span class="wacart-detail-value">
                                <span class="badge badge-{$status_class}">{$status_label}</span>
                            </span>
                        </div>
                        <div class="wacart-detail-item">
                            <span class="wacart-detail-label">{l s='Telefono WhatsApp:' mod='mpwacart'}</span>
                            <span class="wacart-detail-value">{$request->phone_number}</span>
                        </div>
                        <div class="wacart-detail-item">
                            <span class="wacart-detail-label">{l s='Totale prodotti:' mod='mpwacart'}</span>
                            <span class="wacart-detail-value">{$request->total_products|currency}</span>
                        </div>
                        <div class="wacart-detail-item">
                            <span class="wacart-detail-label">{l s='Spedizione:' mod='mpwacart'}</span>
                            <span class="wacart-detail-value">{$request->total_shipping|currency}</span>
                        </div>
                        <div class="wacart-detail-item">
                            <span class="wacart-detail-label">{l s='Totale:' mod='mpwacart'}</span>
                            <span class="wacart-detail-value">{$request->total|currency}</span>
                        </div>
                    </div>
                    <div class="card-footer">
                        <a href="{$whatsapp_link}" target="_blank" class="btn btn-success">
                            <i class="fab fa-whatsapp"></i> {l s='Apri WhatsApp' mod='mpwacart'}
                        </a>
                    </div>
                </div>
            </div>
            
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">{l s='Conversazione' mod='mpwacart'}</h3>
                    </div>
                    <div class="card-body">
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
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="card mt-3">
            <div class="card-header">
                <h3 class="card-title">{l s='Prodotti nel carrello' mod='mpwacart'}</h3>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped">
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
                                        <a href="{$link->getProductLink($product.id_product)}">
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
        </div>
        
        <div class="wacart-actions mt-3">
            <a href="{$link->getModuleLink('mpwacart', 'history')}" class="btn btn-secondary">
                <i class="material-icons">arrow_back</i> {l s='Torna alla lista' mod='mpwacart'}
            </a>
        </div>
    </div>
    
    <style type="text/css">
        .wacart-detail-item {
            margin-bottom: 10px;
            padding-bottom: 10px;
            border-bottom: 1px solid #eee;
            display: flex;
            justify-content: space-between;
        }
        
        .wacart-detail-item:last-child {
            border-bottom: none;
            margin-bottom: 0;
            padding-bottom: 0;
        }
        
        .wacart-detail-label {
            font-weight: bold;
            color: #555;
        }
        
        .wacart-conversation {
            max-height: 300px;
            overflow-y: auto;
            padding: 10px;
            background-color: #f9f9f9;
            border-radius: 5px;
        }
        
        .wacart-messages {
            overflow: hidden;
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
            padding: 10px;
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
    </style>
{/block}
