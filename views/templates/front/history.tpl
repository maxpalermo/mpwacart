{extends file='customer/page.tpl'}

{block name='page_title'}
    {l s='I miei preventivi WhatsApp' mod='mpwacart'}
{/block}

{block name='page_content'}
    <div class="wacart-history">
        {if $requests|count > 0}
            <div class="table-responsive">
                <table class="table table-striped table-bordered">
                    <thead class="thead-default">
                        <tr>
                            <th>{l s='ID' mod='mpwacart'}</th>
                            <th>{l s='Data' mod='mpwacart'}</th>
                            <th>{l s='Prodotti' mod='mpwacart'}</th>
                            <th>{l s='Totale' mod='mpwacart'}</th>
                            <th>{l s='Stato' mod='mpwacart'}</th>
                            <th class="text-center">{l s='Azioni' mod='mpwacart'}</th>
                        </tr>
                    </thead>
                    <tbody>
                        {foreach from=$requests item=request}
                            <tr>
                                <td>#{$request.id_request}</td>
                                <td>{$request.date_add|date_format:"%d/%m/%Y %H:%M"}</td>
                                <td>{l s='Carrello #%s' sprintf=[$request.id_cart] mod='mpwacart'}</td>
                                <td>{$request.total|currency}</td>
                                <td>
                                    <span class="badge badge-{$request.status_class}">
                                        {$request.status_label}
                                    </span>
                                </td>
                                <td class="text-center">
                                    <a href="{$link->getModuleLink('mpwacart', 'detail', ['id_request' => $request.id_request])}" class="btn btn-primary btn-sm">
                                        <i class="material-icons">visibility</i> {l s='Dettagli' mod='mpwacart'}
                                    </a>
                                    <a href="https://wa.me/{$request.phone_number|replace:' ':''|replace:'+':''}" target="_blank" class="btn btn-success btn-sm">
                                        <i class="fab fa-whatsapp"></i> {l s='WhatsApp' mod='mpwacart'}
                                    </a>
                                </td>
                            </tr>
                        {/foreach}
                    </tbody>
                </table>
            </div>
            
            {* Paginazione *}
            {if $pagination.pages > 1}
                <nav class="pagination">
                    <div class="col-md-4">
                        {l s='Mostrando %from%-%to% di %total% elementi' sprintf=['%from%' => $pagination.items_shown_from, '%to%' => $pagination.items_shown_to, '%total%' => $pagination.total_items] mod='mpwacart'}
                    </div>
                    <div class="col-md-8">
                        <ul class="page-list clearfix text-sm-right">
                            {if $prev_page}
                                <li>
                                    <a rel="prev" href="{$link->getModuleLink('mpwacart', 'history', ['page' => $prev_page])}">
                                        <i class="material-icons">keyboard_arrow_left</i> {l s='Precedente' mod='mpwacart'}
                                    </a>
                                </li>
                            {/if}
                            
                            {for $p=1 to $pagination.pages}
                                <li {if $p == $page}class="current"{/if}>
                                    <a href="{$link->getModuleLink('mpwacart', 'history', ['page' => $p])}" {if $p == $page}class="disabled"{/if}>
                                        {$p}
                                    </a>
                                </li>
                            {/for}
                            
                            {if $next_page}
                                <li>
                                    <a rel="next" href="{$link->getModuleLink('mpwacart', 'history', ['page' => $next_page])}">
                                        {l s='Successivo' mod='mpwacart'} <i class="material-icons">keyboard_arrow_right</i>
                                    </a>
                                </li>
                            {/if}
                        </ul>
                    </div>
                </nav>
            {/if}
        {else}
            <div class="alert alert-info">
                {l s='Non hai ancora richiesto preventivi via WhatsApp.' mod='mpwacart'}
            </div>
            
            <div class="text-center">
                <a href="{$link->getPageLink('cart')}" class="btn btn-primary">
                    <i class="material-icons">shopping_cart</i> {l s='Vai al carrello' mod='mpwacart'}
                </a>
            </div>
        {/if}
    </div>
{/block}
