@extends('layouts.app', ['title' => 'Undangan Digital'])

@push('styles')
@include('styles.form-edit')
@include('profiles.partials.edit.styles')
@endpush

@section('content')

<section class="invitation-edit-grid">
    
    @include('profiles.partials.edit.preview-card')

    <div class="editor-right-column">
        
        @include('profiles.partials.edit.section-switcher')

        <div class="editor-panel-stack" id="editorPanelStack">

            @include('profiles.partials.sections.live-streaming')

            @include('profiles.partials.sections.wedding-gift')

            @include('profiles.partials.sections.wedding-wish')

            @include('profiles.partials.sections.quote')

            @include('profiles.partials.sections.story')

            @include('profiles.partials.sections.gallery')

            @include('profiles.partials.sections.video')

            @include('profiles.partials.sections.save-date')

            @include('profiles.partials.sections.agenda')

            @include('profiles.partials.sections.dresscode')

            @include('profiles.partials.sections.rsvp')

            @include('profiles.partials.sections.cover')

            @include('profiles.partials.sections.couple')

            @include('profiles.partials.sections.footer')

        </div>
    </div>
</section>
@include('profiles.partials.edit.scripts')
@endsection