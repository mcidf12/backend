<x-mail::message>
Emenet Comunicaciones

Restablecer contrasña


Hemos recibido una solicitud para restablecer tu contraseña, haz click en el siguiente boton para crear una nueva contraseña

<x-mail::button :url="$url">
Restablecer contraseña
</x-mail::button>

Si no has sido tú quien envio la solicitudad, no hagas caso a este correo

Thanks,<br>
{{ config('app.name') }}
</x-mail::message>
