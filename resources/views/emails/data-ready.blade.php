@component('mail::message')
# Data Ready

Hi {{ $user->name }},

Your data is ready for you. Click below to start the download.

@component('mail::button', ['url' => $file->getDownloadUrl()])
Download Data
@endcomponent

Thanks,<br>
{{ config('app.name') }}
@endcomponent
