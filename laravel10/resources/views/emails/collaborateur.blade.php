@component('mail::message')
# Bonjour {{ $collab->nom_collab }},

Vous avez été ajouté en tant que **collaborateur** sur un projet dans la plateforme **AcadProManage**.

Merci de consulter votre compte pour voir les détails du projet.

@component('mail::button', ['url' => env('APP_URL')])
Voir le projet
@endcomponent

Merci,<br>
{{ config('app.name') }}
@endcomponent
