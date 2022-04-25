@extends('templates.vincent')
@section('content')
    @include('snippets.contentSections')
    @include('snippets.contact-form')
    {{-- @include('snippets.route') --}}
@endsection
@section('after_body_tag')
    <script src="https://www.google.com/recaptcha/api.js"></script>
    <script>
        function onSubmit(token) {
            document.getElementById("contactForm").submit();
        }
    </script>
    @if(session('success'))
        <div class="alert alert-success">
            {{-- <div><p class="thumbsUpIcon"></p></div> --}}
            {{-- <div><p>{{ $data['website_options']['form_success_message'] }}</p></div> --}}
            <div><p>Bedankt voor uw bericht! We nemen zo spoedig mogelijk contact op.</p></div>
        </div>
    @endif
    @if($errors->any())
        <div class="alert alert-danger">
            {{-- <div><p class="exclamationTriangleIcon"></i></div> --}}
            {{-- <div><p>{{ $data['website_options']['form_error_message'] }}</p></div> --}}
            <div><p>Het formulier kon niet verzonden worden, controleer op fouten. Er is geen bericht verzonden.</p></div>
        </div>
    @endif
@endsection
@section('before_closing_body_tag')
    @if($errors->any())
    <script>
        const errors = document.querySelectorAll('.error');
        errors.forEach((el) => {
            const err = document.createElement('span');
            err.classList.add('errMsg');
            err.innerHTML = el.dataset.errMsg;
            el.appendChild(err);
        });
    </script>
    @endif
@endsection
