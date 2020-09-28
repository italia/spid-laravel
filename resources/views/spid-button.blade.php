<form class="spid-form" id="spid_idp_access{{ isset($button_id) ? ('_' . $button_id) : '' }}" name="spid_idp_access"
      action="{!! $SPIDActionUrl !!}" method="post">
    <input id="spid_idp_access_provider{{ isset($button_id) ? ('_' . $button_id) : '' }}" type="hidden" name="provider"
           class="spid-btn" value="" />
    <a href="#" class="italia-it-button italia-it-button-size-{{ $size ?? 'm' }} button-spid"
       spid-idp-button="#spid-idp-button-{{ $size ?? 'm' }}-post" aria-haspopup="true" aria-expanded="false">
        <span class="italia-it-button-icon">
            <img src="{{ asset('/vendor/spid-auth/img/spid-ico-circle-bb.svg') }}"
                 onerror="this.src='{{ asset('/vendor/spid-auth/img/spid-ico-circle-bb.png') }}'; this.onerror=null;"
                 alt="" />
        </span>
        <span class="italia-it-button-text">Entra con SPID</span>
    </a>
    <div class="spid-menu-container">
        <div id="spid-idp-button-{{ $size ?? 'm' }}-post{{ isset($button_id) ? ('_' . $button_id) : '' }}"
             class="spid-idp-button spid-idp-button-tip spid-idp-button-relative{{ $rightAlign ?? false ? ' spid-idp-button-anchor-right' : ''}}">
            <ul id="spid-idp-list-{{ $size ?? 'm' }}-root-post{{ isset($button_id) ? ('_' . $button_id) : '' }}"
                class="spid-idp-button-menu">
                @unless(config('spid-auth.hide_real_idps'))
                    @foreach(collect(Config::get('spid-auth.idps'))->map(function($idp){ return (object) $idp; })->shuffle()->all() as $idp)
                        <li class="spid-idp-button-link" data-idp="{{ $idp->idp }}">
                            <button class="idp-button-idp-logo" name="{{ $idp->name }}" type="submit">
                                <span class="spid-sr-only">{{ $idp->description }}</span>
                                <img class="spid-idp-button-logo" src="{{ asset($idp->svg_logo) }}"
                                     onerror="this.src='{{ asset($idp->png_logo) }}'; this.onerror=null;"
                                     alt="{{ $idp->description }}" />
                            </button>
                        </li>
                    @endforeach
                @endunless
                @if (config('spid-auth.test_idp'))
                    <li class="spid-idp-button-link" data-idp="test">
                        <button class="idp-button-idp-logo" name="test" type="submit">
                            <span class="spid-sr-only">Test IdP</span>
                            <img class="spid-idp-button-logo"
                                 src="{{ asset('/vendor/spid-auth/img/spid-idp-test.svg') }}"
                                 onerror="this.src='{{ asset('/vendor/spid-auth/img/spid-idp-test.png') }}'; this.onerror=null;"
                                 alt="Test IdP" />
                        </button>
                    </li>
                @endif
                @if (config('spid-auth.validator_idp'))
                    <li class="spid-idp-button-link" data-idp="validator">
                        <button class="idp-button-idp-logo" name="validator" type="submit">
                            <span class="spid-sr-only">Validator IdP</span>
                            <img class="spid-idp-button-logo"
                                 src="{{ asset('/vendor/spid-auth/img/spid-validator.svg') }}"
                                 onerror="this.src='{{ asset('/vendor/spid-auth/img/spid-validator.png') }}'; this.onerror=null;"
                                 alt="Validator" />
                        </button>
                    </li>
                @endif
            </ul>
            <ul id="spid-idp-footer" class="spid-idp-button-menu">
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
    </div>
</form>
@push('styles')
    <link rel="stylesheet" href="{{ asset('/vendor/spid-auth/css/spid-sp-access-button.min.css') }}">
    <style>
        .spid-menu-container {
            width: max-content;
        }

        .spid-idp-button {
            width: inherit;
            margin: fill;
            z-index: 998 !important;
        }

        .spid-idp-button-menu {
            overflow-y: scroll !important;
            max-height: 250px;
        }

        #spid-idp-footer {
            overflow-y: hidden !important;
        }

    </style>
@endpush

@push('scripts')
    <script type="text/javascript" src="{{ asset('/vendor/spid-auth/js/spid-sp-access-button.min.js') }}"></script>
@endpush

@once
@push('scripts')
    <script type="text/javascript">
        document.querySelectorAll('.spid-idp-button-link').forEach(spid_link => {
            spid_link.addEventListener('click', e => {
                let spid_form = spid_link.closest('form');
                let spid_button = spid_form.querySelector('[id^="spid_idp_access_provider"]');
                spid_button.value = spid_link.getAttribute('data-idp');
            })
        });
    </script>
@endpush
@endonce

