<div class="agid-spid-enter-button" aria-live="polite" data-size="{{ $size or 'm' }}"></div>
@push('scripts')
    <!-- Promise polyfill -->
    <script src="https://cdn.jsdelivr.net/npm/es6-promise@4/dist/es6-promise.auto.min.js"></script>
    <script type="text/javascript" src="{{ asset('/vendor/spid-auth/js/agid-spid-enter.min.latest.js') }}"></script>
    <script type="text/javascript">
      window.agidSpidEnter.init({
          formSubmitMethod: 'POST',
          formActionUrl: '{!! $SPIDActionUrl !!}'
      });
    </script>
@endpush
