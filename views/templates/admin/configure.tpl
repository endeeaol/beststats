<div class="beststats">
    <div class{if isset($bootstrap_version) && $bootstrap_version == 5}"card mt-4"{else}"panel"{/if}>
        
        <div class="card-header">
            <h4 class="card-title"><i class="icon-bar-chart"></i> {l s='Statystyki z dnia' mod='beststats'} {$today_date|escape:'htmlall':'UTF-8'}</h4>
        </div>
        <div class="card-body">
            <table class="table table-bordered table-striped">
                <tbody>
                    <tr>
                        <td><i class="material-icons mi-shopping_basket">shopping_basket</i> {l s='Dzisiejsza wartość zamówień' mod='beststats'}</td>
                        <td class="text-end fw-bold">{$stats_today.revenue}</td>
                    </tr>
                    <tr>
                        <td><i class="material-icons mi-assessment">assessment</i> {l s='Ilość dzisiejszych zamówień' mod='beststats'}</td>
                        <td class="text-end fw-bold">{$stats_today.order_count}</td>
                    </tr>
                    <tr>
                        <td><i class="material-icons mi-trending_up">trending_up</i> {l s='Średnia wartość zamówienia' mod='beststats'}</td>
                        <td class="text-end fw-bold">{$stats_today.average}</td>
                    </tr>
                    <tr>
                        <td><i class="material-icons mi-local_offer">local_offer</i> {l s='Dziś udzielono rabatów na kwotę' mod='beststats'}</td>
                        <td class="text-end fw-bold">{$stats_today.discounts}</td>
                    </tr>
                    {* NOWY WIERSZ *}
                    <tr>
                        <td><i class="material-icons mi-cancel">cancel</i> {l s='Wartość zamówień anulowanych' mod='beststats'}</td>
                        <td class="text-end fw-bold">{$stats_today.cancelled_revenue}</td>
                    </tr>
                </tbody>
            </table>
        </div>

        <div class="card-header mt-4">
            <h4 class="card-title"><i class="icon-bar-chart"></i> {l s='Statystyki z dnia' mod='beststats'} {$yesterday_date|escape:'htmlall':'UTF-8'}</h4>
        </div>
        <div class="card-body">
            <table class="table table-bordered table-striped">
                <tbody>
                    <tr>
                        <td><i class="material-icons mi-shopping_basket">shopping_basket</i> {l s='Wczorajsza wartość zamówień' mod='beststats'}</td>
                        <td class="text-end fw-bold">{$stats_yesterday.revenue}</td>
                    </tr>
                    <tr>
                        <td><i class="material-icons mi-assessment">assessment</i> {l s='Ilość wczorajszych zamówień' mod='beststats'}</td>
                        <td class="text-end fw-bold">{$stats_yesterday.order_count}</td>
                    </tr>
                    <tr>
                        <td><i class="material-icons mi-trending_up">trending_up</i> {l s='Średnia wartość zamówienia (wczoraj)' mod='beststats'}</td>
                        <td class="text-end fw-bold">{$stats_yesterday.average}</td>
                    </tr>
                    <tr>
                        <td><i class="material-icons mi-local_offer">local_offer</i> {l s='Wczoraj udzielono rabatów na kwotę' mod='beststats'}</td>
                        <td class="text-end fw-bold">{$stats_yesterday.discounts}</td>
                    </tr>
                    {* NOWY WIERSZ *}
                    <tr>
                        <td><i class="material-icons mi-cancel">cancel</i> {l s='Wartość zamówień anulowanych' mod='beststats'}</td>
                        <td class="text-end fw-bold">{$stats_yesterday.cancelled_revenue}</td>
                    </tr>
                </tbody>
            </table>
        </div>

        <div class="card-header mt-4">
            <h4 class="card-title"><i class="icon-bar-chart"></i> {l s='Statystyki z bieżącego miesiąca' mod='beststats'}</h4>
        </div>
        <div class="card-body">
            <table class="table table-bordered table-striped">
                <tbody>
                    <tr>
                        <td><i class="material-icons mi-shopping_basket">shopping_basket</i> {l s='Wartość zamówień w tym miesiącu' mod='beststats'}</td>
                        <td class="text-end fw-bold">{$stats_month.revenue}</td>
                    </tr>
                    <tr>
                        <td><i class="material-icons mi-assessment">assessment</i> {l s='Ilość zamówień w tym miesiącu' mod='beststats'}</td>
                        <td class="text-end fw-bold">{$stats_month.order_count}</td>
                    </tr>
                    <tr>
                        <td><i class="material-icons mi-trending_up">trending_up</i> {l s='Średnia wartość zamówienia (miesiąc)' mod='beststats'}</td>
                        <td class="text-end fw-bold">{$stats_month.average}</td>
                    </tr>
                    <tr>
                        <td><i class="material-icons mi-local_offer">local_offer</i> {l s='Udzielono rabatów w tym miesiącu na kwotę' mod='beststats'}</td>
                        <td class="text-end fw-bold">{$stats_month.discounts}</td>
                    </tr>
                    {* NOWY WIERSZ *}
                    <tr>
                        <td><i class="material-icons mi-cancel">cancel</i> {l s='Wartość zamówień anulowanych' mod='beststats'}</td>
                        <td class="text-end fw-bold">{$stats_month.cancelled_revenue}</td>
                    </tr>
                </tbody>
            </table>
        </div>

        {if !empty($top_customers)}
            <div class="card-header mt-4">
                <h4 class="card-title"><i class="material-icons mi-emoji_events">emoji_events</i> {l s='Bohaterami miesiąca są:' mod='beststats'}</h4>
            </div>
            <div class="card-body">
                <table class="table table-bordered table-striped ">
                    <thead>
                        <tr>
                            <th>{l s='Klient' mod='beststats'}</th>
                            <th class="text-end">{l s='Suma wydatków' mod='beststats'}</th>
                        </tr>
                    </thead>
                    <tbody>
                        {foreach from=$top_customers item=customer name=customer_loop}
                            <tr {if $smarty.foreach.customer_loop.iteration <= 3}class="top-customer"{/if}>
                                <td>{$customer.firstname|escape:'htmlall':'UTF-8'} {$customer.lastname|escape:'htmlall':'UTF-8'}</td>
                                <td class="text-end">{$customer.total_spent_formatted}</td>
                            </tr>
                        {/foreach}
                    </tbody>
                </table>
            </div>
        {/if}
    </div>
</div>