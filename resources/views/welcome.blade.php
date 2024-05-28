<!DOCTYPE html>
    <!--[if lt IE 8 ]><html class="no-js ie ie7" lang="en"> <![endif]-->
    <!--[if IE 8 ]><html class="no-js ie ie8" lang="en"> <![endif]-->
    <!--[if (gte IE 8)|!(IE)]><!--><html class="no-js" lang="en"> <!--<![endif]-->
    <head>
        <!--- Basic Page Needs
        ================================================== -->
        <meta charset="utf-8">
        <title>José Gomes</title>

        <meta name="description" content="">
        <meta name="author" content="">

        <!-- Mobile Specific Metas
        ================================================== -->
        <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">

        <!-- CSS
        ================================================== -->
        <link rel="stylesheet" href="css/default.css">
        <link rel="stylesheet" href="css/layout.css">
        <link rel="stylesheet" href="css/media-queries.css">
        <link rel="stylesheet" href="css/magnific-popup.css">
        <link rel="stylesheet" href="css/cookies.css">
        <link rel="stylesheet" href="css/local/public/general.css">

        <!-- Script
        ================================================== -->
        <script src="js/modernizr.js"></script>

        <!-- Favicons
         ================================================== -->
        <link rel="shortcut icon" href="favicon.png" >
    </head>
    <body>
        <!-- Header
        ================================================== -->
        <header id="home">
            <nav id="nav-wrap">
                <a class="mobile-btn" href="#nav-wrap" title="Show navigation">Show navigation</a>
                <a class="mobile-btn" href="#" title="Hide navigation">Hide navigation</a>
                <ul id="nav" class="nav">
                    <li class="current"><a class="smoothscroll" href="#home">{{ __('welcome.Home') }}</a></li>
                    <li><a class="smoothscroll" href="#about">{{ __('welcome.About_me') }}</a></li>
                    <li><a class="smoothscroll" href="#resume">{{ __('welcome.Resume') }}</a></li>
                    <li><a class="smoothscroll" href="#call-to-action">{{ __('welcome.Stack_Tools') }}</a></li>
                    <li><a class="smoothscroll" href="#portfolio">{{ __('welcome.Projects') }}</a></li>
                    <li><a class="smoothscroll" href="#testimonials">{{ __('welcome.WOW') }}</a></li>
                    <li><a class="smoothscroll" href="#contact">{{ __('welcome.Contact') }}</a></li>
                    <li><a href="/login"> 🔐{{ __('welcome.Login') }}</a></li>
                    <li>
                        <div class="custom-select-wrapper">
                            <div class="custom-select">
                                @php
                                    switch(app()->getLocale()) {
                                        case 'en':
                                            $emoji = '🏴󠁧󠁢󠁥󠁮󠁧󠁿';
                                            break;
                                        case 'pt':
                                            $emoji = '🇵🇹';
                                            break;
                                        case 'jp':
                                            $emoji = '🇯🇵';
                                            break;
                                        default:
                                            $emoji = '';
                                    }
                                @endphp
                                <div class="custom-select-trigger" style="font-size: 25px; position: relative; top: 5px;">{{ $emoji }}</div>
                                <div class="custom-options">
                                    <span class="custom-option" data-value="/language/en">🏴󠁧󠁢󠁥󠁮󠁧󠁿EN</span>
                                    <span class="custom-option" data-value="/language/pt">🇵🇹PT</span>
                                    <span class="custom-option" data-value="/language/jp">🇯🇵JP</span>
                                </div>
                            </div>
                        </div>
                    </li>
                </ul> <!-- end #nav -->
            </nav> <!-- end #nav-wrap -->
            <div class="row banner">
                @if(app()->isDownForMaintenance())
                    <div class="maintenance-mode">
                        <p> {{ __('welcome.Maintenance1') }} <br /> {{ __('welcome.Maintenance2') }} </p>
                    </div>
                @endif
                <div class="banner-text">
                    <a href="https://www.codewars.com/users/JGomes87" target="_blank"><img alt="Dark Badge (large)" class="hidden dark:block" src="https://www.codewars.com/users/JGomes87/badges/large"></a>
                    <h1 class="responsive-headline">José Gomes</h1>
                    <script type="text/javascript"> //<![CDATA[
                        let tlJsHost = ((window.location.protocol == "https:") ? "https://secure.trust-provider.com/" : "http://www.trustlogo.com/");
                        document.write(unescape("%3Cscript src='" + tlJsHost + "trustlogo/javascript/trustlogo.js' type='text/javascript'%3E%3C/script%3E"));
                        //]]></script>
                    <script language="JavaScript" type="text/javascript">
                        TrustLogo("https://www.positivessl.com/images/seals/positivessl_trust_seal_md_167x42.png", "POSDV", "none");
                    </script> <br/>
                    <h3> {{ __('welcome.Phrase_1') }} <span> {{ __('welcome.Phrase_2') }} </span>.<br/><span>{{ __('welcome.Phrase_3') }}<a class="smoothscroll" href="#about"> {{ __('welcome.Phrase_4') }} </a> {{ __('welcome.Phrase_5') }} <a class="smoothscroll" href="#about"> {{ __('welcome.Phrase_6') }}</a>.</span>
                    </h3>
                    <hr />

                    <ul class="social">
                        <li><a href="https://twitter.com/routineris" target="_blank"><i class="fa fa-twitter social_extra"></i></a></li>
                        <li><a href="https://www.linkedin.com/in/jos%C3%A9-gomes-3ba05869/" target="_blank"><i class="fa fa-linkedin social_extra"></i></a></li>
                        <li><a href="https://github.com/jfgomes?tab=repositories" target="_blank"><i class="fa fa-github social_extra"></i></a></li>
                        <li><a href="https://www.youtube.com/@JGomes-dev/videos" target="_blank"><i class="fa fa-youtube social_extra"></i></a></li>
                    </ul>
                </div>
            </div>
            <p class="scrolldown">
                <a class="smoothscroll" href="#about"><i class="icon-down-circle"></i></a>
            </p>
        </header> <!-- Header End -->

        <!-- About Section
        ================================================== -->
        <section id="about">
            <div class="row">
                <div class="three columns">
                    <img class="profile-pic"  src="images/personal/logo.png" alt="" />
                </div>
                <div class="nine columns main-col">
                    <h2>{{ __('welcome.About_me') }}</h2>
                    <p>{{ __('welcome.About_me_details_textarea') }}</p>
                    <div class="row">
                        <div class="columns contact-details">
                            <h2>{{ __('welcome.Contact_details') }}</h2>
                            <p class="address">
                                <span>José Gomes</span><br>
                                <span>{{ __('welcome.City') }}</span><br>
                                <span>+351937503892</span><br>
                                <span>zx.gomes@gmail.com</span>
                            </p>
                        </div>
                        <div class="columns download">
                            <p>
                                <a onclick="downloadCV('jgomes-cv-en.pdf')" class="button"><i class="fa fa-download"></i>Download C.V. (EN - PDF)</a>
                                <a target="_blank" href="https://europa.eu/europass/eportfolio/api/eprofile/shared-profile/jos%C3%A9+francisco-rosa+gomes/b3fbb889-38e7-4acc-968e-fe906b3839f6?view=html" class="button">
                                    <i class="fa fa-download"></i>
                                    Online C.V. (EN)
                                </a>
                            </p>
                            <p>
                                <a onclick="downloadCV('jgomes-cv-pt.pdf')" class="button"><i class="fa fa-download"></i>Download C.V. (PT - PDF)</a>
                                <a target="_blank" href="https://europa.eu/europass/eportfolio/api/eprofile/shared-profile/jos%C3%A9+francisco-rosa+gomes/dd9dd07f-1796-47cb-abd6-8a6196c87cd8?view=html" class="button">
                                    <i class="fa fa-download"></i>
                                    Online C.V. (PT)
                                </a>
                            </p>
                        </div>
                    </div> <!-- end row -->
                </div> <!-- end .main-col -->
            </div>
        </section> <!-- About Section End-->

        <!-- Resume Section
        ================================================== -->
        <section id="resume">
            <!-- Education
            ----------------------------------------------- -->
            <div class="row education">
                <div class="three columns header-col">
                    <h1><span>{{ __('welcome.Education') }}</span></h1>
                </div>
                <div class="nine columns main-col">
                    <div class="row item">
                        <div class="twelve columns">
                            <img width="65" height="65" src="images/personal/olx_group_logo.jpeg" alt="">
                            <h3>{{ __('welcome.Education_place1') }}</h3>
                            <p class="info">{{ __('welcome.Education_place1_desc') }}<span>&bull;</span> <em class="date">{{ __('welcome.Education_place1_year') }}</em></p>
                        </div>
                    </div> <!-- item end -->
                    <br/>
                    <div class="row item">
                        <div class="twelve columns">
                            <img width="50" height="50" src="images/personal/edit.png" alt="">
                            <h3>{{ __('welcome.Education_place2') }}</h3>
                            <p class="info">{{ __('welcome.Education_place2') }}<span>&bull;</span> <em class="date">{{ __('welcome.Education_place2_year') }}</em></p>
                        </div>
                    </div> <!-- item end -->
                    <br/>
                    <div class="row item">
                        <div class="twelve columns">
                            <img width="50" height="50" src="images/personal/doitbetter.jpeg" alt="">
                            <h3>{{ __('welcome.Education_place3') }}</h3>
                            <p class="info">{{ __('welcome.Education_place3_desc') }}<span>&bull;</span> <em class="date">{{ __('welcome.Education_place3_year') }}</em></p>
                            <p>
                                - {{ __('welcome.Education_place3_qualification') }}
                            </p>
                        </div>
                    </div> <!-- item end -->
                    <div class="row item">
                        <div class="twelve columns">
                            <img width="60" height="60" src="images/personal/ipt.png"  alt=""/>
                            <h3>{{ __('welcome.Education_place4') }}</h3>
                            <p class="info">{{ __('welcome.Education_place4_desc') }}<span>&bull;</span> <em class="date">{{ __('welcome.Education_place4_year') }}</em></p>
                        </div>
                    </div> <!-- item end -->
                </div> <!-- main-col end -->
            </div> <!-- End Education -->

            <!-- Work
            ----------------------------------------------- -->
            <div class="row work">
                <div class="three columns header-col">
                    <h1><span>{{ __('welcome.Work') }}</span></h1>
                </div>
                <div class="nine columns main-col">
                    <div class="row item">
                        <div class="twelve columns">
                            <img width="65" height="65" src="images/personal/olx_group_logo.jpeg" alt=""/>
                            <h3>{{ __('welcome.Work_company1') }}</h3>
                            <p class="info">{{ __('welcome.Work_role1') }}<span>&bull;</span> <em class="date">{{ __('welcome.Work_time1') }}</em></p>
                            <p>{{ __('welcome.Work_desc1_textarea') }}</p>
                        </div>
                    </div> <!-- item end -->
                    <div class="row item">
                        <div class="twelve columns">
                            <img width="50" height="50" src="images/personal/bk_group_logo.jpeg" alt=""/>
                            <h3>{{ __('welcome.Work_company2') }}</h3>
                            <p class="info">{{ __('welcome.Work_role2') }}<span>&bull;</span> <em class="date">{{ __('welcome.Work_time2') }}</em></p>
                            <p>{{ __('welcome.Work_desc2_textarea') }}</p>
                        </div>
                    </div> <!-- item end -->
                    <div class="row item">
                        <div class="twelve columns">
                            <img width="50" height="50" src="images/personal/gw.jpeg" alt=""/>
                            <h3>{{ __('welcome.Work_company3') }}</h3>
                            <p class="info">{{ __('welcome.Work_role3') }}<span>&bull;</span> <em class="date">{{ __('welcome.Work_time3') }}</em></p>
                            <p>{{ __('welcome.Work_desc3_textarea') }}</p>
                        </div>
                    </div> <!-- item end -->
                </div> <!-- main-col end -->
            </div> <!-- End Work -->

            <!-- Skills
            ----------------------------------------------- -->
            <div class="row skill">
                <div class="three columns header-col">
                    <h1><span>{{ __('welcome.Skills') }}</span></h1>
                </div>
                <div class="nine columns main-col">
                    <p>{{ __('welcome.Skills_desc_textarea') }}</p>
                    <div class="bars">
                        <ul class="skills">
                            <li><span class="bar-expand php"></span><em>PHP</em></li>
                            <li><span class="bar-expand apcu"></span><em>APCu</em></li>
                            <li><span class="bar-expand psr2"></span><em>PSR-2</em></li>
                            <li><span class="bar-expand phpunit"></span><em>Phpunit</em></li>
                            <li><span class="bar-expand tdd"></span><em>TDD</em></li>
                            <li><span class="bar-expand mvc"></span><em>MVC</em></li>
                            <li><span class="bar-expand laravel"></span><em>Laravel</em></li>
                            <li><span class="bar-expand microservices"></span><em>Microservices</em></li>
                            <li><span class="bar-expand soap"></span><em>Soap</em></li>
                            <li><span class="bar-expand rest"></span><em>Rest</em></li>
                            <li><span class="bar-expand restfull"></span><em>Restfull</em></li>
                            <li><span class="bar-expand mysql"></span><em>MySQL</em></li>
                            <li><span class="bar-expand postgres"></span><em>Postgres</em></li>
                            <li><span class="bar-expand docker"></span><em>Docker ( docker-composer )</em></li>
                            <li><span class="bar-expand scrum"></span><em>Agile ( Scrum )</em></li>
                            <li><span class="bar-expand jira"></span><em>Jira</em></li>
                            <li><span class="bar-expand trello"></span><em>Trello</em></li>
                            <li><span class="bar-expand rabbit"></span><em>RabbitMQ</em></li>
                            <li><span class="bar-expand jenkins"></span><em>Jenkins</em></li>
                            <li><span class="bar-expand git"></span><em>Git</em></li>
                            <li><span class="bar-expand gitflow"></span><em>Git flow</em></li>
                            <li><span class="bar-expand github"></span><em>GitHub Repo</em></li>
                            <li><span class="bar-expand gitlab"></span><em>GitLab Repo</em></li>
                            <li><span class="bar-expand gitlabcicd"></span><em>GitLab CI/CD</em></li>
                            <li><span class="bar-expand redis"></span><em>Redis</em></li>
                            <li><span class="bar-expand swagger"></span><em>Swagger</em></li>
                            <li><span class="bar-expand css"></span><em>CSS</em></li>
                            <li><span class="bar-expand html"></span><em>HTML</em></li>
                            <li><span class="bar-expand jquery"></span><em>jQuery</em></li>
                            <li><span class="bar-expand aws"></span><em>Amazon Web Services (AWS)</em></li>
                            <li><span class="bar-expand gcp"></span><em> Google Cloud Platform (GCP)</em></li>
                        </ul>
                    </div><!-- end skill-bars -->
                </div> <!-- main-col end -->
            </div> <!-- End skills -->
        </section> <!-- Resume Section End-->

        <!-- Preferred stack / tools Section
        ================================================== -->
        <section id="call-to-action">
            <div class="row">
                <div class="two columns header-col">
                    <h1><span></span></h1>
                </div>
                <div class="seven columns">
                    <h2>
                        <span class="lead">
                            {{ __('welcome.Stack_Tools') }}
                        </span>
                    </h2><br>
                    <p class="text-left">
                        <span class="lead">
                            <img src="/images/personal/logos/ubuntu.png" width="32px" height="32px" alt=""/> linux (ubuntu)<br>
                            <img src="/images/personal/logos/phpstorm.png" width="32px" height="32px" alt="" /> phpstorm<br>
                            <img src="/images/personal/logos/workb.png" width="32px" height="32px" alt="" /> mysql workbench<br>
                            <img src="/images/personal/logos/phpmyadmin.png" width="32px" height="40px" alt="" /> phpmyadmin<br>
                            <img src="/images/personal/logos/docker.png" width="32px" height="32px" alt="" /> docker desktop<br>
                            <img src="/images/personal/logos/postman.png" width="32px" height="32px" alt="" /> postman<br>
                            <img src="/images/personal/logos/zoom.png" width="32px" height="32px" alt="" /> zoom<br>
                            <img src="/images/personal/logos/apache.png" width="32px" height="32px" alt="" /> apache2<br>
                            <img src="/images/personal/logos/gc.png" width="32px" height="32px" alt="" /> gcp buckets<br>
                            <img src="/images/personal/logos/gimp.png" width="32px" height="32px" alt="" /> gimp <br>
                            <img src="/images/personal/logos/slack.png" width="32px" height="32px" alt="" /> slack <br>
                            <img src="/images/personal/logos/drawio.png" width="32px" height="32px" alt="" style="padding: 3px"/> draw io <br>
                            - {{ __('welcome.Etc') }}<br>
                        </span>
                    </p>
                    <style>
                        .lead img {
                            vertical-align: middle;
                            margin-bottom: 6px;
                        }
                    </style>
                </div>
                <div class="three columns action"></div>
            </div>
        </section> <!-- Preferred stack / tools Section End-->

        <!-- Portfolio Section
        ================================================== -->
        <section id="portfolio">
            <div class="row">
                <div class="twelve columns collapsed">
                    <h1 class="portfolio-text-size">{{ __('welcome.Projects') }}</h1>
                    <!-- portfolio-wrapper -->


                    <div id="portfolio-wrapper" class="bgrid-quarters s-bgrid-thirds cf">
                        <div class="columns portfolio-item">
                            <div class="item-wrap">
                                <a href="#">
                                    <img alt="Cimac - Plataforma criativa" class="portfolio-image" src="images/personal/cimac.png">
                                    <div class="overlay">
                                        <div class="portfolio-item-meta">
                                            <h5>Cimac - Plataforma criativa</h5>
                                            <p>Backend dev + Integrations</p>
                                        </div>
                                    </div>
                                    <div class="link-icon"><i class="icon-plus"></i></div>
                                </a>
                            </div>
                        </div>  <!-- item end -->
                        <div class="columns portfolio-item">
                            <div class="item-wrap">
                                <a href="#">
                                    <img alt="Savoy Residence" class="portfolio-image" src="images/personal/savoy.png">
                                    <div class="overlay">
                                        <div class="portfolio-item-meta">
                                            <h5>Savoy Residence</h5>
                                            <p>Backend dev + Security</p>
                                        </div>
                                    </div>
                                    <div class="link-icon"><i class="icon-plus"></i></div>
                                </a>
                            </div>
                        </div>  <!-- item end -->
                        <div class="columns portfolio-item">
                            <div class="item-wrap">
                                <a href="#">
                                    <img alt="Mautic for Skilltech and OAL" class="portfolio-image" src="images/personal/mautic.png">
                                    <div class="overlay">
                                        <div class="portfolio-item-meta">
                                            <h5>Mautic for Skilltech and OAL</h5>
                                            <p>Infra + Supervisor + Crons + Queues</p>
                                        </div>
                                    </div>
                                    <div class="link-icon"><i class="icon-plus"></i></div>
                                </a>
                            </div>
                        </div> <!-- item end -->
                        <div class="columns portfolio-item">
                            <div class="item-wrap">
                                <a href="#">
                                    <img alt="MyFib" class="portfolio-image" src="images/personal/myfib.png">
                                    <div class="overlay">
                                        <div class="portfolio-item-meta">
                                            <h5>MyFib</h5>
                                            <p>Backend + platform support</p>
                                        </div>
                                    </div>
                                    <div class="link-icon"><i class="icon-plus"></i></div>
                                </a>
                            </div>
                        </div> <!-- item end -->








                    <div id="portfolio-wrapper" class="bgrid-quarters s-bgrid-thirds cf">
                        <div class="columns portfolio-item">
                            <div class="item-wrap">
                                <a href="#modal-12">
                                    <img alt="José Gomes Site" class="portfolio-image" src="images/personal/logo3.png">
                                    <div class="overlay">
                                        <div class="portfolio-item-meta">
                                            <h5>{{ __('welcome.Project12') }}</h5>
                                            <p>{{ __('welcome.Project12_role') }}</p>
                                        </div>
                                    </div>
                                    <div class="link-icon"><i class="icon-plus"></i></div>
                                </a>
                            </div>
                        </div>  <!-- item end -->
                        <div class="columns portfolio-item">
                            <div class="item-wrap">
                                <a href="#modal-11">
                                    <img alt="Routineris" class="portfolio-image" src="images/personal/routineris2.png">
                                    <div class="overlay">
                                        <div class="portfolio-item-meta">
                                            <h5>{{ __('welcome.Project11') }} <br/> <span style="color:orange; font-size: 12px">{{ __('welcome.Project11_migration') }}</span></h5>
                                            <p>{{ __('welcome.Project11_role') }}</p>
                                        </div>
                                    </div>
                                    <div class="link-icon"><i class="icon-plus"></i></div>
                                </a>
                            </div>
                        </div>  <!-- item end -->
                        <div class="columns portfolio-item">
                            <div class="item-wrap">
                                <a href="#modal-01">
                                    <img alt="Imovirtual" class="portfolio-image" src="images/personal/imo2.png">
                                    <div class="overlay">
                                        <div class="portfolio-item-meta">
                                            <h5>{{ __('welcome.Project10') }}</h5>
                                            <p>{{ __('welcome.Project10_role') }}</p>
                                        </div>
                                    </div>
                                    <div class="link-icon"><i class="icon-plus"></i></div>
                                </a>
                            </div>
                        </div> <!-- item end -->
                        <div class="columns portfolio-item">
                            <div class="item-wrap">
                                <a href="#modal-02">
                                    <img alt="Otodom" class="portfolio-image" src="images/personal/otodom.png">
                                    <div class="overlay">
                                        <div class="portfolio-item-meta">
                                            <h5>{{ __('welcome.Project9') }}</h5>
                                            <p>{{ __('welcome.Project9_role') }}</p>
                                        </div>
                                    </div>
                                    <div class="link-icon"><i class="icon-plus"></i></div>
                                </a>
                            </div>
                        </div> <!-- item end -->
                        <div class="columns portfolio-item">
                            <div class="item-wrap">
                                <a href="#modal-03">
                                    <img alt="Storia" class="portfolio-image" src="images/personal/storia2.jpg">
                                    <div class="overlay">
                                        <div class="portfolio-item-meta">
                                            <h5>{{ __('welcome.Project8') }}</h5>
                                            <p>{{ __('welcome.Project8_role') }}</p>
                                        </div>
                                    </div>
                                    <div class="link-icon"><i class="icon-plus"></i></div>
                                </a>
                            </div>
                        </div> <!-- item end -->
                        <div class="columns portfolio-item">
                            <div class="item-wrap">
                                <a href="#modal-04">
                                    <img alt="Atlastock" class="portfolio-image" src="images/personal/as2.png">
                                    <div class="overlay">
                                        <div class="portfolio-item-meta">
                                            <h5>{{ __('welcome.Project7') }}</h5>
                                            <p>{{ __('welcome.Project7_role') }}</p>
                                        </div>
                                    </div>
                                    <div class="link-icon"><i class="icon-plus"></i></div>
                                </a>
                            </div>
                        </div> <!-- item end -->
                        <div class="columns portfolio-item">
                            <div class="item-wrap">
                                <a href="#modal-05">
                                    <img alt="Young Great Westerners!" class="portfolio-image" src="images/personal/gwr2.jpg">
                                    <div class="overlay">
                                        <div class="portfolio-item-meta">
                                            <h5>{{ __('welcome.Project6') }}</h5>
                                            <p>{{ __('welcome.Project6_role') }}</p>
                                        </div>
                                    </div>
                                    <div class="link-icon"><i class="icon-plus"></i></div>
                                </a>
                            </div>
                        </div> <!-- item end -->
                        <div class="columns portfolio-item">
                            <div class="item-wrap">
                                <a href="#modal-06">
                                    <img alt="PA MySite" class="portfolio-image" src="images/personal/paginas-amarelas-sa-lisboa_big2.jpg">
                                    <div class="overlay">
                                        <div class="portfolio-item-meta">
                                            <h5>{{ __('welcome.Project5') }}</h5>
                                            <p>{{ __('welcome.Project5_role') }}</p>
                                        </div>
                                    </div>
                                    <div class="link-icon"><i class="icon-plus"></i></div>
                                </a>
                            </div>
                        </div> <!-- item end -->
                        <div class="columns portfolio-item">
                            <div class="item-wrap">
                                <a href="#modal-07">
                                    <img alt="THU" class="portfolio-image" src="images/personal/trojan.jpg">
                                    <div class="overlay">
                                        <div class="portfolio-item-meta">
                                            <h5>{{ __('welcome.Project4') }}</h5>
                                            <p>{{ __('welcome.Project4_role') }}</p>
                                        </div>
                                    </div>
                                    <div class="link-icon"><i class="icon-plus"></i></div>
                                </a>
                            </div>
                        </div> <!-- item end -->
                        <div class="columns portfolio-item">
                            <div class="item-wrap">
                                <a href="#modal-08">
                                    <img alt="Gatewit - Warehouse" class="portfolio-image" src="images/personal/logo_wh.png">
                                    <div class="overlay">
                                        <div class="portfolio-item-meta">
                                            <h5>{{ __('welcome.Project3') }}</h5>
                                            <p>{{ __('welcome.Project3_role') }}</p>
                                        </div>
                                    </div>
                                    <div class="link-icon"><i class="icon-plus"></i></div>
                                </a>
                            </div>
                        </div>  <!-- item end -->
                        <div class="columns portfolio-item">
                            <div class="item-wrap">
                                <a href="#modal-09">
                                    <img alt="Gatewit - Requisition" class="portfolio-image" src="images/personal/logo_req.png">
                                    <div class="overlay">
                                        <div class="portfolio-item-meta">
                                            <h5>{{ __('welcome.Project2') }}</h5>
                                            <p>{{ __('welcome.Project2_role') }}</p>
                                        </div>
                                    </div>
                                    <div class="link-icon"><i class="icon-plus"></i></div>
                                </a>
                            </div>
                        </div>  <!-- item end -->
                        <div class="columns portfolio-item">
                            <div class="item-wrap">
                                <a href="#modal-10">
                                    <img alt="Gatewit - Compras Públicas" class="portfolio-image" src="images/personal/compras2.png">
                                    <div class="overlay">
                                        <div class="portfolio-item-meta">
                                            <h5>{{ __('welcome.Project1') }}</h5>
                                            <p>{{ __('welcome.Project1_role') }}</p>
                                        </div>
                                    </div>
                                    <div class="link-icon"><i class="icon-plus"></i></div>
                                </a>
                            </div>
                        </div>  <!-- item end -->
                    </div> <!-- portfolio-wrapper end -->
                </div> <!-- twelve columns end -->

                <!-- Modal Popup
                 --------------------------------------------------------------- -->
                <div id="modal-01" class="popup-modal mfp-hide">
                    <img class="scale-with-grid portfolio-image-modal" src="images/personal/imo-det.png" alt="Imovirtual modal" />
                    <div class="description-box">
                        <h4>{{ __('welcome.Project10') }}</h4>
                        <p>{{ __('welcome.Project10_desc_textarea') }}</p>
                        <span class="categories"><i class="fa fa-tag"></i>{{ __('welcome.Project10_role') }}</span>
                    </div>
                    <div class="link-box">
                        <a href="https://www.imovirtual.com" target="_blank">{{ __('welcome.Site') }}</a>
                        <a class="popup-modal-dismiss">{{ __('welcome.Close') }}</a>
                    </div>
                </div><!-- modal-01 End -->
                <div id="modal-02" class="popup-modal mfp-hide">
                    <img class="scale-with-grid portfolio-image-modal" src="images/personal/otodom-det.png" alt="Otodom modal" />
                    <div class="description-box">
                        <h4>{{ __('welcome.Project9') }}</h4>
                        <p>{{ __('welcome.Project9_desc_textarea') }}</p>
                        <span class="categories"><i class="fa fa-tag"></i>{{ __('welcome.Project9_role') }}</span>
                    </div>
                    <div class="link-box">
                        <a href="https://www.otodom.pl" target="_blank">{{ __('welcome.Site') }}</a>
                        <a class="popup-modal-dismiss">{{ __('welcome.Close') }}</a>
                    </div>
                </div><!-- modal-02 End -->
                <div id="modal-03" class="popup-modal mfp-hide">
                    <img class="scale-with-grid portfolio-image-modal" src="images/personal/storia-det.jpg" alt="Storia modal" />
                    <div class="description-box">
                        <h4>{{ __('welcome.Project8') }}</h4>
                        <p>{{ __('welcome.Project8_desc_textarea') }}</p>
                        <span class="categories"><i class="fa fa-tag"></i>{{ __('welcome.Project8_role') }}</span>
                    </div>
                    <div class="link-box">
                        <a href="https://www.storia.ro" target="_blank">{{ __('welcome.Site') }}</a>
                        <a class="popup-modal-dismiss">{{ __('welcome.Close') }}</a>
                    </div>
                </div><!-- modal-03 End -->
                <div id="modal-04" class="popup-modal mfp-hide">
                    <img class="scale-with-grid portfolio-image-modal" src="images/personal/as-det2.png" alt="Atlastock modal" />
                    <div class="description-box">
                        <h4>{{ __('welcome.Project7') }}</h4>
                        <p>{{ __('welcome.Project7_desc_textarea') }}</p>
                        <span class="categories"><i class="fa fa-tag"></i>{{ __('welcome.Project7_role') }}</span>
                    </div>
                    <div class="link-box">
                        <span class="portfolio-modal-deprecated-site">{{ __('welcome.Deprecated') }}</span>
                        <a class="popup-modal-dismiss">{{ __('welcome.Close') }}</a>
                    </div>
                </div><!-- modal-04 End -->
                <div id="modal-05" class="popup-modal mfp-hide">
                    <img class="scale-with-grid portfolio-image-modal" src="images/personal/gwr-det.jpg" alt="Young Great Westerners! modal" />
                    <div class="description-box">
                        <h4>{{ __('welcome.Project6') }}</h4>
                        <p>{{ __('welcome.Project6_desc_textarea') }}</p>
                        <span class="categories"><i class="fa fa-tag"></i>{{ __('welcome.Project6_role') }}</span>
                    </div>
                    <div class="link-box">
                        <a href="https://www.younggreatwesterners.com/" target="_blank">{{ __('welcome.Site') }}</a>
                        <a class="popup-modal-dismiss">{{ __('welcome.Close') }}</a>
                    </div>
                </div><!-- modal-05 End -->
                <div id="modal-06" class="popup-modal mfp-hide">
                    <img class="scale-with-grid portfolio-image-modal" src="images/personal/pa-det.jpg" alt="PA Mysite modal" />
                    <div class="description-box">
                        <h4>{{ __('welcome.Project5') }}</h4>
                        <p>{{ __('welcome.Project5_desc_textarea') }}</p>
                        <span class="categories"><i class="fa fa-tag"></i>{{ __('welcome.Project5_role') }}</span>
                    </div>
                    <div class="link-box">
                        <span class="portfolio-modal-deprecated-site">{{ __('welcome.Deprecated') }}</span>
                        <a class="popup-modal-dismiss">{{ __('welcome.Close') }}</a>
                    </div>
                </div><!-- modal-06 End -->
                <div id="modal-07" class="popup-modal mfp-hide">
                    <img class="scale-with-grid portfolio-image-modal" src="images/personal/trojan-det.jpg" alt="THU modal" />
                    <div class="description-box">
                        <h4>{{ __('welcome.Project4') }}</h4>
                        <p>{{ __('welcome.Project4_desc_textarea') }}</p>
                        <span class="categories"><i class="fa fa-tag"></i>{{ __('welcome.Project4_role') }}</span>
                    </div>
                    <div class="link-box">
                        <a href="https://www.trojan-unicorn.com/" target="_blank">{{ __('welcome.Site') }}</a>
                        <a class="popup-modal-dismiss">{{ __('welcome.Close') }}</a>
                    </div>
                </div><!-- modal-07 End -->
                <div id="modal-08" class="popup-modal mfp-hide">
                    <img class="scale-with-grid portfolio-image-modal" src="images/personal/logo_wh-det.png" alt="Gatewit - Warehouse modal" />
                    <div class="description-box">
                        <h4>{{ __('welcome.Project3') }}</h4>
                        <p>{{ __('welcome.Project3_desc_textarea') }}</p>
                        <span class="categories"><i class="fa fa-tag"></i>{{ __('welcome.Project3_role') }}</span>
                    </div>
                    <div class="link-box">
                        <span class="portfolio-modal-deprecated-site">{{ __('welcome.Deprecated') }}</span>
                        <a class="popup-modal-dismiss">{{ __('welcome.Close') }}</a>
                    </div>
                </div><!-- modal-08 End -->
                <div id="modal-09" class="popup-modal mfp-hide">
                    <img class="scale-with-grid portfolio-image-modal" src="images/personal/logo_req-det.png" alt="Gatewit - Requisition modal" />
                    <div class="description-box">
                        <h4>{{ __('welcome.Project2') }}</h4>
                        <p>{{ __('welcome.Project2_desc_textarea') }}</p>
                        <span class="categories"><i class="fa fa-tag"></i>{{ __('welcome.Project2_role') }}</span>
                    </div>
                    <div class="link-box">
                        <span class="portfolio-modal-deprecated-site">{{ __('welcome.Deprecated') }}</span>
                        <a class="popup-modal-dismiss">{{ __('welcome.Close') }}</a>
                    </div>
                </div><!-- modal-09 End -->
                <div id="modal-10" class="popup-modal mfp-hide">
                    <img class="scale-with-grid portfolio-image-modal" src="images/personal/compras-det.png" alt="Gatewit - Compras Públicas modal" />
                    <div class="description-box">
                        <h4>{{ __('welcome.Project1') }}</h4>
                        <p>{{ __('welcome.Project1_desc_textarea') }}</p>
                        <span class="categories"><i class="fa fa-tag"></i>{{ __('welcome.Project1_role') }}</span>
                    </div>
                    <div class="link-box">
                        <span class="portfolio-modal-deprecated-site">{{ __('welcome.Deprecated') }}</span>
                        <a class="popup-modal-dismiss">{{ __('welcome.Close') }}</a>
                    </div>
                </div><!-- modal-10 End -->
                <div id="modal-11" class="popup-modal mfp-hide">
                    <img class="scale-with-grid portfolio-image-modal" src="images/personal/routineris-det.png" alt="Routineris modal" />
                    <div class="description-box">
                        <h4>{{ __('welcome.Project11') }}<br/> <span style="color:orange; font-size: 12px">{{ __('welcome.Project11_migration') }}</span> </h4>
                        <p>{{ __('welcome.Project11_desc_textarea') }} </p>
                        <span class="categories"><i class="fa fa-tag"></i>{{ __('welcome.Project11_role') }}</span>
                    </div>
                    <div class="link-box">
                        <span class="portfolio-modal-deprecated-site">{{ __('welcome.Temporary_unavailable') }}</span>
                        <!-- <a href="https://routineris.xyz/" target="_blank">Site</a> -->
                        <a class="popup-modal-dismiss">{{ __('welcome.Close') }}</a>
                    </div>
                </div><!-- modal-11 End -->
                <div id="modal-12" class="popup-modal mfp-hide">
                    <img class="scale-with-grid portfolio-image-modal" src="images/personal/logo-det2.png" alt="José Gomes site modal" />
                    <div class="description-box">
                        <h4>{{ __('welcome.Project12') }}</h4>
                        <p> {{ __('welcome.Project12_desc_textarea') }}</p>
                        <span class="categories"><i class="fa fa-tag"></i>{{ __('welcome.Project12_role') }}</span>
                    </div>
                    <div class="link-box">
                        <a href="https://jgomes.site" target="_blank">{{ __('welcome.Site') }}</a>
                        <a class="popup-modal-dismiss">{{ __('welcome.Close') }}</a>
                    </div>
                </div><!-- modal-12 End -->
            </div> <!-- row End -->
        </section> <!-- Portfolio Section End-->

        <!-- WOW Section
        ================================================== -->
        <section id="testimonials">
            <div class="text-container">
                <div class="row">
                    <div class="two columns header-col">
                        <h1><span>{{ __('welcome.WOW') }}</span></h1>
                    </div>
                    <div class="ten columns flex-container">
                        <div class="flexslider">
                            <p class="case-studies">
                                {{ __('welcome.WOW_desc_textarea') }} : <br/><br/>
                                - {{ __('welcome.WOW_info1') }}:
                                <a target="_blank" href="https://github.com/jfgomes/site-jgomes/blob/master/README.md">
                                    <u>{{ __('welcome.Here') }}</u>
                                </a> <br/>
                                - {{ __('welcome.WOW_info2') }}:
                                <a target="_blank" href="https://trello.com/b/zOuG1loa/j-gomes-site">
                                    <u>{{ __('welcome.Here') }}</u>
                                </a> <br />
                                - {{ __('welcome.WOW_info3') }}:
                                <a href="/api/documentation#/Message" target="_blank">
                                    <u>{{ __('welcome.Here') }}</u>
                                </a> <br />
                                - {{ __('welcome.WOW_info4') }}:
                                <a href="/coverage-report/index.html" target="_blank">
                                    <u>{{ __('welcome.Here') }}</u>
                                </a> <br />
                                - {{ __('welcome.WOW_info5') }}:
                                <a href="/case-studies" target="_blank">
                                    <u>{{ __('welcome.Here') }}</u>
                                </a> <br />
                                - {{ __('welcome.WOW_info6') }}:
                                <a href="/details" target="_blank">
                                    <u>{{ __('welcome.Here') }}</u>
                                </a> <br />
                                - {{ __('welcome.WOW_info7') }}:
                                <a href="https://jjenkins.xyz/" target="_blank">
                                    <u>{{ __('welcome.Here') }}</u>
                                </a> <br />
                            </p>
                        </div> <!-- div.flexslider ends -->
                    </div> <!-- div.flex-container ends -->
                </div> <!-- row ends -->
            </div>  <!-- text-container ends -->
        </section> <!-- WOW Section End-->

        <!-- Contact Section
        ================================================== -->
        <section id="contact">
            <div class="row section-head">
                <div class="two columns header-col">
                    <h1><span>{{ __('welcome.Contact') }}</span></h1>
                </div>
                <div class="ten columns">
                    <p class="lead text-left">
                        {{ __('welcome.Contact_desc_textarea') }}
                    </p>
                </div>
            </div>
            <div class="row">
                <div class="eight columns">
                    <!-- form -->
                    <form action="/api/send" method="post" id="contactForm" name="contactForm">
                        @csrf
                        <fieldset>
                            <div>
                                <label for="name"> {{ __('welcome.Contact_name') }} <span class="required">*</span></label>
                                <input type="text" value="" size="35" id="name" name="contactName">
                            </div>
                            <div>
                                <label for="email"> {{ __('welcome.Contact_email') }} <span class="required">*</span></label>
                                <input type="text" value="" size="35" id="email" name="contactEmail">
                            </div>
                            <div>
                                <label for="subject"> {{ __('welcome.Contact_subject') }} </label>
                                <input type="text" value="" size="35" id="subject" name="contactSubject">
                            </div>
                            <div>
                                <label for="content"> {{ __('welcome.Contact_message') }} <span class="required">*</span></label>
                                <div class="chatCount">
                                    <span id="chatCount"></span>
                                </div>
                                <textarea class="contactMessageExtra" cols="50" rows="15" id="content" name="contactMessage"></textarea>
                            </div>
                            <div>
                                <button type="submit" class="submit">{{ __('welcome.Contact_submit') }} </button>
                                <span id="image-loader">
                                    <img alt="" src="images/loader.gif">
                                </span>
                            </div>
                        </fieldset>
                    </form> <!-- Form End -->
                    <!-- contact-warning -->
                    <div id="message-warning"></div>
                    <!-- contact-success -->
                    <div id="message-success">
                        <i class="fa fa-check"></i>{{ __('welcome.Contact_sent_success') }} <br>
                    </div>
                </div>
                <aside class="four columns footer-widgets">
                    <div class="widget widget_contact">
                        <h4>{{ __('welcome.Contact') }}</h4>
                        <p class="address">
                            José Gomes<br>
                            {{ __('welcome.City') }}<br>
                            <span>+351937523892</span>
                        </p>
                    </div>
                </aside>
            </div>
        </section> <!-- Contact Section End -->

        <!-- footer
        ================================================== -->
        <footer>
            <div class="row">
                <div class="twelve columns">
                    <ul class="social-links">
                        <li>
                            <a href="https://twitter.com/routineris" target="_blank">
                                <i class="fa fa-twitter social_extra"></i>
                            </a>
                        </li>
                        <li>
                            <a href="https://www.linkedin.com/in/jos%C3%A9-gomes-3ba05869/" target="_blank">
                                <i class="fa fa-linkedin social_extra"></i>
                            </a>
                        </li>
                        <li>
                            <a href="https://github.com/jfgomes?tab=repositories" target="_blank">
                                <i class="fa fa-github social_extra"></i>
                            </a>
                        </li>
                        <li>
                            <a href="https://www.youtube.com/@JGomes-dev/videos" target="_blank">
                                <i class="fa fa-youtube social_extra"></i>
                            </a>
                        </li>
                    </ul>
                    <a href="https://www.codewars.com/users/JGomes87" target="_blank"><img alt="Dark Badge (large)" class="hidden dark:block" src="https://www.codewars.com/users/JGomes87/badges/large"></a>
                </div>
                <div id="go-top">
                    <a class="smoothscroll" title="Back to Top" href="#home">
                        <i class="icon-up-open"></i>
                    </a>
                </div>
            </div>
            <footer>
                @include('partials.cookies')
            </footer> <!-- Footer End-->
        </footer> <!-- Footer End-->

        <!-- Java Script
        ================================================== -->
        <script src="http://ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js"></script>
        <script>window.jQuery || document.write('<script src="js/jquery-1.10.2.min.js"><\/script>')</script>
        <script type="text/javascript" src="js/jquery-migrate-1.2.1.min.js"></script>
        <script src="js/jquery.flexslider.js"></script>
        <script src="js/waypoints.js"></script>
        <script src="js/jquery.fittext.js"></script>
        <script src="js/magnific-popup.js"></script>
        <script src="js/local/public/init.js"></script>
        <script src="{{ (app()->environment() === 'prod')
                            ? mix('js/prod/app.js')
                            : 'js/local/public/init.js' }}">
        </script>
        <script src="/js/cookies.js"></script>
        <script src="js/local/public/general.js"></script>
    </body>
</html>

