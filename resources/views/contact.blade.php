@extends('layouts.app')

@section('content')
    <section class="innerhead smallhead">
        <div class="container-fluid">
            <div class="container">
                <div class="row">
                    <div class="col-md-12 col-sm-12 col-xs-12 bannercont animate text-center" data-anim-type="zoomIn" data-anim-delay="800">
                        <div class="center">
                            <h1>Contact Us</h1>
                        </div>
                        <div class="breadcrum-sitemap">
                            <ul>
                                <li><a href="/">Home</a></li>
                                <li><a href="javascript:void(0);">Contact Us</a></li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="contform innerlayout">
        <div class="container">
            <div class="row">
                <div class="col-md-12 col-sm-12 col-xs-12 inbox">
                    <div class="col-md-7 col-sm-7 col-xs-12 formlft animate" data-anim-type="fadeInLeft" data-anim-delay="800">
                        <form action="{{ route('post.contact') }}" id="contact-form" method="post">
                            @csrf
                            <div class="form-group">
                                <label for="name">Name</label>
                                <input type="text" class="form-control" id="name" name="name" required="required" 
                                       minlength="3" maxlength="50" placeholder="Enter your name">
                                
                                <script>
                                    document.getElementById('name').addEventListener('input', function() {
                                        const nameInput = this.value;
                                        const numberPattern = /\d/; // Regular expression to detect digits
                                
                                        // Check if the input contains any digits
                                        if (numberPattern.test(nameInput)) {
                                            alert("Please enter a valid name without numbers.");
                                            this.value = nameInput.replace(/\d/g, ''); // Remove any numbers
                                        }
                                    });
                                </script>
                            </div>
                            
                            <div class="form-group">
                                <label for="email">Email</label>
                                <input type="email" class="form-control" id="email" name="email" 
                                       required="required"  placeholder="Enter Your Email here"
                                       pattern="[a-z0-9._%+-]+@[a-z0-9.-]+\.[a-z]{2,3}$" 
                                       title="Please enter a valid email address ending with .com">
                            </div>
                            
                            <div class="form-group">
                                <label for="message">Message</label>
                                <textarea class="form-control" id="message" name="message" required 
                                          minlength="10" maxlength="500" placeholder="Enter your message"></textarea>
                            </div>

                            <div class="form-group">
                                <div class="g-recaptcha" data-sitekey="{{ config('app.google_recaptcha_site_key') }}"></div>
                            </div>
                            <button type="submit" class="btn btn-default btn-1 lkbtn"> Submit </button>
                        </form>
                    </div>
                    <script>
                        document.getElementById('contact-form').addEventListener('submit', function(event) {
                            var recaptchaResponse = grecaptcha.getResponse();
                    
                            if (recaptchaResponse.length === 0) {
                                event.preventDefault();
                                alert('Please complete the reCAPTCHA');
                            }
                        });
                    </script>
                    <div class="col-md-5 col-sm-5 col-xs-12 formrgt animate" style="height:50px !important; margin-top: 40px;" data-anim-type="fadeInRight" data-anim-delay="800">
                        <h3>Contact Information</h3>
                        <ul class="conticons">
                            <li>
                                <a href="javacsript:void(0)">
                                    <i class="fa fa-map-marker" aria-hidden="true"></i> Test address </a>
                            </li>
                            <li><a href="tel:12345678"><i class="fa fa-phone" aria-hidden="true"></i> 07452 998 238 </a></li>
                            <li><a href="mailto:admin@locumkit.com"><i class="fa fa-envelope" aria-hidden="true"></i> admin@locumkit.com </a></li>
                        </ul>
                        @php
                            use App\Models\coreConfigData;
                        
                            $socialIcons = [
                                'fb' => 'fa-facebook-square',
                                'li' => 'fa-linkedin-square',
                                'gp' => 'fa-google',
                                'pi' => 'fa-pinterest',
                                'tw' => '',
                            ];
                        
                            $socialLinks = coreConfigData::whereIn('identifier', array_keys($socialIcons))
                                ->pluck('value', 'identifier');
                        @endphp
                        
                        <h3>Social Media</h3>
                        <ul class="list-inline socialicon">
                           @foreach ($socialIcons as $identifier => $icon)
                                @if (!empty($socialLinks[$identifier]) && ($identifier == 'li' || $identifier == 'fb'))
                                    <li>
                                        <a href="{{ $socialLinks[$identifier] }}" target="_blank">
                                            <i class="fa {{ $icon }}" aria-hidden="true"></i>
                                        </a>
                                    </li>
                                @endif
                            @endforeach
                            <!--<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="white" class="bi bi-twitter-x" viewBox="0 0 16 16">-->
                            <!--  <path d="M12.6.75h2.454l-5.36 6.142L16 15.25h-4.937l-3.867-5.07-4.425 5.07H.316l5.733-6.57L0 .75h5.063l3.495 4.633L12.601.75Zm-.86 13.028h1.36L4.323 2.145H2.865z"/>-->
                            <!--</svg>-->
                        </ul>

                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

@push('scripts')
    <script src='https://www.google.com/recaptcha/api.js'></script>
@endpush
