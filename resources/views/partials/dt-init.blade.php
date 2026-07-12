{{--
    Shared DataTable initializer — solves Vite module deferred execution race.

    Usage:
      @include('partials.dt-init', [
          'tableId' => 'tbl-foo',
          'config'  => "{ order: [[0,'asc']] }",   // raw JS object literal
          'extra'   => "dt.column(0).search('').draw();",  // runs after init, 'dt' is the instance
      ])
--}}
<script>
(function () {
    function initTable() {
        if (typeof window.DataTable === 'undefined') return;
        var dt = new window.DataTable(
            '#{{ $tableId }}',
            {!! $config ?? '{}' !!}
        );
        // store on window for console debugging
        window['dt_{{ Str::camel($tableId) }}'] = dt;
        @isset($extra)
        (function(dt) {
            {!! $extra !!}
        })(dt);
        @endisset
    }

    if (typeof window.DataTable !== 'undefined') {
        // Module already loaded (e.g. second navigation, cached)
        initTable();
    } else {
        document.addEventListener('datatables:ready', initTable, { once: true });
    }
})();
</script>
