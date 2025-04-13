{extends file='page.tpl'}

{block name='page_content'}
<div class="wacart-container">
    <div class="wacart-confirmation">
        <div class="wacart-confirmation-header">
            <div class="wacart-icon-container">
                <i class="fab fa-whatsapp wacart-icon"></i>
                <i class="material-icons wacart-check">check_circle</i>
            </div>
            <h1>{l s='Richiesta inviata con successo!' mod='mpwacart'}</h1>
            <p class="wacart-subtitle">{l s='Il tuo preventivo è stato inviato via WhatsApp' mod='mpwacart'}</p>
        </div>
        
        <div class="wacart-confirmation-content">
            <div class="row">
                <div class="col-md-6">
                    <div class="wacart-confirmation-details">
                        <h3>{l s='Dettagli della richiesta' mod='mpwacart'}</h3>
                        
                        <div class="wacart-detail-item">
                            <span class="wacart-detail-label">{l s='Numero richiesta:' mod='mpwacart'}</span>
                            <span class="wacart-detail-value">{$request->id}</span>
                        </div>
                        
                        <div class="wacart-detail-item">
                            <span class="wacart-detail-label">{l s='Data:' mod='mpwacart'}</span>
                            <span class="wacart-detail-value">{$request->date_add|date_format:"%d/%m/%Y %H:%M"}</span>
                        </div>
                        
                        <div class="wacart-detail-item">
                            <span class="wacart-detail-label">{l s='Prodotti nel carrello:' mod='mpwacart'}</span>
                            <span class="wacart-detail-value">{count($products)}</span>
                        </div>
                        
                        <div class="wacart-detail-item">
                            <span class="wacart-detail-label">{l s='Totale:' mod='mpwacart'}</span>
                            <span class="wacart-detail-value">{Tools::displayPrice($cart_total)}</span>
                        </div>
                        
                        <div class="wacart-detail-item">
                            <span class="wacart-detail-label">{l s='Numero WhatsApp:' mod='mpwacart'}</span>
                            <span class="wacart-detail-value">{$customer_phone}</span>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-6">
                    <div class="wacart-next-steps">
                        <h3>{l s='Prossimi passi' mod='mpwacart'}</h3>
                        
                        <ol class="wacart-steps-list">
                            <li>
                                <i class="material-icons">smartphone</i>
                                <div>
                                    <h4>{l s='Controlla WhatsApp' mod='mpwacart'}</h4>
                                    <p>{l s='Hai ricevuto un messaggio di conferma sul tuo numero WhatsApp.' mod='mpwacart'}</p>
                                </div>
                            </li>
                            <li>
                                <i class="material-icons">schedule</i>
                                <div>
                                    <h4>{l s='Attendi il preventivo' mod='mpwacart'}</h4>
                                    <p>{l s='Il nostro team esaminerà la tua richiesta e ti invierà un preventivo personalizzato.' mod='mpwacart'}</p>
                                </div>
                            </li>
                            <li>
                                <i class="material-icons">chat</i>
                                <div>
                                    <h4>{l s='Conferma l\'ordine' mod='mpwacart'}</h4>
                                    <p>{l s='Una volta ricevuto il preventivo, potrai confermare l\'ordine direttamente via WhatsApp.' mod='mpwacart'}</p>
                                </div>
                            </li>
                        </ol>
                        
                        {if $use_direct_link}
                        <div class="wacart-whatsapp-buttons">
                            <div class="alert alert-info wacart-redirect-info">
                                <i class="material-icons">info</i> 
                                {l s='Stai per essere reindirizzato a WhatsApp. Quando si aprirà la pagina, premi "Invia" per completare l\'invio del messaggio.' mod='mpwacart'}
                            </div>
                            <div id="countdown-container-owner">
                                <p>{l s='Reindirizzamento in' mod='mpwacart'} <span id="countdown-owner">5</span> {l s='secondi' mod='mpwacart'}...</p>
                                <div class="progress">
                                    <div class="progress-bar bg-success" role="progressbar" style="width: 100%" aria-valuenow="100" aria-valuemin="0" aria-valuemax="100" id="progress-bar-owner"></div>
                                </div>
                            </div>
                            <a href="{$wa_link_owner}" target="_blank" id="whatsapp-link-owner" class="btn btn-success btn-lg">
                                <i class="fab fa-whatsapp"></i> {l s='Apri WhatsApp ora' mod='mpwacart'}
                            </a>
                            <script type="text/javascript">
                                document.addEventListener('DOMContentLoaded', function() {
                                    var countdown = 5;
                                    var countdownElement = document.getElementById('countdown-owner');
                                    var progressBar = document.getElementById('progress-bar-owner');
                                    var whatsappLink = document.getElementById('whatsapp-link-owner');
                                    var whatsappUrl = '{$wa_link_owner}';
                                    
                                    var timer = setInterval(function() {
                                        countdown--;
                                        countdownElement.textContent = countdown;
                                        progressBar.style.width = (countdown / 5 * 100) + '%';
                                        
                                        if (countdown <= 0) {
                                            clearInterval(timer);
                                            window.open(whatsappUrl, '_blank');
                                            document.getElementById('countdown-container-owner').innerHTML = '<p class="text-success">{l s="WhatsApp è stato aperto in una nuova finestra." mod="mpwacart"}</p>';
                                        }
                                    }, 1000);
                                });
                            </script>
                        </div>
                        {else}
                        <div class="wacart-whatsapp-button">
                            <div class="alert alert-success wacart-success-info">
                                <i class="material-icons">check_circle</i> 
                                {l s='Il messaggio è stato inviato automaticamente al titolare. Riceverai presto una risposta.' mod='mpwacart'}
                            </div>
                        </div>
                        {/if}
                    </div>
                </div>
            </div>
        </div>
        
        <div class="wacart-confirmation-footer">
            <a href="{$urls.pages.index}" class="btn btn-outline-primary">
                <i class="material-icons">arrow_back</i> {l s='Torna alla home' mod='mpwacart'}
            </a>
        </div>
    </div>
</div>
{/block}
