@component('mail::message')
<h1 style="text-align: center;">Olá {{$user->name}}, você se cadastrou no Cite!</h1>
<h2 style="text-align: center;">Comece agora mesmo a criar e compartilhar seus próprios conteúdos!</h2>
<br />
<p style="font-size: 10px;">Caso não tenha sido você, por favor, <a href="https://cite-dccb7.rj.r.appspot.com/destroy/{{$user->remember_token}}">exclua esta conta</a>.</p>
@endcomponent
