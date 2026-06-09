<div class="panel">
    <h3><i class="icon-tag"></i> {l s='Etiquetas Visuales' mod='productbadges'}</h3>
    <p class="text-muted">{l s='Selecciona las etiquetas que quieres mostrar sobre la imagen de este producto.' mod='productbadges'}</p>
    
    <div class="form-group">
        {if !empty($badges)}
            {foreach from=$badges item=badge}
                <div class="checkbox">
                    <label>
                        <input type="checkbox" name="productbadges[]" value="{$badge.id_productbadges}" {if in_array($badge.id_productbadges, $assigned_badges)}checked="checked"{/if}>
                        <span style="background-color: {$badge.color_bg}; color: {$badge.color_text}; padding: 3px 8px; border-radius: 4px; font-size: 12px; font-weight: bold; margin-left: 5px;">
                            {$badge.text}
                        </span>
                    </label>
                </div>
            {/foreach}
        {else}
            <div class="alert alert-warning">
                {l s='No hay etiquetas creadas o activas. Ve al menú Catálogo > Product Badges para crear la primera.' mod='productbadges'}
            </div>
        {/if}
    </div>
</div>