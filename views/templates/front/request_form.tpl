{extends file='page.tpl'}

{block name='page_content'}
<div class="wacart-container">
    <div class="wacart-header">
        <h1>{l s='Richiedi preventivo via WhatsApp' mod='mpwacart'}</h1>
        <p class="wacart-subtitle">{l s='Invia il tuo carrello direttamente su WhatsApp e ricevi un preventivo personalizzato' mod='mpwacart'}</p>
    </div>
    
    {if isset($errors) && $errors}
        <div class="alert alert-danger">
            <ul>
                {foreach from=$errors item=error}
                    <li>{$error}</li>
                {/foreach}
            </ul>
        </div>
    {/if}
    
    <div class="wacart-content">
        <div class="row">
            <div class="col-md-7">
                <div class="wacart-cart-summary">
                    <h2>{l s='Riepilogo carrello' mod='mpwacart'}</h2>
                    
                    <div class="wacart-products">
                        {foreach from=$mpwacart_cart.products item=product}
                            <div class="wacart-product">
                                <div class="wacart-product-img">
                                    {if isset($product.cover)}
                                        <img src="{$product.cover.medium.url}" alt="{$product.name}">
                                    {else}
                                        <img src="{$urls.no_picture_image.bySize.medium_default.url}" alt="{$product.name}">
                                    {/if}
                                </div>
                                <div class="wacart-product-info">
                                    <h3>{$product.name}</h3>
                                    {if isset($product.attributes) && $product.attributes}
                                        <p class="wacart-product-attributes">
                                            {foreach from=$product.attributes key=attribute_name item=attribute_value name=attributes}
                                                {$attribute_name}: {$attribute_value}{if !$smarty.foreach.attributes.last}, {/if}
                                            {/foreach}
                                        </p>
                                    {/if}
                                    <div class="wacart-product-price-qty">
                                        <span class="wacart-product-qty">x{$product.quantity}</span>
                                        <span class="wacart-product-price">{$product.price}</span>
                                    </div>
                                </div>
                            </div>
                        {/foreach}
                    </div>
                    
                    <div class="wacart-totals">
                        <div class="wacart-total-row">
                            <span>{l s='Totale prodotti' mod='mpwacart'}</span>
                            <span>{$mpwacart_cart.totals.total.amount}</span>
                        </div>
                        
                        <div class="wacart-total-row">
                            <span>{l s='Spedizione' mod='mpwacart'}</span>
                            <span><em>{l s='Da definire' mod='mpwacart'}</em></span>
                        </div>
                        
                        {if isset($mpwacart_cart.subtotals.discounts) && $mpwacart_cart.subtotals.discounts.amount > 0}
                            <div class="wacart-total-row">
                                <span>{l s='Sconto' mod='mpwacart'}</span>
                                <span>-{$mpwacart_cart.subtotals.discounts.value}</span>
                            </div>
                        {/if}
                        
                        <div class="wacart-total-row wacart-grand-total">
                            <span>{l s='Totale' mod='mpwacart'}</span>
                            <span>{$mpwacart_cart.totals.total.value}</span>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-md-5">
                <div class="wacart-form-container">
                    <h2>{l s='I tuoi dati' mod='mpwacart'}</h2>
                    
                    <form action="{$link->getModuleLink('mpwacart', 'request', [], true)}" method="post" id="wacart-form">
                        <input type="hidden" name="submitWaCartRequest" value="1">
                        
                        {if !$is_logged_in}
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="customer_firstname">{l s='Nome' mod='mpwacart'}</label>
                                    <div class="input-group">
                                        <span class="input-group-addon">
                                            <i class="material-icons">person</i>
                                        </span>
                                        <input type="text" name="customer_firstname" id="customer_firstname" class="form-control" required>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="customer_lastname">{l s='Cognome' mod='mpwacart'}</label>
                                    <div class="input-group">
                                        <span class="input-group-addon">
                                            <i class="material-icons">person</i>
                                        </span>
                                        <input type="text" name="customer_lastname" id="customer_lastname" class="form-control" required>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="customer_email">{l s='Email' mod='mpwacart'}</label>
                            <div class="input-group">
                                <span class="input-group-addon">
                                    <i class="material-icons">email</i>
                                </span>
                                <input type="email" name="customer_email" id="customer_email" class="form-control" required>
                            </div>
                        </div>
                        {else}
                        <div class="wacart-customer-info">
                            <p><strong>{l s='Nome:' mod='mpwacart'}</strong> {$customer_info.firstname}</p>
                            <p><strong>{l s='Cognome:' mod='mpwacart'}</strong> {$customer_info.lastname}</p>
                            <p><strong>{l s='Email:' mod='mpwacart'}</strong> {$customer_info.email}</p>
                        </div>
                        {/if}
                        
                        <div class="form-group">
                            <label for="phone_number">{l s='Numero di telefono WhatsApp' mod='mpwacart'}</label>
                            <div class="input-group">
                                <span class="input-group-addon">
                                    <i class="material-icons">phone</i>
                                </span>
                                <input type="tel" name="phone_number" id="phone_number" class="form-control" placeholder="+39 XXX XXXXXXX" required>
                            </div>
                            <small class="form-text text-muted">{l s='Inserisci il tuo numero di telefono in formato internazionale (es. +39 XXX XXXXXXX)' mod='mpwacart'}</small>
                        </div>
                        
                        <div class="form-group">
                            <div class="checkbox">
                                <span class="custom-checkbox">
                                    <input type="checkbox" id="privacy_policy" name="privacy_policy" required>
                                    <span><i class="material-icons rtl-no-flip checkbox-checked">&#xE5CA;</i></span>
                                </span>
                                <label for="privacy_policy">
                                    {l s='Ho letto e accetto la' mod='mpwacart'} <a href="{$link->getCMSLink('3')}" target="_blank">{l s='Privacy Policy' mod='mpwacart'}</a>
                                </label>
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <button type="submit" name="submitWaCartRequest" class="btn btn-primary btn-lg btn-block wacart-submit">
                                <i class="fab fa-whatsapp"></i> {l s='Richiedi preventivo via WhatsApp' mod='mpwacart'}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
{/block}
