@if (Route::is('index'))
    <!-- Footer -->
    <footer class="footer footer-four">	
        <!-- Footer Top -->	
        <div class="footer-top aos" data-aos="fade-up">
            <div class="container">
                <div class="row">
                    <div class="col-lg-5">
                        <div class="footer-contact footer-widget">
                            <div class="footer-logo">
                                <img src="{{URL::asset('build/img/logo-white.svg')}}" class="img-fluid aos" alt="logo">
                            </div>
                            <div class="footer-contact-info">
                                <p>Nous offrons une flotte diversifiée de véhicules pour répondre à tous les besoins, y compris des voitures compactes, des berlines, des SUV et des véhicules de luxe.</p>
                            </div>	
                            <div class="d-flex align-items-center gap-1 app-icon">
                                <a href="javascript:void(0);">
                                    <img src="{{URL::asset('build/img/icons/gpay.svg')}}" class="img-fluid" alt="logo">
                                </a>
                                <a href="javascript:void(0);">
                                    <img src="{{URL::asset('build/img/icons/app.svg')}}" class="img-fluid" alt="logo">
                                </a>
                            </div>
                            <ul class="social-icon">
                                <li>
                                    <a href="javascript:void(0)"><i class="fa-brands fa-facebook-f"></i></a>
                                </li>
                                <li>
                                    <a href="javascript:void(0)"><i class="fa-brands fa-instagram"></i></a>
                                </li>
                                <li>
                                    <a href="javascript:void(0)"><i class="fab fa-behance"></i></a>
                                </li>
                                <li>
                                    <a href="javascript:void(0)"><i class="fab fa-twitter"></i> </a>
                                </li>
                                <li>
                                    <a href="javascript:void(0)"><i class="fab fa-linkedin"></i></a>
                                </li>
                            </ul>
                        </div>
                    </div>
                    <div class="col-lg-7">
                        <div class="row">
                            <div class="col-lg-4 col-md-6">
                                <!-- Footer Widget -->
                                <div class="footer-widget footer-menu">
                                    <h5 class="footer-title">Liens rapides</h5>
                                    <ul>
                                        <li>
                                            <a href="javascript:void(0)">Mon compte</a>
                                        </li>
                                        <li>
                                            <a href="javascript:void(0)">Campagnes</a>
                                        </li>
                                        <li>
                                            <a href="javascript:void(0)">Dreams rent Dealers</a>
                                        </li>
                                        <li>
                                            <a href="javascript:void(0)">Offres et promotions</a>
                                        </li>
                                        <li>
                                            <a href="javascript:void(0)">Services financiers</a>
                                        </li>							
                                    </ul>
                                </div>
                                <!-- /Footer Widget -->
                            </div>
                            <div class="col-lg-4 col-md-6">
                                <!-- Footer Widget -->
                                <div class="footer-widget footer-menu">
                                    <h5 class="footer-title">Pages</h5>
                                    <ul>
                                        <li>
                                            <a href="{{url('about-us')}}">À propos de nous</a>
                                        </li>
                                        <li>
                                            <a href="javascript:void(0)">Devenir partenaire</a>
                                        </li>
                                        <li>
                                            <a href="{{url('faq')}}">Faq’s</a>
                                        </li>
                                        <li>
                                            <a href="{{url('testimonial')}}">Témoignages</a>
                                        </li>
                                        <li>
                                            <a href="{{url('contact-us')}}">Contactez-nous</a>
                                        </li>							
                                    </ul>
                                </div>
                                <!-- /Footer Widget -->
                            </div>	
                            <div class="col-lg-4 col-md-6">
                                <!-- Footer Widget -->
                                <div class="footer-widget footer-menu">
                                    <h5 class="footer-title">Liens utiles</h5>
                                    <ul>
                                        <li>
                                            <a href="javascript:void(0)">Mon compte</a>
                                        </li>
                                        <li>
                                            <a href="javascript:void(0)">Campagnes</a>
                                        </li>
                                        <li>
                                            <a href="javascript:void(0)">Dreams rent Dealers</a>
                                        </li>
                                        <li>
                                            <a href="javascript:void(0)">Offres et promotions</a>
                                        </li>
                                        <li>
                                            <a href="javascript:void(0)">Services financiers</a>
                                        </li>							
                                    </ul>
                                </div>
                                <!-- /Footer Widget -->
                            </div>									
                        </div>							
                    </div>
                </div>					
            </div>
        </div>
        <!-- /Footer Top -->

        <!-- Footer Bottom -->
        <div class="footer-bottom">
            <div class="container">
                <!-- Copyright -->
                <div class="copyright">
                    <div class="row align-items-center row-gap-3">
                        <div class="col-lg-4">
                            <div class="copyright-text">
                                <p>Copyright &copy; 2025 Dreams Rent. Tous droits réservés.</p>
                            </div>
                        </div>
                        <div class="col-lg-4">
                            <div class="payment-list">
                                <a href="javascript:void(0);">
                                    <img src="{{URL::asset('build/img/icons/payment-01.svg')}}" alt="img">
                                </a>
                                <a href="javascript:void(0);">
                                    <img src="{{URL::asset('build/img/icons/payment-02.svg')}}" alt="img">
                                </a>
                                <a href="javascript:void(0);">
                                    <img src="{{URL::asset('build/img/icons/payment-03.svg')}}" alt="img">
                                </a>
                            </div>
                        </div>
                        <div class="col-lg-4">
                            <ul class="privacy-link">
                                <li>
                                    <a href="{{url('privacy-policy')}}">Confidentialité</a>
                                </li>
                                <li>
                                    <a href="{{url('terms-condition')}}">Conditions générales</a>
                                </li>
                                <li>
                                    <a href="javascript:void(0);">Politique de remboursement</a>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
                <!-- /Copyright -->
            </div>
        </div>
        <!-- /Footer Bottom -->			
    </footer>
    <!-- /Footer -->	
@endif

@if (!Route::is(['forgot-password', 'login', 'register', 'reset-password','booking-adon','index','index-3', 'index-4']))
    <!-- Footer -->
    <footer class="footer">	
        <!-- Footer Top -->	
        <div class="footer-top aos" data-aos="fade-down">
            <div class="container">
                <div class="row">
                    <div class="col-lg-7">
                        <div class="row">
                            <div class="col-lg-4 col-md-6">
                                <!-- Footer Widget -->
                                <div class="footer-widget footer-menu">
                                    <h5 class="footer-title">À propos</h5>
                                    <ul>
                                        <li>
                                            <a href="{{url('about-us')}}">Notre entreprise</a>
                                        </li>
                                        <li>
                                            <a href="javascript:void(0)">Shop Toyota</a>
                                        </li>
                                        <li>
                                            <a href="javascript:void(0)">Dreamsrentals USA</a>
                                        </li>
                                        <li>
                                            <a href="javascript:void(0)">Dreamsrentals Worldwide</a>
                                        </li>
                                        <li>
                                            <a href="javascript:void(0)">Dreamsrental Category</a>
                                        </li>										
                                    </ul>
                                </div>
                                <!-- /Footer Widget -->
                            </div>
                            <div class="col-lg-4 col-md-6">
                                <!-- Footer Widget -->
                                <div class="footer-widget footer-menu">
                                    <h5 class="footer-title">Types de véhicules</h5>
                                    <ul>
                                        <li>
                                            <a href="javascript:void(0)">Tous les véhicules</a>
                                        </li>
                                        <li>
                                            <a href="javascript:void(0)">SUVs</a>
                                        </li>
                                        <li>
                                            <a href="javascript:void(0)">Camions</a>
                                        </li>
                                        <li>
                                            <a href="javascript:void(0)">Voitures</a>
                                        </li>
                                        <li>
                                            <a href="javascript:void(0)">Crossovers</a>
                                        </li>								
                                    </ul>
                                </div>
                                <!-- /Footer Widget -->
                            </div>
                            <div class="col-lg-4 col-md-6">
                                <!-- Footer Widget -->
                                <div class="footer-widget footer-menu">
                                    <h5 class="footer-title">Liens rapides</h5>
                                    <ul>
                                        <li>
                                            <a href="javascript:void(0)">Mon compte</a>
                                        </li>
                                        <li>
                                            <a href="javascript:void(0)">Campagnes</a>
                                        </li>
                                        <li>
                                            <a href="javascript:void(0)">Dreamsrental Dealers</a>
                                        </li>
                                        <li>
                                            <a href="javascript:void(0)">Offres et promotions</a>
                                        </li>
                                        <li>
                                            <a href="javascript:void(0)">Services financiers</a>
                                        </li>								
                                    </ul>
                                </div>
                                <!-- /Footer Widget -->
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-5">
                        <div class="footer-contact footer-widget">
                            <h5 class="footer-title">Coordonnées</h5>
                            <div class="footer-contact-info">									
                                <div class="footer-address">											
                                    <span><i class="feather-phone-call"></i></span>
                                    <div class="addr-info">
                                        <a href="tel:+1(888)7601940">+ 1 (888) 760 1940</a>
                                    </div>
                                </div>
                                <div class="footer-address">
                                    <span><i class="feather-mail"></i></span>
                                    <div class="addr-info">
                                        <a href="mailto:support@example.com">support@example.com</a>
                                    </div>
                                </div>
                                <div class="update-form">
                                    <form action="#">
                                        <span><i class="feather-mail"></i></span> 
                                        <input type="email" class="form-control" placeholder="Entrez votre email ici">
                                        <button type="submit" class="btn btn-subscribe"><span><i class="feather-send"></i></span></button>
                                    </form>
                                </div>
                            </div>								
                            <div class="footer-social-widget">
                                <ul class="nav-social">
                                    <li>
                                        <a href="javascript:void(0)"><i class="fa-brands fa-facebook-f fa-facebook fi-icon"></i></a>
                                    </li>
                                    <li>
                                        <a href="javascript:void(0)"><i class="fab fa-instagram fi-icon"></i></a>
                                    </li>
                                    <li>
                                        <a href="javascript:void(0)"><i class="fab fa-behance fi-icon"></i></a>
                                    </li>
                                    <li>
                                        <a href="javascript:void(0)"><i class="fab fa-twitter fi-icon"></i> </a>
                                    </li>
                                    <li>
                                        <a href="javascript:void(0)"><i class="fab fa-linkedin fi-icon"></i></a>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>					
            </div>
        </div>
        <!-- /Footer Top -->

        <!-- Footer Bottom -->
        <div class="footer-bottom">
            <div class="container">
                <!-- Copyright -->
                <div class="copyright">
                    <div class="row align-items-center">
                        <div class="col-md-6">
                            <div class="copyright-text">
                                <p>© 2024 Dreams Rent. Tous droits réservés.</p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <!-- Copyright Menu -->
                            <div class="copyright-menu">
                                <div class="vistors-details">
                                    <ul class="d-flex">											
                                        <li><a href="javascript:void(0)"><img class="img-fluid" src="{{URL::asset('/build/img/icons/paypal.svg')}}" alt="Paypal"></a></li>											
                                        <li><a href="javascript:void(0)"><img class="img-fluid" src="{{URL::asset('/build/img/icons/visa.svg')}}" alt="Visa"></a></li>
                                        <li><a href="javascript:void(0)"><img class="img-fluid" src="{{URL::asset('/build/img/icons/master.svg')}}" alt="Master"></a></li>
                                        <li><a href="javascript:void(0)"><img class="img-fluid" src="{{URL::asset('/build/img/icons/applegpay.svg')}}" alt="applegpay"></a></li>
                                    </ul>										   								
                                </div>
                            </div>
                            <!-- /Copyright Menu -->
                        </div>
                    </div>
                </div>
                <!-- /Copyright -->
            </div>
        </div>
        <!-- /Footer Bottom -->			
    </footer>
    <!-- /Footer -->	
@endif

@if(Route::is(['index-3']))
    <!-- Footer -->
    <footer class="footer footer-three">	
        <!-- Footer Top -->	
        <div class="footer-top aos" data-aos="fade-up">
            <div class="container">
                <div class="row">
                    <div class="col-lg-2">
                        <div class="footer-contact footer-widget">
                            <div class="footer-logo">
                                <img src="{{URL::asset('/build/img/logo.svg')}}" class="img-fluid aos" alt="logo">
                            </div>
                            <div class="footer-contact-info">
                                <h6>Vous souhaitez réserver instantanément ? Contactez-nous !!!</h6>
                                <div class="footer-address">
                                    <div class="addr-info">
                                        <a href="tel:+1(888)7601940"><i class="bx bxs-phone"></i>+ 1 (888) 760 1940</a>
                                    </div>
                                </div>
                                <div class="footer-address">
                                    <div class="addr-info">
                                        <a href="mailto:support@example.com"><i class="bx bxs-envelope"></i>support@example.com</a>
                                    </div>
                                </div>
                            </div>	
                            <ul class="store-icon">
                                <li>
                                    <a href="javascript:void(0);">
                                        <img src="{{URL::asset('/build/img/icons/play-icon.svg')}}" class="img-fluid" alt="logo">
                                    </a>
                                </li>
                                <li>
                                    <a href="javascript:void(0);">
                                        <img src="{{URL::asset('/build/img/icons/app-icon.svg')}}" class="img-fluid" alt="logo">
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </div>
                    <div class="col-lg-2 col-md-6">
                        <!-- Footer Widget -->
                        <div class="footer-widget footer-menu">
                            <h5 class="footer-title">Entreprise</h5>
                            <ul>
                                <li>
                                    <a href="{{url('about-us')}}">Notre entreprise</a>
                                </li>
                                <li>
                                    <a href="javascript:void(0)">Bike Rent</a>
                                </li>
                                <li>
                                    <a href="javascript:void(0)">Dreams rent USA</a>
                                </li>
                                <li>
                                    <a href="javascript:void(0)">Dreams rent Worldwide</a>
                                </li>
                                <li>
                                    <a href="javascript:void(0)">Dreams rent Category</a>
                                </li>									
                            </ul>
                        </div>
                        <!-- /Footer Widget -->
                    </div>
                    <div class="col-lg-2 col-md-6">
                        <!-- Footer Widget -->
                        <div class="footer-widget footer-menu">
                            <h5 class="footer-title">Types de véhicules</h5>
                            <ul>
                                <li>
                                    <a href="javascript:void(0)">Electric</a>
                                </li>
                                <li>
                                    <a href="javascript:void(0)">Scooters</a>
                                </li>
                                <li>
                                    <a href="javascript:void(0)">Sports</a>
                                </li>
                                <li>
                                    <a href="javascript:void(0)">Motos de course</a>
                                </li>
                                <li>
                                    <a href="javascript:void(0)">Tout-terrain</a>
                                </li>							
                            </ul>
                        </div>
                        <!-- /Footer Widget -->
                    </div>
                    <div class="col-lg-2 col-md-6">
                        <!-- Footer Widget -->
                        <div class="footer-widget footer-menu">
                            <h5 class="footer-title">Liens rapides</h5>
                            <ul>
                                <li>
                                    <a href="javascript:void(0)">Mon compte</a>
                                </li>
                                <li>
                                    <a href="javascript:void(0)">Campagnes</a>
                                </li>
                                <li>
                                    <a href="javascript:void(0)">Dreams rent Dealers</a>
                                </li>
                                <li>
                                    <a href="javascript:void(0)">Offres et promotions</a>
                                </li>
                                <li>
                                    <a href="javascript:void(0)">Services financiers</a>
                                </li>							
                            </ul>
                        </div>
                        <!-- /Footer Widget -->
                    </div>	
                    <div class="col-lg-2 col-md-6">
                        <!-- Footer Widget -->
                        <div class="footer-widget footer-menu">
                            <h5 class="footer-title">Ressources</h5>
                            <ul>
                                <li>
                                    <a href="javascript:void(0)">Assistance</a>
                                </li>
                                <li>
                                    <a href="javascript:void(0)">Sécurité</a>
                                </li>
                                <li>
                                    <a href="javascript:void(0)">Centres d'aide</a>
                                </li>
                                <li>
                                    <a href="javascript:void(0)">Préférences</a>
                                </li>
                                <li>
                                    <a href="javascript:void(0)">Préférences</a>
                                </li>							
                            </ul>
                        </div>
                        <!-- /Footer Widget -->
                    </div>	
                    <div class="col-lg-2 col-md-6">
                        <!-- Footer Widget -->
                        <div class="footer-widget footer-menu">
                            <h5 class="footer-title">Pour commencer</h5>
                            <ul>
                                <li>
                                    <a href="javascript:void(0)">Introduction</a>
                                </li>
                                <li>
                                    <a href="javascript:void(0)">Documentation</a>
                                </li>
                                <li>
                                    <a href="javascript:void(0)">Usage</a>
                                </li>
                                <li>
                                    <a href="javascript:void(0)">API</a>
                                </li>
                                <li>
                                    <a href="javascript:void(0)">Elements</a>
                                </li>							
                            </ul>
                        </div>
                        <!-- /Footer Widget -->
                    </div>
                </div>					
            </div>
        </div>
        <!-- /Footer Top -->

        <!-- Footer Bottom -->
        <div class="footer-bottom">
            <div class="container">
                <!-- Copyright -->
                <div class="copyright">
                    <div class="row align-items-center">
                        <div class="col-lg-6">
                            <div class="copyright-text">
                                <p>Copyright © 2024 <span>Dreams Rent</span>. Tous droits réservés.</p>
                            </div>
                        </div>
                        <div class="col-lg-6">

                            <div class="footer-list">
                                <ul>
                                    <li class="country-flag">
                                        <div class="dropdown">
                                            <a class="dropdown-toggle nav-tog" data-bs-toggle="dropdown" href="javascript:void(0);">
                                                <img src="{{URL::asset('/build/img/flags/us.png')}}" alt="Img">Anglais
                                            </a>
                                            <div class="dropdown-menu dropdown-menu-end">
                                                <a href="javascript:void(0);" class="dropdown-item">
                                                    <img src="{{URL::asset('/build/img/flags/fr.png')}}" alt="Img">Français
                                                </a>
                                                <a href="javascript:void(0);" class="dropdown-item">
                                                    <img src="{{URL::asset('/build/img/flags/es.png')}}" alt="Img">Espagnol
                                                </a>
                                                <a href="javascript:void(0);" class="dropdown-item">
                                                    <img src="{{URL::asset('/build/img/flags/de.png')}}" alt="Img">Allemand
                                                </a>
                                            </div>
                                        </div>
                                    </li>
                                    <li class="country-flag lang-nav">
                                        <div class="dropdown">
                                            <a class="dropdown-toggle nav-tog" data-bs-toggle="dropdown" href="javascript:void(0);">
                                            <i class="bx bx-globe"></i>USD
                                        </a>
                                        <div class="dropdown-menu dropdown-menu-end">
                                            <a href="javascript:void(0);" class="dropdown-item">
                                                <img src="{{URL::asset('/build/img/flags/fr.png')}}" alt="Img">Euro
                                            </a>
                                            <a href="javascript:void(0);" class="dropdown-item">
                                                <img src="{{URL::asset('/build/img/flags/es.png')}}" alt="Img">INR
                                            </a>
                                        </div>
                                        </div>
                                    </li>
                                    <li>
                                        <ul class="social-icon">
                                            <li>
                                                <a href="javascript:void(0)"><i class="fa-brands fa-facebook-f fa-facebook"></i></a>
                                            </li>
                                            <li>
                                                <a href="javascript:void(0)"><i class="fab fa-instagram"></i></a>
                                            </li>
                                            <li>
                                                <a href="javascript:void(0)"><i class="fab fa-behance"></i></a>
                                            </li>
                                            <li>
                                                <a href="javascript:void(0)"><i class="fab fa-twitter"></i> </a>
                                            </li>
                                            <li>
                                                <a href="javascript:void(0)"><i class="fab fa-linkedin"></i></a>
                                            </li>
                                        </ul>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- /Copyright -->
            </div>
        </div>
        <!-- /Footer Bottom -->			
    </footer>
    <!-- /Footer -->	
@endif

@if(Route::is(['index-4']))
    <!-- Footer -->
    <footer class="footer-two">
        <div class="sec-bg">
            <img src="{{URL::asset('build/img/bg/sec-bg-wave.png')}}" alt="Img">
            <img src="{{URL::asset('build/img/bg/anchor-img-02.png')}}" alt="Img">
        </div>
        <div class="container">
            <div class="footer-top">
                <div class="row">
                    <div class="col-xl-2 col-lg-3 col-md-4 col-sm-6">
                        <div class="footer-widget">
                            <div class="widget-title">
                                <h4>Entreprise</h4>
                                <ul class="footer-links">
                                    <li><a href="javascript:void(0);"><i class="fas fa-chevron-right"></i>Notre entreprise</a></li>
                                    <li><a href="javascript:void(0);"><i class="fas fa-chevron-right"></i>Yacht Rent</a></li>
                                    <li><a href="javascript:void(0);"><i class="fas fa-chevron-right"></i>Dreams rent USA</a></li>
                                    <li><a href="javascript:void(0);"><i class="fas fa-chevron-right"></i>Dreams rent Worldwide</a></li>
                                    <li><a href="javascript:void(0);"><i class="fas fa-chevron-right"></i>Dreams rent Category</a></li>
                                </ul>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-2 col-lg-3 col-md-4 col-sm-6">
                        <div class="footer-widget">
                            <div class="widget-title">
                                <h4>Types de véhicules</h4>
                                <ul class="footer-links">
                                    <li><a href="javascript:void(0);"><i class="fas fa-chevron-right"></i>Electric</a></li>
                                    <li><a href="javascript:void(0);"><i class="fas fa-chevron-right"></i>Scooters</a></li>
                                    <li><a href="javascript:void(0);"><i class="fas fa-chevron-right"></i>Sports</a></li>
                                    <li><a href="javascript:void(0);"><i class="fas fa-chevron-right"></i>Motos de course</a></li>
                                    <li><a href="javascript:void(0);"><i class="fas fa-chevron-right"></i>Tout-terrain</a></li>
                                </ul>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-2 col-lg-3 col-md-4 col-sm-6">
                        <div class="footer-widget">
                            <div class="widget-title">
                                <h4>Liens rapides</h4>
                                <ul class="footer-links">
                                    <li><a href="javascript:void(0);"><i class="fas fa-chevron-right"></i>Mon compte</a></li>
                                    <li><a href="javascript:void(0);"><i class="fas fa-chevron-right"></i>Campagnes</a></li>
                                    <li><a href="javascript:void(0);"><i class="fas fa-chevron-right"></i>Dreams rent Dealers</a></li>
                                    <li><a href="javascript:void(0);"><i class="fas fa-chevron-right"></i>Offres et promotions</a></li>
                                    <li><a href="javascript:void(0);"><i class="fas fa-chevron-right"></i>Services financiers</a></li>
                                </ul>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-2 col-lg-3 col-md-4 col-sm-6">
                        <div class="footer-widget">
                            <div class="widget-title">
                                <h4>Ressources</h4>
                                <ul class="footer-links">
                                    <li><a href="javascript:void(0);"><i class="fas fa-chevron-right"></i>Assistance</a></li>
                                    <li><a href="javascript:void(0);"><i class="fas fa-chevron-right"></i>Sécurité</a></li>
                                    <li><a href="javascript:void(0);"><i class="fas fa-chevron-right"></i>Centres d'aide</a></li>
                                    <li><a href="javascript:void(0);"><i class="fas fa-chevron-right"></i>Préférences</a></li>
                                    <li><a href="javascript:void(0);"><i class="fas fa-chevron-right"></i>Clients</a></li>
                                </ul>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-2 col-lg-3 col-md-4 col-sm-6">
                        <div class="footer-widget">
                            <div class="widget-title">
                                <h4>Pour commencer</h4>
                                <ul class="footer-links">
                                    <li><a href="javascript:void(0);"><i class="fas fa-chevron-right"></i>Introduction</a></li>
                                    <li><a href="javascript:void(0);"><i class="fas fa-chevron-right"></i>Documentation</a></li>
                                    <li><a href="javascript:void(0);"><i class="fas fa-chevron-right"></i>Usage</a></li>
                                    <li><a href="javascript:void(0);"><i class="fas fa-chevron-right"></i>API</a></li>
                                    <li><a href="javascript:void(0);"><i class="fas fa-chevron-right"></i>Elements</a></li>
                                </ul>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-2 col-lg-3 col-md-4 col-sm-6">
                        <div class="footer-widget">
                            <div class="widget-title">
                                <h4>Assistance 24h/24 7j/7</h4>
                                <ul class="footer-address">
                                    <li>Vous souhaitez réserver instantanément ? Contactez-nous !!!</li>
                                    <li>Contact  : + 1 (888) 760 1940</li>
                                    <li>Emergency  : +1 68564 55664</li>
                                    <li>Email : support@example.com</li>
                                    <li class="social-link">
                                        <ul>
                                            <li><a href="javascript:void(0);"><i class="fa-brands fa-facebook-f"></i></a></li>
                                            <li><a href="javascript:void(0);"><i class="fa-brands fa-instagram"></i></a></li>
                                            <li><a href="javascript:void(0);"><i class="fa-brands fa-behance"></i></a></li>
                                            <li><a href="javascript:void(0);"><i class="fa-brands fa-twitter"></i></a></li>
                                            <li><a href="javascript:void(0);"><i class="fa-brands fa-pinterest-p"></i></a></li>
                                            <li><a href="javascript:void(0);"><i class="fa-brands fa-linkedin"></i></a></li>
                                        </ul>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="footer-bottom">
                <div class="copy-right">
                    <p>Copyright &copy; 2024 <span> Dreams Rent</span> . Tous droits réservés.</p>
                </div>
                <div class="app-store-links d-flex align-items-center">
                    <span class="me-2"><a href="javascript:void(0);"><img src="{{URL::asset('build/img/icons/google-play.svg')}}" alt="Img"></a></span>
                    <span><a href="javascript:void(0);"><img src="{{URL::asset('build/img/icons/app-store.svg')}}" alt="Img"></a></span>
                </div>
            </div>
        </div>
    </footer>
    <!-- Footer -->
@endif