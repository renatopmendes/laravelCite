@component('mail::message')
<h1 style="text-align: center;">Olá {{$user->name}}, você solicitou uma nova senha de acesso.</h1>
<br />
<p>Sua nova senha de acesso é: {{$user->password}}</p>
@endcomponent
