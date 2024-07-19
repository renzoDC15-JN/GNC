<div>
    <x-filament::input.wrapper>
        <x-filament::input.select wire:model="document">
            @foreach($documents as $doc)
                <option value="{{$doc->id}}">{{$doc->name}}</option>
            @endforeach
{{--            <option value="draft">Draft</option>--}}
{{--            <option value="reviewing">Reviewing</option>--}}
{{--            <option value="published">Published</option>--}}
        </x-filament::input.select>
    </x-filament::input.wrapper>
{{--    {{$record}}--}}
{{--    {{$documents}}--}}
</div>
