<div class="panel">
    <div class="panel-heading">
        <i class="icon-whatsapp"></i> {l s='Richieste preventivo WhatsApp' mod='mpwacart'}
    </div>
    
    {if $requests|count > 0}
        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th>{l s='ID' mod='mpwacart'}</th>
                        <th>{l s='Data' mod='mpwacart'}</th>
                        <th>{l s='Telefono' mod='mpwacart'}</th>
                        <th>{l s='Stato' mod='mpwacart'}</th>
                        <th class="text-right">{l s='Totale' mod='mpwacart'}</th>
                        <th class="text-right">{l s='Azioni' mod='mpwacart'}</th>
                    </tr>
                </thead>
                <tbody>
                    {foreach from=$requests item=request}
                        <tr>
                            <td>#{$request->id}</td>
                            <td>{$request->date_add|date_format:"%d/%m/%Y %H:%M"}</td>
                            <td>{$request->phone_number}</td>
                            <td>
                                {if $request->status == 'pending'}
                                    <span class="badge badge-warning">{l s='In attesa' mod='mpwacart'}</span>
                                {elseif $request->status == 'sent'}
                                    <span class="badge badge-info">{l s='Inviato' mod='mpwacart'}</span>
                                {elseif $request->status == 'replied'}
                                    <span class="badge badge-primary">{l s='Risposto' mod='mpwacart'}</span>
                                {elseif $request->status == 'completed'}
                                    <span class="badge badge-success">{l s='Completato' mod='mpwacart'}</span>
                                {elseif $request->status == 'cancelled'}
                                    <span class="badge badge-danger">{l s='Annullato' mod='mpwacart'}</span>
                                {/if}
                            </td>
                            <td class="text-right">{$request->total|currency}</td>
                            <td class="text-right">
                                <a href="{$mpwacart_admin_link}&id_request={$request->id}&viewmpwacart_request" class="btn btn-default">
                                    <i class="icon-eye"></i> {l s='Visualizza' mod='mpwacart'}
                                </a>
                            </td>
                        </tr>
                    {/foreach}
                </tbody>
            </table>
        </div>
    {else}
        <div class="alert alert-info">
            {l s='Nessuna richiesta di preventivo WhatsApp associata a questo ordine.' mod='mpwacart'}
        </div>
    {/if}
</div>
