<form id="spid_idp_access" name="spid_idp_access" action="{!! $SPIDActionUrl !!}" method="post">
    <input id="spid_idp_access_provider" type="hidden" name="provider" value="" />
    <a href="#" class="italia-it-button italia-it-button-size-{{ $size ?? 'm' }} button-spid" spid-idp-button="#spid-idp-button-{{ $size ?? 'm' }}-post" aria-haspopup="true" aria-expanded="false">
        <span class="italia-it-button-icon"><img src="{{ asset('/vendor/spid-auth/img/spid-ico-circle-bb.svg') }}" onerror="this.src='{{ asset('/vendor/spid-auth/img/spid-ico-circle-bb.png') }}'; this.onerror=null;" alt="" /></span>
        <span class="italia-it-button-text">Entra con SPID</span>
    </a>
    <div id="spid-idp-button-{{ $size ?? 'm' }}-post" class="spid-idp-button spid-idp-button-tip spid-idp-button-relative{{ $rightAlign ?? false ? ' spid-idp-button-anchor-right' : ''}}">
        <ul id="spid-idp-list-{{ $size ?? 'm' }}-root-post" class="spid-idp-button-menu">
            @unless(config('spid-auth.hide_real_idps'))
            <li class="spid-idp-button-link" data-idp="aruba">
                <button class="idp-button-idp-logo" name="aruba_id" type="submit"><span class="spid-sr-only">Aruba ID</span><img class="spid-idp-button-logo" src="{{ asset('/vendor/spid-auth/img/spid-idp-arubaid.svg') }}" onerror="this.src='{{ asset('/vendor/spid-auth/img/spid-idp-arubaid.png') }}'; this.onerror=null;" alt="Aruba ID" /></button>
            </li>
            <li class="spid-idp-button-link" data-idp="infocert">
                <button class="idp-button-idp-logo" name="infocert_id" type="submit"><span class="spid-sr-only">Infocert ID</span><img class="spid-idp-button-logo" src="{{ asset('/vendor/spid-auth/img/spid-idp-infocertid.svg') }}" onerror="this.src='{{ asset('/vendor/spid-auth/img/spid-idp-infocertid.png') }}'; this.onerror=null;" alt="Infocert ID" /></button>
            </li>
            <li class="spid-idp-button-link" data-idp="intesa">
                <button class="idp-button-idp-logo" name="intesa_id" type="submit"><span class="spid-sr-only">Intesa ID</span><img class="spid-idp-button-logo" src="{{ asset('/vendor/spid-auth/img/spid-idp-intesaid.svg') }}" onerror="this.src='{{ asset('/vendor/spid-auth/img/spid-idp-intesaid.png') }}'; this.onerror=null;" alt="Intesa ID" /></button>
            </li>
            <li class="spid-idp-button-link" data-idp="lepida">
                <button class="idp-button-idp-logo" name="lepida_id" type="submit"><span class="spid-sr-only">Lepida ID</span><img class="spid-idp-button-logo" src="{{ asset('/vendor/spid-auth/img/spid-idp-lepidaid.svg') }}" onerror="this.src='{{ asset('/vendor/spid-auth/img/spid-idp-lepidaid.png') }}'; this.onerror=null;" alt="Lepida ID" /></button>
            </li>
            <li class="spid-idp-button-link" data-idp="namirial">
                <button class="idp-button-idp-logo" name="namirial_id" type="submit"><span class="spid-sr-only">Namirial ID</span><img class="spid-idp-button-logo" src="{{ asset('/vendor/spid-auth/img/spid-idp-namirialid.svg') }}" onerror="this.src='{{ asset('/vendor/spid-auth/img/spid-idp-namirialid.png') }}'; this.onerror=null;" alt="Namirial ID" /></button>
            </li>
            <li class="spid-idp-button-link" data-idp="poste">
                <button class="idp-button-idp-logo" name="poste_id" type="submit"><span class="spid-sr-only">Poste ID</span><img class="spid-idp-button-logo" src="{{ asset('/vendor/spid-auth/img/spid-idp-posteid.svg') }}" onerror="this.src='{{ asset('/vendor/spid-auth/img/spid-idp-posteid.png') }}'; this.onerror=null;" alt="Poste ID" /></button>
            </li>
            <li class="spid-idp-button-link" data-idp="sielte">
                <button class="idp-button-idp-logo" name="sielte_id" type="submit"><span class="spid-sr-only">Sielte ID</span><img class="spid-idp-button-logo" src="{{ asset('/vendor/spid-auth/img/spid-idp-sielteid.svg') }}" onerror="this.src='{{ asset('/vendor/spid-auth/img/spid-idp-sielteid.png') }}'; this.onerror=null;" alt="Sielte ID" /></button>
            </li>
            <li class="spid-idp-button-link" data-idp="spiditalia">
                <button class="idp-button-idp-logo" name="spiditalia" type="submit"><span class="spid-sr-only">SPIDItalia Register.it</span><img class="spid-idp-button-logo" src="{{ asset('/vendor/spid-auth/img/spid-idp-spiditalia.svg') }}" onerror="this.src='{{ asset('/vendor/spid-auth/img/spid-idp-spiditalia.png') }}'; this.onerror=null;" alt="SpidItalia" /></button>
            </li>
            <li class="spid-idp-button-link" data-idp="tim">
                <button class="idp-button-idp-logo" name="tim_id" type="submit"><span class="spid-sr-only">Tim ID</span><img class="spid-idp-button-logo" src="{{ asset('/vendor/spid-auth/img/spid-idp-timid.svg') }}" onerror="this.src='{{ asset('/vendor/spid-auth/img/spid-idp-timid.png') }}'; this.onerror=null;" alt="Tim ID" /></button>
            </li>
            @endunless
            @if (config('spid-auth.test_idp'))
            <li class="spid-idp-button-link" data-idp="test">
                <button class="idp-button-idp-logo" name="test" type="submit"><span class="spid-sr-only">Test IdP</span><img class="spid-idp-button-logo" src="{{ asset('/vendor/spid-auth/img/spid-idp-test.svg') }}" onerror="this.src='{{ asset('/vendor/spid-auth/img/spid-idp-test.png') }}'; this.onerror=null;" alt="Test IdP" /></button>
            </li>
            @endif
            @if (config('spid-auth.validator_idp'))
            <li class="spid-idp-button-link" data-idp="validator">
                <button class="idp-button-idp-logo" name="validator" type="submit"><span class="spid-sr-only">Validator IdP</span><img class="spid-idp-button-logo" src="{{ asset('/vendor/spid-auth/img/spid-validator.svg') }}" onerror="this.src='{{ asset('/vendor/spid-auth/img/spid-validator.png') }}'; this.onerror=null;" alt="Validator" /></button>
            </li>
            @endif
            <li class="spid-idp-support-link" data-spidlink="info">
                <a href="https://www.spid.gov.it">Maggiori informazioni</a>
            </li>
            <li class="spid-idp-support-link" data-spidlink="rich">
                <a href="https://www.spid.gov.it/richiedi-spid">Non hai SPID?</a>
            </li>
            <li class="spid-idp-support-link" data-spidlink="help">
                <a href="https://www.spid.gov.it/serve-aiuto">Serve aiuto?</a>
            </li>
        </ul>
    </div>
</form>
@push('styles')
    <link rel="stylesheet" href="{{ asset('/vendor/spid-auth/css/spid-sp-access-button.min.css') }}">
@endpush
@push('scripts')
    <script type="text/javascript" src="{{ asset('/vendor/spid-auth/js/spid-sp-access-button.min.js') }}"></script>
    <script type="text/javascript">
        jQuery('.spid-idp-button-link').click(function(event) {
            jQuery('#spid_idp_access_provider').val(jQuery(event.currentTarget).data('idp'));
        });

        $(document).ready(function(){
            var rootList = $(".spid-idp-button-menu").first();
            var idpList = rootList.children(".spid-idp-button-link");
            var lnkList = rootList.children(".spid-idp-support-link");
            while (idpList.length) {
                rootList.append(idpList.splice(Math.floor(Math.random() * idpList.length), 1)[0]);
            }
            rootList.append(lnkList);
        });
    </script>
@endpush
