@component('mail::message')
<h1>Demande de réinitialisation du mot de passe</h1>
<p>Vous pouvez utiliser le code suivant pour récupérer votre compte:</p>

@component('mail::panel')
{{ $code }}
@endcomponent

<p>La durée autorisée du code est d'une heure à compter de l'envoi du message.</p>
@endcomponent