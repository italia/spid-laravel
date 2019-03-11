<div class="agid-spid-enter-button" aria-live="polite" data-size="{{ $size ?? 'm' }}"></div>
@push('scripts')
    <script type="text/javascript" src="{{ asset('/vendor/spid-auth/js/agid-spid-enter.min.latest.js') }}"></script>
    <script type="text/javascript">
      window.agidSpidEnter.init({
          formSubmitMethod: 'POST',
          formActionUrl: '{!! $SPIDActionUrl !!}'
      });
    </script>
@endpush
