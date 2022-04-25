{{-- <form action="/submit-contact-form" method="post" id="contactForm">
    @csrf
    <div class="fieldlist">
        <div><label for="form-name">Naam *</label><br /><input type="text" id="form-name" name="Naam" value="{{ old('Naam') }}"></div>
        <div><label for="form-phone">Telefoon</label><br /><input type="text" id="form-phone" name="Telefoon" value="{{ old('Telefoon') }}"></div>
        <div><label for="form-email">Email Adres *</label><br /><input type="text" id="form-email" name="Emailadres" value="{{ old('Emailadres') }}"></div>
        <div><label for="form-company">Bedrijfsnaam</label><br /><input type="text" id="form-company" name="Bedrijfsnaam" value="{{ old('Bedrijfsnaam') }}"></div>    
    </div>
    <div><label for="form-message">Bericht *</label><br /><textarea id="form-message" name="Bericht" rows="6" cols="20">{{ old('Bericht') }}</textarea></div>
    <div><input type="checkbox" id="form-receive-updates" name="AanmeldenNieuwsbrief" value="Ja"{{ old('AanmeldenNieuwsbrief') == 'Ja' ? ' checked' : '' }}><label for="form-receive-updates">Ik meld mij aan voor de nieuwsbrief</label></div>
    <div><button type="submit"><span>Verzenden</span></button></div>
</form>
<hr /> --}}
<form action="/submit-contact-form" method="post" id="contactForm">
    {{-- <h2>Contactformulier</h2> --}}
    @csrf
    <div class="fieldlist">
        <div @error('Naam')class="error" data-err-msg="{{ $message }}"@enderror><label for="form-name">Naam *</label><br /><input type="text" id="form-name" name="Naam" value="{{ old('Naam') }}"></div>
        <div><label for="form-phone">Telefoon</label><br /><input type="text" id="form-phone" name="Telefoon" value="{{ old('Telefoon') }}"></div>
        <div @error('E-mail_adres')class="error" data-err-msg="{{ $message }}"@enderror><label for="form-email">Email Adres *</label><br /><input type="text" id="form-email" name="E-mail_adres" value="{{ old('E-mail_adres') }}"></div>
        <div><label for="form-company">Bedrijfsnaam</label><br /><input type="text" id="form-company" name="Bedrijfsnaam" value="{{ old('Bedrijfsnaam') }}"></div>    
    </div>
    <div @error('Bericht')class="error" data-err-msg="{{ $message }}"@enderror><label for="form-message">Bericht *</label><br /><textarea id="form-message" name="Bericht" rows="6" cols="20">{{ old('Bericht') }}</textarea></div>
    {{-- <div><input type="checkbox" id="form-receive-updates" name="Aanmelden_nieuwsbrief" value="Ja"{{ old('Aanmelden_nieuwsbrief') == 'Ja' ? ' checked' : '' }}><label for="form-receive-updates">Ik meld mij aan voor de nieuwsbrief</label></div> --}}
    <div><button type="submit" class="g-recaptcha" data-sitekey="6Lc2cW4dAAAAAO8T8vgu1eBlYyBqYp3Ci4E9s40P" data-callback="onSubmit" data-action="submit"><span>Verzenden</span></button></div>
</form>
