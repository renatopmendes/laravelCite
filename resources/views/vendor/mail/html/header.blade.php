<tr>
<td class="header">
@if (trim($slot) === 'Cite')
<img src="https://storage.googleapis.com/cite-pages/cite-mail.png" class="logo" alt="">
@else
{{ $slot }}
@endif
</td>
</tr>
