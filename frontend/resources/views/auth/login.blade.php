<x-layout bodyClass="bg-gray-200">
    <main class="main-content mt-0">
        <div class="page-header align-items-start min-vh-100"
            style="background-image: url('https://images.unsplash.com/photo-1497294815431-9365093b7331?ixlib=rb-1.2.1&ixid=MnwxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8&auto=format&fit=crop&w=1950&q=80');">
            <span class="mask bg-gradient-dark opacity-6"></span>
            <div class="container mt-5">
                <div class="row signin-margin">
                    <div class="col-lg-4 col-md-8 col-12 mx-auto">
                        <div class="card z-index-0 fadeIn3 fadeInBottom">
                            <div class="card-header p-0 position-relative mt-n4 mx-3 z-index-2">
                                <div class="bg-gradient-primary shadow-primary border-radius-lg py-3 pe-1">
                                    <h4 class="text-white font-weight-bolder text-center mt-2 mb-0">Sign In</h4>
                                    <p class="text-white text-center text-sm mb-2">Sistem Inventaris Laboratorium</p>
                                </div>
                            </div>
                            <div class="card-body">
                                <form role="form" method="POST" action="{{ route('login.post') }}" class="text-start">
                                    @csrf

                                    @if (session('success'))
                                    <div class="alert alert-success alert-dismissible text-white" role="alert">
                                        <span class="text-sm">{{ session('success') }}</span>
                                        <button type="button" class="btn-close text-lg py-3 opacity-10"
                                            data-bs-dismiss="alert" aria-label="Close">
                                            <span aria-hidden="true">&times;</span>
                                        </button>
                                    </div>
                                    @endif

                                    @if (session('error'))
                                    <div class="alert alert-danger alert-dismissible text-white" role="alert">
                                        <span class="text-sm">{{ session('error') }}</span>
                                        <button type="button" class="btn-close text-lg py-3 opacity-10"
                                            data-bs-dismiss="alert" aria-label="Close">
                                            <span aria-hidden="true">&times;</span>
                                        </button>
                                    </div>
                                    @endif

                                    <div class="input-group input-group-outline mt-3">
                                        <label class="form-label">Email</label>
                                        <input type="email" class="form-control" name="email" id="email"
                                            value="{{ old('email') }}">
                                    </div>
                                    @error('email')
                                    <p class='text-danger inputerror'>{{ $message }}</p>
                                    @enderror

                                    <div class="input-group input-group-outline mt-3">
                                        <label class="form-label">Password</label>
                                        <input type="password" class="form-control" name="password" id="password">
                                    </div>
                                    @error('password')
                                    <p class='text-danger inputerror'>{{ $message }}</p>
                                    @enderror

                                    <div class="text-center">
                                        <button type="submit" class="btn bg-gradient-primary w-100 my-4 mb-2">
                                            Sign In
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>

    @push('js')
    <script src="{{ asset('assets') }}/js/jquery.min.js"></script>
    <script>
        $(function() {
            $(".input-group input").each(function() {
                if ($(this).val() !== "") {
                    $(this).closest('.input-group').addClass('is-filled');
                }
            });
            $(".input-group input").on('focus', function() {
                $(this).closest('.input-group').addClass('is-filled');
            }).on('blur', function() {
                if ($(this).val() === "") {
                    $(this).closest('.input-group').removeClass('is-filled');
                }
            });
        });
    </script>
    @endpush
</x-layout>
