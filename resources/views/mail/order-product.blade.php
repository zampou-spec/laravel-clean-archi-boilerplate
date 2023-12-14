@component('mail::message')
<h1>Vous avez une nouvelle commande</h1>
<p>Information du client:</p>

@component('mail::panel')
<p style="margin: 0;"><b>Nom complet:</b> {{ $order['name'] }}</p>
<p style="margin: 0;"><b>Pays:</b> {{ $order['country'] }} </p>
<p style="margin: 0;"><b>Numéro de téléphone:</b> {{ $order['mobile_number'] }}</p>
@endcomponent

<p>Information produit:</p>
@component('mail::panel')
<p style="margin: 0;"><b>Prix:</b> {{ $order['product']['price'] }} </p>
<p style="margin: 0;"><b>Nom du produit:</b> {{ $order['product']['name'] }} </p>
@endcomponent
@endcomponent