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

    <!-- Script
    ================================================== -->
    <script src="js/modernizr.js"></script>

    <!-- Favicons
     ================================================== -->
    <link rel="shortcut icon" href="favicon.png" >

    <script>
        function downloadCV(urlDocument)
        {
            let link  = document.createElement('a');
            link.href = urlDocument;
            link.download = urlDocument;
            link.click();
        }
    </script>

</head>

<body>

<!-- Header
================================================== -->
<header id="home">

    <nav id="nav-wrap">

        <a class="mobile-btn" href="#nav-wrap" title="Show navigation">Show navigation</a>
        <a class="mobile-btn" href="#" title="Hide navigation">Hide navigation</a>

        <ul id="nav" class="nav">
            <li class="current"><a class="smoothscroll" href="#home">Home</a></li>
            <li><a class="smoothscroll" href="#about">About</a></li>
            <li><a class="smoothscroll" href="#resume">Resume</a></li>
            <li><a class="smoothscroll" href="#portfolio">Projects</a></li>
            <li><a class="smoothscroll" href="#testimonials">Case studies</a></li>
            <li><a class="smoothscroll" href="#contact">Contact</a></li>
        </ul> <!-- end #nav -->

    </nav> <!-- end #nav-wrap -->

    <div class="row banner">
        <style>
            .maintenance-mode {
                background-color: #ff6347;
                color: #fff;
                padding: 10px 0 15px 0;
                text-align: center;
                font-size: 18px;
                margin-bottom: 25px;
            }

            .maintenance-mode p {
                margin: 0;
            }

            .maintenance-mode::before {
                content: "⚠️";
                font-size: 24px;
                margin-right: 10px;
            }
        </style>
        @if(app()->isDownForMaintenance())
            <div class="maintenance-mode">
                <p>The application is currently under maintenance! <br /> Is only visible for you and all the other users have the application under maintenance mode page.</p>
            </div>
        @endif
        <div class="banner-text">
            <a href="https://www.codewars.com/users/JGomes87" target="_blank"><img alt="Dark Badge (large)" class="hidden dark:block" src="https://www.codewars.com/users/JGomes87/badges/large"></a>
            <h1 class="responsive-headline">José Gomes</h1>
            <script type="text/javascript"> //<![CDATA[
                var tlJsHost = ((window.location.protocol == "https:") ? "https://secure.trust-provider.com/" : "http://www.trustlogo.com/");
                document.write(unescape("%3Cscript src='" + tlJsHost + "trustlogo/javascript/trustlogo.js' type='text/javascript'%3E%3C/script%3E"));
                //]]></script>
            <script language="JavaScript" type="text/javascript">
                TrustLogo("https://www.positivessl.com/images/seals/positivessl_trust_seal_md_167x42.png", "POSDV", "none");
            </script> <br/>
            <h3>I'm a Lisbon based <span>software developer</span>.<br/><span> Let's <a class="smoothscroll" href="#about">start scrolling</a> and know more <a class="smoothscroll" href="#about">about me</a>.</span>
            </h3>
            <hr />

            <ul class="social">
                <li><a href="https://twitter.com/routineris" target="_blank"><i class="fa fa-twitter social_extra"></i></a></li>
                <li><a href="https://www.linkedin.com/in/jos%C3%A9-gomes-3ba05869/" target="_blank"><i class="fa fa-linkedin social_extra"></i></a></li>
                <li><a href="https://github.com/jfgomes?tab=repositories" target="_blank"><i class="fa fa-github social_extra"></i></a></li>
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

            <h2>About Me</h2>

            <p> I'm a software developer passionate about open source technologies, agile methodologies and helping the product achieve its proposed objectives.
                I don't consider myself as a technology CR7 or a guru, I'm always learning. Is mandatory to me deliver code without errors,
                well tested and developed, always offering the best possible quality in my work. Over the years, I have become more customer-focused,
                in the performance offered to them and in the best possible planning to delivery the tasks,
                contributing to product quality. Legacy code doesn't scare me, in fact I see everything as a challenge and opportunity for improvement.
            </p>

            <div class="row">

                <div class="columns contact-details">

                    <h2>Contact Details</h2>
                    <p class="address">
                        <span>José Gomes</span><br>
                        <span>Lisbon</span><br>
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
            <h1><span>Education</span></h1>
        </div>

        <div class="nine columns main-col">

            <div class="row item">

                <div class="twelve columns">
                    <img width="65" height="65" src="images/personal/olx_group_logo.jpeg">
                    <h3>Training in company, Lisbon</h3>
                    <p class="info">PHPUnit with Sebastian Bergmann<span>&bull;</span> <em class="date">2017</em></p>
                </div>

            </div> <!-- item end -->
            <br/>
            <div class="row item">

                <div class="twelve columns">
                    <img width="50" height="50" src="images/personal/edit.png">
                    <h3><i>EDIT School</i>, Lisbon</h3>
                    <p class="info">Workshop / UX - User Experience<span>&bull;</span> <em class="date">2015</em></p>
                </div>

            </div> <!-- item end -->
            <br/>
            <div class="row item">

                <div class="twelve columns">
                    <img width="50" height="50" src="images/personal/doitbetter.jpeg">
                    <h3><i>Do It Better - Training center</i>, Amadora</h3>
                    <p class="info">IEFP Training of Trainers Certification - CCP (formerly CAP)<span>&bull;</span> <em class="date">2014</em></p>
                    <p>
                        - CCP no. F629584/2014
                    </p>
                </div>

            </div> <!-- item end -->

            <div class="row item">

                <div class="twelve columns">
                    <img width="60" height="60" src="images/personal/ipt.png" />
                    <h3><i>Higher School of Technology</i>, Tomar</h3>
                    <p class="info">Degree in computer engineering<span>&bull;</span> <em class="date">2006 - 2010</em></p>
                </div>

            </div> <!-- item end -->

        </div> <!-- main-col end -->

    </div> <!-- End Education -->


    <!-- Work
    ----------------------------------------------- -->
    <div class="row work">

        <div class="three columns header-col">
            <h1><span>Work</span></h1>
        </div>

        <div class="nine columns main-col">

            <div class="row item">

                <div class="twelve columns">
                    <img width="65" height="65" src="images/personal/olx_group_logo.jpeg" />
                    <h3>OLX</h3>
                    <p class="info">Software Developer <span>&bull;</span> <em class="date">2016 - Present</em></p>

                    <p>
                        During my initial tenure (one year and a half) at the company, I worked on business tools for real estate consultants.
                        With the company's acquisition by OLX, I shifted my focus to managing public real estate portals, working and adapting within the industry.
                        In this role, I ensured the effective implementation of Scrum principles and ceremonies in our iterations, guaranteed proper task planning,
                        and contributed as a developer in implementing new features. I handled bug corrections, hot fixes, and maintained the portals continuously.
                        I belonged to the team responsible for all location-related information across real estate portals such as Imovirtual, Storia and Otodom.pl.
                        When necessary, I conduct demonstrations of new features and improvements for other teams and managers.
                    </p>

                </div>

            </div> <!-- item end -->

            <div class="row item">

                <div class="twelve columns">
                    <img width="50" height="50" src="images/personal/bk_group_logo.jpeg" />
                    <h3>BLKO</h3>
                    <p class="info">Software Developer<span>&bull;</span> <em class="date">2014 - 2016</em></p>

                    <p>
                        Worked as a developer in a startup-minded company, primarily focused on developing projects to generate funding
                        for the company's main project named THO (Trojan Worse was an Unicorn).
                        Engaged in full-stack development.
                        Created turnkey portals operating in an offshore engineering mode for external companies like "Great Western Railway" and others.
                        Developed projects for internal companies such as "Yellowpages" and "Caminho.
                    </p>

                </div>

            </div> <!-- item end -->

            <div class="row item">

                <div class="twelve columns">
                    <img width="50" height="50" src="images/personal/gw.jpeg" />
                    <h3>Gatewit</h3>
                    <p class="info">Software Developer<span>&bull;</span> <em class="date">2010 - 2014</em></p>

                    <p>
                        Worked as developer at compraspublicas.com, a government-selected portal for state public procurement,
                        enabling supplier competition and proposal submissions. ( site deprecated due to company closure )
                        and warehouse.procurement.com developing for "Nextrans" to streamline transportation registrations,
                        logistics, supply chain, and warehousing. ( site deprecated due to company closure )
                        My Responsibilities here was guarantee full-stack development, implementing, new features, satellite portals and bug fixing.
                    </p>

                </div>

            </div> <!-- item end -->

        </div> <!-- main-col end -->

    </div> <!-- End Work -->


    <!-- Skills
    ----------------------------------------------- -->
    <div class="row skill">

        <div class="three columns header-col">
            <h1><span>Skills</span></h1>
        </div>

        <div class="nine columns main-col">

            <p>
                The following skills and respective quotation ( bar width ) are based on the technologies used in the projects I am/was involved plus the training I had in the projects context.
                It reflects my experience in real projects and not in what quotation I should have in each tech.
                Indicative values and in some technologies may be based on my professional experience and not on total technical knowledge.
            </p>

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

<!-- Call-To-Action Section
================================================== -->
<section id="call-to-action">

    <div class="row">

        <div class="two columns header-col">

            <h1><span></span></h1>

        </div>

        <div class="seven columns">

            <h2><span class="lead">Preferred stack / tools:</span></h2>
            <p class="text-left">
                <span class="lead">
                <img src="/images/personal/logos/ubuntu.png" /> linux (ubuntu) <br>
                <img src="/images/personal/logos/phpstorm.png" width="32px" height="32px" /> phpstorm<br>
                <img src="/images/personal/logos/workb.png" /> mysql workbench<br>
                <img src="/images/personal/logos/phpmyadmin.png" width="32px" height="40px" /> phpmyadmin<br>
                <img src="/images/personal/logos/docker.png" width="32px" height="32px" /> docker desktop<br>
                <img src="/images/personal/logos/postman.png" /> postman<br>
                <img src="/images/personal/logos/zoom.png" /> zoom<br>
                <img src="/images/personal/logos/apache.png" /> apache2<br>
                <img src="/images/personal/logos/gc.png" width="32px" height="32px" /> gcp buckets<br>
                <img src="/images/personal/logos/gimp.png" /> gimp <br>
                <img src="/images/personal/logos/slack.png" /> slack <br>
                - etc...<br>
                </span>
            </p>

        </div>

        <div class="three columns action">

        </div>

    </div>

</section> <!-- Call-To-Action Section End-->

<!-- Portfolio Section
================================================== -->
<section id="portfolio">

    <div class="row">

        <div class="twelve columns collapsed">

            <h1 class="portfolio-text-size">Projects</h1>

            <!-- portfolio-wrapper -->
            <div id="portfolio-wrapper" class="bgrid-quarters s-bgrid-thirds cf">

                <div class="columns portfolio-item">
                    <div class="item-wrap">
                        <a href="#modal-12">
                            <img alt="José Gomes Site" class="portfolio-image" src="images/personal/logo3.png">
                            <div class="overlay">
                                <div class="portfolio-item-meta">
                                    <h5>José Gomes Site</h5>
                                    <p>Web Development</p>
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
                                    <h5>Routineris</h5>
                                    <p>Web Development</p>
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
                                    <h5>Imovirtual</h5>
                                    <p>Web Development</p>
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
                                    <h5>Otodom</h5>
                                    <p>Web Development</p>
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
                                    <h5>Storia</h5>
                                    <p>Web Development</p>
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
                                    <h5>Atlastock</h5>
                                    <p>Web Development</p>
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
                                    <h5>Young Great Westerners!</h5>
                                    <p>Web Development</p>
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
                                    <h5>PA MySite</h5>
                                    <p>Web Development</p>
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
                                    <h5>THU</h5>
                                    <p>Web Development</p>
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
                                    <h5>Gatewit - Warehouse</h5>
                                    <p>Web Development</p>
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
                                    <h5>Gatewit - Requisition</h5>
                                    <p>Web Development</p>
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
                                    <h5>Gatewit - Compras Públicas</h5>
                                    <p>Web Development</p>
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
                <h4>Imovirtual</h4>
                <p>
                    This application is an online portal specialized in real estate classifieds,
                    where we can find advertisements for the sale and rental of properties, such as houses, apartments,
                    land, among others, for the Portuguese market.
                </p>
                <span class="categories"><i class="fa fa-tag"></i>Web Development</span>
            </div>

            <div class="link-box">
                <a href="https://www.imovirtual.com" target="_blank">Site</a>
                <a class="popup-modal-dismiss">Close</a>
            </div>

        </div><!-- modal-01 End -->

        <div id="modal-02" class="popup-modal mfp-hide">

            <img class="scale-with-grid portfolio-image-modal" src="images/personal/otodom-det.png" alt="Otodom modal" />

            <div class="description-box">
                <h4>Otodom</h4>
                <p>
                    This application is an online portal specialized in real estate classifieds,
                    where we can find advertisements for the sale and rental of properties, such as houses, apartments,
                    land, among others, for the Polish market.
                </p>
                <span class="categories"><i class="fa fa-tag"></i>Web Development</span>
            </div>

            <div class="link-box">
                <a href="https://www.otodom.pl" target="_blank">Site</a>
                <a class="popup-modal-dismiss">Close</a>
            </div>

        </div><!-- modal-02 End -->

        <div id="modal-03" class="popup-modal mfp-hide">

            <img class="scale-with-grid portfolio-image-modal" src="images/personal/storia-det.jpg" alt="Storia modal" />

            <div class="description-box">
                <h4>Storia</h4>
                <p>
                    This application is an online portal specialized in real estate classifieds,
                    where we can find advertisements for the sale and rental of properties, such as houses, apartments,
                    land, among others, for the Romanian market.
                </p>
                <span class="categories"><i class="fa fa-tag"></i>Web Development</span>
            </div>

            <div class="link-box">
                <a href="https://www.storia.ro" target="_blank">Details</a>
                <a class="popup-modal-dismiss">Close</a>
            </div>

        </div><!-- modal-03 End -->

        <div id="modal-04" class="popup-modal mfp-hide">

            <img class="scale-with-grid portfolio-image-modal" src="images/personal/as-det2.png" alt="Atlastock modal" />

            <div class="description-box">
                <h4>Atlastock</h4>
                <p> Application that was a business tool for real estate, which allowed consultants to manage their
                    properties in their portfolio. One of the main features was the exportation of properties to Imovirtual
                    and/or OLX, transforming this content into advertisements visible to the public user.</p>
                <span class="categories"><i class="fa fa-tag"></i>Web Development</span>
            </div>

            <div class="link-box">
                <span class="portfolio-modal-deprecated-site">Deprecated</span>
                <a class="popup-modal-dismiss">Close</a>
            </div>

        </div><!-- modal-04 End -->

        <div id="modal-05" class="popup-modal mfp-hide">

            <img class="scale-with-grid portfolio-image-modal" src="images/personal/gwr-det.jpg" alt="Young Great Westerners! modal" />

            <div class="description-box">
                <h4>Young Great Westerners!</h4>
                <p> Application for a British railway company for a project associated with
                    public education where the goal is to publicize the company and at the same time giving
                    the possibility to students from various schools to be aware about the points of interest in Great Britain that are
                    accessible by traveling on the British train network. </p>
                <span class="categories"><i class="fa fa-tag"></i>Web Development</span>
            </div>

            <div class="link-box">
                <a href="https://www.younggreatwesterners.com/" target="_blank">Site</a>
                <a class="popup-modal-dismiss">Close</a>
            </div>

        </div><!-- modal-05 End -->

        <div id="modal-06" class="popup-modal mfp-hide">

            <img class="scale-with-grid portfolio-image-modal" src="images/personal/pa-det.jpg" alt="PA Mysite modal" />

            <div class="description-box">
                <h4>PA Mysite</h4>
                <p>
                    Portal for company information and contacts. Custom web page generator for PA customers through the
                    mysite-page solution instantly.
                </p>
                <span class="categories"><i class="fa fa-tag"></i>Web Development</span>
            </div>

            <div class="link-box">
                <span class="portfolio-modal-deprecated-site">Deprecated</span>
                <a class="popup-modal-dismiss">Close</a>
            </div>

        </div><!-- modal-06 End -->

        <div id="modal-07" class="popup-modal mfp-hide">

            <img class="scale-with-grid portfolio-image-modal" src="images/personal/trojan-det.jpg" alt="THU modal" />

            <div class="description-box">
                <h4>THU</h4>
                <p>
                    Application that serves as a communication platform of articles, initiatives and announces
                    the main event ( THU ) that combines conferences, festival and meeting for professionals in digital media,
                    animation, visual effects, design and related areas that aims to bring together talented and inspiring artists.
                </p>
                <span class="categories"><i class="fa fa-tag"></i>Web Development</span>
            </div>

            <div class="link-box">
                <a href="https://www.trojan-unicorn.com/" target="_blank">Site</a>
                <a class="popup-modal-dismiss">Close</a>
            </div>

        </div><!-- modal-07 End -->

        <div id="modal-08" class="popup-modal mfp-hide">

            <img class="scale-with-grid portfolio-image-modal" src="images/personal/logo_wh-det.png" alt="Gatewit - Warehouse modal" />

            <div class="description-box">
                <h4>Gatewit - Warehouse</h4>
                <p>
                    Application to manage inventory controlling prices and costs of various items, creating alerts for stock outages, creating
                    orders and preparation of purchase orders. Allows management and control of movements.
                    Customized usage profiles per user. History of all actions performed and their respective users.
                </p>
                <span class="categories"><i class="fa fa-tag"></i>Web Development</span>
            </div>

            <div class="link-box">
                <span class="portfolio-modal-deprecated-site">Deprecated</span>
                <a class="popup-modal-dismiss">Close</a>
            </div>

        </div><!-- modal-08 End -->

        <div id="modal-09" class="popup-modal mfp-hide">

            <img class="scale-with-grid portfolio-image-modal" src="images/personal/logo_req-det.png" alt="Gatewit - Requisition modal" />

            <div class="description-box">
                <h4>Gatewit - Requisition</h4>
                <p>
                    Application for product management, orders, purchase orders, stocks
                    and requests.
                    It allows different degrees of access and permissions per user depending on their profile.
                    Integration with Public Sourcing to launch procedures and treatment of awards.
                </p>
                <span class="categories"><i class="fa fa-tag"></i>Web Development</span>
            </div>

            <div class="link-box">
                <span class="portfolio-modal-deprecated-site">Deprecated</span>
                <a class="popup-modal-dismiss">Close</a>
            </div>

        </div><!-- modal-09 End -->

        <div id="modal-10" class="popup-modal mfp-hide">

            <img class="scale-with-grid portfolio-image-modal" src="images/personal/compras-det.png" alt="Gatewit - Compras Públicas modal" />

            <div class="description-box">
                <h4>Gatewit - Compras Públicas</h4>
                <p>
                    Management platform and procedures for contracting goods and services. Through this application, suppliers could consult
                    public advertisements and present their offers and associated value. Also through this application,
                    all offers were analyzed to obtain the award of the good or service.
                </p>
                <span class="categories"><i class="fa fa-tag"></i>Web Development</span>
            </div>

            <div class="link-box">
                <span class="portfolio-modal-deprecated-site">Deprecated</span>
                <a class="popup-modal-dismiss">Close</a>
            </div>

        </div><!-- modal-10 End -->

        <div id="modal-11" class="popup-modal mfp-hide">

            <img class="scale-with-grid portfolio-image-modal" src="images/personal/routineris-det.png" alt="Routineris modal" />

            <div class="description-box">
                <h4>Routineris</h4>
                <p>
                    Website to save photos in the cloud.
                    Allows the user to create maps ( which we can call it albums ) where you can invite other people
                    to contribute with their photos to the map. At the end, this application works as a family social network.
                </p>
                <span class="categories"><i class="fa fa-tag"></i>Web Development</span>
            </div>

            <div class="link-box">
                <a href="https://routineris.xyz/" target="_blank">Site</a>
                <a class="popup-modal-dismiss">Close</a>
            </div>

        </div><!-- modal-11 End -->

        <div id="modal-12" class="popup-modal mfp-hide">

            <img class="scale-with-grid portfolio-image-modal" src="images/personal/logo-det2.png" alt="José Gomes site modal" />

            <div class="description-box">
                <h4>José Gomes site</h4>
                <p>
                    Personal website where I show my CV, the technologies I like most,
                    the ones I had the opportunity to work on and also some case studies of some features that
                    I can't demonstrate on sites where I don't have rights to show the code.
                </p>
                <span class="categories"><i class="fa fa-tag"></i>Web Development</span>
            </div>

            <div class="link-box">
                <a href="https://jgomes.site" target="_blank">Site</a>
                <a class="popup-modal-dismiss">Close</a>
            </div>

        </div><!-- modal-12 End -->

    </div> <!-- row End -->

</section> <!-- Portfolio Section End-->

<!-- Testimonials Section
================================================== -->
<section id="testimonials">

    <div class="text-container">

        <div class="row">

            <div class="two columns header-col">

                <!-- <h1><span>Case studies</span></h1> -->

            </div>

            <div class="ten columns flex-container">

                <div class="flexslider">

                    <p class="case-studies">
                        I'm using this website to show some of my WOW as the majority of the projects I worked / I'm working for are copyrighted.
                        So you can have more details about this site with read access to: <br/><br/>
                        - The repo: <a target="_blank" href="https://github.com/jfgomes/site-jgomes/blob/master/README.md"><u>Repository here</u></a> <br/>
                        - The board: <a target="_blank" href="https://trello.com/b/zOuG1loa/j-gomes-site"><u>Board here</u></a> <br />
                        - The swagger: <a href="/api/documentation#/Message" target="_blank"><u>Swagger here</u></a> <br />
                        - The phpunit: <a href="/coverage-report/index.html" target="_blank"><u>Code coverage here</u></a> <br />
                        - Case studies: <a href="/case-studies" target="_blank"><u>Case studies here</u></a> <br />
                        - More info: <a href="/details" target="_blank"><u>Details about this website here</u></a> <br />
                    </p>
                    <ul class="slides">
                    <!-- <li>
                            <blockquote>
                                <p>This is Photoshop's version  of Lorem Ipsum. Proin gravida nibh vel velit auctor aliquet.
                                    Aenean sollicitudin, lorem quis bibendum auctor, nisi elit consequat ipsum, nec sagittis sem
                                    nibh id elit. Duis sed odio sit amet nibh vulputate cursus a sit amet mauris.
                                </p>
                                <cite>Mr. Adobe</cite>
                            </blockquote>
                        </li> -->
                        <!-- slide ends -->
                    </ul>

                </div> <!-- div.flexslider ends -->

            </div> <!-- div.flex-container ends -->

        </div> <!-- row ends -->

    </div>  <!-- text-container ends -->

</section> <!-- Testimonials Section End-->


<!-- Contact Section
================================================== -->
<section id="contact">

    <div class="row section-head">

        <div class="two columns header-col">

            <h1><span>Get In Touch</span></h1>

        </div>

        <div class="ten columns">

            <p class="lead text-left">
                If you want to contact me, please leave your message using this form. I will respond as soon as possible! Thank you.
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
                                    <label for="name">Name <span class="required">*</span></label>
                                    <input type="text" value="" size="35" id="name" name="contactName">
                                </div>

                                <div>
                                    <label for="email">Email <span class="required">*</span></label>
                                    <input type="text" value="" size="35" id="email" name="contactEmail">
                                </div>

                                <div>
                                    <label for="subject">Subject</label>
                                    <input type="text" value="" size="35" id="subject" name="contactSubject">
                                </div>

                                <div>
                                    <label for="content">Message <span class="required">*</span></label>
                                    <div class="chatCount">
                                        <span id="chatCount"></span>
                                    </div>
                                    <textarea class="contactMessageExtra" cols="50" rows="15" id="content" name="contactMessage"></textarea>
                                </div>

                                <div>
                                    <button type="submit" class="submit">Submit</button>
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
                            <i class="fa fa-check"></i>Your message was sent, thank you!<br>
                        </div>

                    </div>


                    <aside class="four columns footer-widgets">

                        <div class="widget widget_contact">

                            <h4>Contact</h4>
                            <p class="address">
                                José Gomes<br>
                                Lisbon<br>
                                <span>+351 937523892</span>
                            </p>

                        </div>

                    </aside>

                </div>

            </section> <!-- Contact Section End-->


            <!-- footer
            ================================================== -->
            <footer>

                <div class="row">

                    <div class="twelve columns">

                        <ul class="social-links">
                            <li><a href="https://twitter.com/routineris" target="_blank"><i class="fa fa-twitter"></i></a></li>
                            <li><a href="https://www.linkedin.com/in/jos%C3%A9-gomes-3ba05869/" target="_blank"><i class="fa fa-linkedin"></i></a></li>
                            <li><a href="https://github.com/jfgomes?tab=repositories" target="_blank"><i class="fa fa-github"></i></a></li>
                        </ul>

                        <a href="https://www.codewars.com/users/JGomes87" target="_blank"><img alt="Dark Badge (large)" class="hidden dark:block" src="https://www.codewars.com/users/JGomes87/badges/large"></a>
                    </div>

                    <div id="go-top"><a class="smoothscroll" title="Back to Top" href="#home"><i class="icon-up-open"></i></a></div>

                </div>

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
            <script src="{{ (app()->environment() === 'prod')
                        ? mix('js/prod/public/app.js')
                        : 'js/local/public/init.js' }}">
            </script>
    </body>

</html>
