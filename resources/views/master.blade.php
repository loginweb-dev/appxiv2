<!DOCTYPE HTML>
<html lang="es">
<head>
  <script async src="https://pagead2.googlesyndication.com/pagead/js/adsbygoogle.js?client=ca-pub-3938581935210559"
     crossorigin="anonymous"></script>

  <meta charset="utf-8">
  <meta http-equiv="pragma" content="no-cache" />
  <meta http-equiv="cache-control" content="max-age=604800" />
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <meta name="author" content="Ing. Percy Alvarez C.">
  <meta name="theme-color" content="#0C2746">
  @yield('meta')
  <link href="{{ asset('ecommerce/css/bootstrap.css') }}" rel="stylesheet" type="text/css"/>
  <link href="{{ asset('ecommerce/css/ui.css') }}" rel="stylesheet" type="text/css"/>
  <link href="{{ asset('ecommerce/css/responsive.css') }}" rel="stylesheet" media="only screen and (max-width: 1200px)" />
  <link rel="stylesheet" type="text/css" href="{{ asset('css/boxs.css') }}"> 
  <link rel="stylesheet" type="text/css" href="{{ asset('css/chatbot.css') }}"> 
  <link rel="stylesheet" type="text/css" href="{{ asset('css/cart.css') }}"> 
  <link rel="stylesheet" type="text/css" href="{{ asset('css/social-share.css') }}"> 

  <link href="{{ asset('ecommerce/plugins/owlcarousel/assets/owl.carousel.min.css') }}" rel="stylesheet">
  <link href="{{ asset('ecommerce/plugins/owlcarousel/assets/owl.theme.default.css') }}" rel="stylesheet">

  <link href="{{ asset('ecommerce/plugins/slickslider/slick.css') }}" rel="stylesheet" type="text/css" />
  <link href="{{ asset('ecommerce/plugins/slickslider/slick-theme.css') }}" rel="stylesheet" type="text/css" />


  <style>
  .scrollup{
     width:40px;
     height:40px;
     /* opacity:0.3; */
     position:fixed;
     bottom:5px;
     right:45%;
     display:none;
     color: white;
     background-color: #0C2746;
     /* text-indent:-9999px; */
     /* background: url(../images/icon_top.png) no-repeat; */
  } 
  article:hover{
    box-shadow: 5px 5px 5px 2px #0C2746;
  }
  nav:hover{
    box-shadow: 5px 5px 5px 2px #0C2746;
  }
  .panel:hover{
    box-shadow: 5px 5px 5px 2px #0C2746;
  }
  .miboton{
	background-color: white;
	color: #0C2746;
	border: 2px solid #0C2746; /* Green */
  }
  .mitext{
	  color: #0C2746;
    box-shadow: 2px 2px 2px 2px #0C2746;
  }
  .mititle{
	  color: #0C2746;
    /* box-shadow: 2px 2px 2px 2px #0C2746; */
  }
  </style>
  @yield('css')
</head>
<body>
	@yield('content')
  {{-- <div id="fb-root"></div> --}}
  <!-- Messenger plugin del chat Code -->
  {{-- <div id="fb-root"></div> --}}

  <!-- Your plugin del chat code -->
  {{-- <div id="fb-customer-chat" class="fb-customerchat"> --}}
  {{-- </div> --}}

  {{-- <script>
    var chatbox = document.getElementById('fb-customer-chat');
    chatbox.setAttribute("page_id", "106265762190084");
    chatbox.setAttribute("attribution", "biz_inbox");
  </script> --}}

  <a href="#" class="btn scrollup miboton"><i class="fa-solid fa-angles-up"></i></a>
  <script src="{{ asset('ecommerce/js/jquery-2.0.0.min.js') }}" type="text/javascript"></script>
	<script src="{{ asset('ecommerce/js/bootstrap.bundle.min.js') }}" type="text/javascript"></script>
	<script src="{{ asset('ecommerce/js/script.js') }}" type="text/javascript"></script>
  <script src="{{ asset('ecommerce/plugins/owlcarousel/owl.carousel.min.js') }}"></script>
  <script src="{{ asset('ecommerce/plugins/slickslider/slick.min.js') }}"></script>
	<script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
	<script src="https://kit.fontawesome.com/6510b28365.js" crossorigin="anonymous"></script>
	<script src="{{ asset('js/boxs.js') }}" crossorigin="anonymous"></script>
	<script src="{{ asset('js/chatbot.js') }}" crossorigin="anonymous"></script>
  <script>
    window.fbAsyncInit = function() {
      FB.init({
        appId            : '1097293254213101',
        autoLogAppEvents : true,
        xfbml            : true,
        version          : 'v14.0'
      });
    };
  </script>
  <script async defer crossorigin="anonymous" src="https://connect.facebook.net/es_ES/sdk.js"></script>
  {{-- <script async defer crossorigin="anonymous" src="https://connect.facebook.net/es_ES/sdk.js#xfbml=1&version=v14.0&appId=1097293254213101&autoLogAppEvents=1" nonce="pPDpU4C2"></script> --}}
  <script>

    $(window).scroll(function(){
            if ($(this).scrollTop() > 100) {
                $('.scrollup').fadeIn();
            } else {
                $('.scrollup').fadeOut();
            }
        });

        $('.scrollup').click(function(){
            $("html, body").animate({ scrollTop: 0 }, 600);
            return false;
        });
  </script>
  @yield('javascript')
  <!-- Google tag (gtag.js) - Google Analytics -->
<script async src="https://www.googletagmanager.com/gtag/js?id=UA-236908331-1"></script>
<script>
  window.dataLayer = window.dataLayer || [];
  function gtag(){dataLayer.push(arguments);}
  gtag('js', new Date());

  gtag('config', 'UA-236908331-1');
</script>

<script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyDH_m3M3RHw6s6AZeubtUZ8XIW7jC2MjCU&v=weekly" defer></script>
</html>