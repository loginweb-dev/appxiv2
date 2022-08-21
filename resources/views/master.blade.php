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
  <link rel="stylesheet" type="text/css" href="{{ asset('css/toastr.css') }}"> 
  {{-- <link rel="stylesheet" type="text/css" href="{{ asset('comments/jquery-comments.css') }}"> --}}

  <style>
  .panel:hover{
    box-shadow: 5px 5px 5px 2px #0C2746;
  }

  .miboton{
	background-color: white;
	color: #0C2746;
	border: 2px solid #0C2746; /* Green */
  border-radius: 10px;
  }
  .mitext{
	  color: #0C2746;
    box-shadow: 1px 1px 1px 1px #0C2746;
    border-radius: 10px;
  }
  
  .micontent{
	  color: #0C2746;
    /* box-shadow: 2px 2px 2px 2px white; */
    border: 2px solid #0C2746;
    border-radius: 20px;
  }

  .mititle{
	  color: #0C2746;
  }

   #miback {
     margin-top: 5px;
     margin-left: 5px;
     
     position:absolute;
     top: 20px;
     left: 10px;
 }

 #milogout {
     margin-top: 5px;
     margin-left: 5px;
     position:absolute;
     top: 20px;
     right: 10px;
 }

 #micart {
     margin-top: 1px;
     margin-left: 1px;
     position:absolute;
     top: 20px;
     right: 80px;
 }

 #micart2 {
     position:absolute;
     top: 20px;
     right: 20px;
 }

 #miinfo {
     position:absolute;
     top: 25px;
     right: 60px;
 }

 .miinput {
    /* width: 60%;
    height: 25px;
    margin: 0 auto; */
    border: 2px solid #0C2746;
    border-radius: 20px;
    box-shadow: 1px 1px 1px 1px #0C2746;
}

.modal-dialog {
    position: fixed;
    bottom: 0%;
    left: 0%;
    right: 0%;
    /* transform: translate(-150%, -150%); */
    
  } 
  .modal-content { 
    -webkit-border-radius: 20px;
}

.miright {
  /* display: flex; */
  /* justify-content: flex-end; */
  position: fixed;
  /* margin-left: auto;
  margin-right: auto; */
  margin-top: 2px;
  margin-left: 200px;
  /* margin-right: 100px; */
  /* left: 0%;
  top: 10px; */
  color: #DD324A;
}


.modal-body{
    max-height: calc(100vh - 200px);
    overflow-y: auto;
}

  </style>
  @yield('css')
</head>
<body>
	@yield('content')

  @if (Auth::user())
    @php
        $cliente = App\Cliente::where('user_id', Auth::user()->id)->first();
        $micarrito = App\Carrito::where('chatbot_id', $cliente->chatbot_id)->with('extras')->get();                    
    @endphp 
  @endif

    <div class="modal fade" id="micart_modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
      <div class="modal-dialog" role="document">
          <div class="modal-content">
            <div class="modal-header text-center">
              <h5 class="modal-title text-center" id="exampleModalLabel"> <img src="{{ Voyager::image(setting('site.logo')) }}" alt="" width="20"> Mi Carrito</h5>
              <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <i class="fa-solid fa-circle-xmark"></i>
              </button>
            </div>
            <div id="cart_body"></div>
          </div>
      </div>
    </div>

  <div class="modal fade" id="milogin" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
          <div class="modal-header text-center">
            <h5 class="modal-title text-center" id="exampleModalLabel"> <img src="{{ Voyager::image(setting('site.logo')) }}" alt="" width="20"> Inicia sesion para continuar</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <i class="fa-solid fa-circle-xmark"></i>
            </button>
          </div>
          <div class="modal-body text-center">
            <a href="#" onclick="miperfil()" class="btn miboton"><i class="fa-solid fa-user fa-xl"></i> Iniciar sesion</a>
          </div>
        </div>
    </div>
  </div>

  <script src="{{ asset('ecommerce/js/jquery-2.0.0.min.js') }}" type="text/javascript"></script>
	<script src="{{ asset('ecommerce/js/bootstrap.bundle.min.js') }}" type="text/javascript"></script>
	<script src="{{ asset('ecommerce/js/script.js') }}" type="text/javascript"></script>
  <script src="{{ asset('ecommerce/plugins/owlcarousel/owl.carousel.min.js') }}"></script>
  <script src="{{ asset('ecommerce/plugins/slickslider/slick.min.js') }}"></script>
	<script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
	<script src="https://kit.fontawesome.com/6510b28365.js" crossorigin="anonymous"></script>
	{{-- <script src="{{ asset('js/boxs.js') }}" crossorigin="anonymous"></script> --}}
	{{-- <script src="{{ asset('js/chatbot.js') }}" crossorigin="anonymous"></script> --}}
  <script src="{{ asset('js/toastr.js') }}" crossorigin="anonymous"></script>
  {{-- <script type="text/javascript" src="{{ asset('comments/comments-data.js') }}"></script> --}}
  {{-- <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jquery.textcomplete/1.8.0/jquery.textcomplete.js"></script> --}}
  {{-- <script type="text/javascript" src="{{ asset('comments/jquery-comments.js') }}"></script> --}}
  <script>

@if(Auth::user())  
  $("#micart_count").html("{{ count($micarrito) }}")
  @else
  $("#micart_count").html("0")
  @endif
function micart() {
    @if(Auth::user())      
      $("#micart_modal").modal()
      // console.log("{{ count($micarrito) }}")
      // $.ajax({
      //   url: "https://appxi.net/carrito/list/{{ $cliente->chatbot_id }}",
      //   dataType: "html",
      //   success: function (response) {
      //     $("#cart_body").html(response)
      //   }
      // });
      milist() 
    @else
    $("#milogin").modal()
    @endif
  }
  function milist() {
    $.ajax({
        url: "https://appxi.net/carrito/list/{{ $cliente->chatbot_id }}",
        dataType: "html",
        success: function (response) {
          $("#cart_body").html(response)
        }
      });
  }
  function miconfirm() {
    $.ajax({
        url: "https://appxi.net/carrito/confirm/{{ $cliente->chatbot_id }}",
        dataType: "html",
        success: function (response) {
          $("#cart_body").html(response)
        }
      });
  }
  function miperfil(){
    localStorage.setItem('mivolver', "/")
    location.href = '/perfil'
  }

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