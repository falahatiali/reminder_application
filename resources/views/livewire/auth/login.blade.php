@section('stylesheets')
    <style>

        .divLogin {
            display: flex;
            align-items: center;
            padding-top: 40px;
            padding-bottom: 40px;
            background-color: #f5f5f5;
        }
        .form-signin {
            width: 100%;
            max-width: 330px;
            padding: 15px;
            margin: auto;
        }

        .form-signin .checkbox {
            font-weight: 400;
        }

        .form-signin .form-floating:focus-within {
            z-index: 2;
        }

        .form-signin input[type="email"] {
            margin-bottom: -1px;
            border-bottom-right-radius: 0;
            border-bottom-left-radius: 0;
        }

        .form-signin input[type="password"] {
            margin-bottom: 10px;
            border-top-left-radius: 0;
            border-top-right-radius: 0;
        }
    </style>
@endsection

<div class="divLogin">
    <main class="form-signin">
        <form wire:submit.prevent="submit" class="d-flex flex-column justify-content-between">
            <h1 class="h5 mb-3 fw-normal text-center">ورود</h1>
            <div class="form-floating">
                <input type="email" wire:model="form.email" class="form-control text-center {{ $errors->has('form.email')?'is-invalid':'' }}" id="floatingEmail" placeholder="name@example.com">
                <label for="floatingEmail" class="text-center">نام کاربری یا ایمیل</label>
                @error('form.email') <span class="text-danger text-center" style="direction: rtl;float: right">{{ $message }}</span> @enderror
            </div>

            <div class="form-floating">
                <input type="password" wire:model="form.password" class="form-control text-center {{ $errors->has('form.password')?'is-invalid':'' }}" id="floatingPassword" placeholder="رمز ورود">
                <label for="floatingPassword" class="text-center">رمز ورود</label>
                @error('form.password') <span class="text-danger text-center" style="direction: rtl;float: right">{{ $message }}</span> @enderror
            </div>

            <button type="submit" class="w-100 mt-5 btn btn-lg btn-primary">ورود </button>
        </form>
    </main>
</div>
