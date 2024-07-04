<div>
    {{-- Nothing in the world is as soft and yielding as water. --}}
{{--    {{$record}}--}}

    @if($record)
{{--        <button wire:click="refreshPdf">Refresh PDF</button>--}}

        <div wire:ignore>
            <iframe id="pdfIframe" src="{{ route('document.stream', ['id' => $record->id??0]) }}" width="100%" height="600px"></iframe>
        </div>
    @endif


</div>

{{--<script>--}}
{{--    document.addEventListener('livewire:load', function () {--}}
{{--        Livewire.on('reloadPdfIframe', function () {--}}
{{--            var iframe = document.getElementById('pdfIframe');--}}
{{--            iframe.src = iframe.src;--}}
{{--        });--}}
{{--    });--}}
{{--</script>--}}
