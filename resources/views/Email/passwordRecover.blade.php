<x-mail::message>

Emenet Comunicaciones

# Restablecer contrasña

<x-mail::panel>
Hemos recibido una solicitud para restablecer tu contraseña.  
Haz clic en el botón de abajo para crear una nueva contraseña. Este enlace expirará en {{ config('auth.passwords.users.expire', 60) }} minutos.
</x-mail::panel>

<x-mail::button :url="$url">
Restablecer contraseña
</x-mail::button>

Si no has sido tú quien envio la solicitudad, no hagas caso a este correo

<x-mail::subcopy>
Si el botón no funciona, copia y pega la siguiente URL en tu navegador:  
[{{ $url }}]({{ $url }})
</x-mail::subcopy>

<x-mail::footer>
© {{ date('Y') }} EMENET Comunicaciones.  
Si necesitas ayuda, contáctanos al 7131334557.
</x-mail::footer>

Thanks,<br>
{{ config('app.name') }}
</x-mail::message>
