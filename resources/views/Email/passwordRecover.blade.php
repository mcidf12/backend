<x-mail::message>
# Cambio de contraseña

Click en el boton para cambiar la contraseña

<x-mail::button :url="'http://localhost:4200/response-password?token=' . $token">
Reset Password
</x-mail::button>

Thanks,<br>
{{ config('app.name') }}
</x-mail::message>
