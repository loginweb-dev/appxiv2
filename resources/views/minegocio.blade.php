@extends('master')

@section('css')
  {{-- <link rel="stylesheet" href="{{ asset('css/carousel.css') }}"> --}}
  <link href="https://cdn.jsdelivr.net/npm/swiffy-slider@1.5.3/dist/css/swiffy-slider.min.css" rel="stylesheet" crossorigin="anonymous">
@endsection
@section('content')
    <!-- ========================= SECTION CONTENT ========================= -->
<section class="section-content bg padding-y-sm">
  <div class="container-fluid">
    <div class="card">
      <div class="card-body">
        @php
          $image=$negocio->logo ? $negocio->logo : setting('negocios.img_default_negocio');
          $latitud=$negocio->latitud ? $negocio->latitud : '-15.2411217' ;
          $longitud=$negocio->longitud ? $negocio->longitud : '-63.8812874';
          $productos_varios= App\Producto::where('negocio_id', $negocio->id )->where('ecommerce', 1)->orderBy('id', 'desc')->with('categoria','negocio')->limit(10)->get();
          $categorias = App\Categoria::where('tipo_id', $negocio->tipo_id)->get();
          // if (isset($_GET['categoria'])) {
            // $productos = App\Producto::where('negocio_id', $negocio->id )->where('ecommerce', 1)->orderBy('id', 'desc')->where('categoria_id', $_GET['categoria'])->with('categoria','negocio')->get();
          // }else {
            // $poductos = App\Producto::where('negocio_id', $negocio->id )->where('ecommerce', 1)->orderBy('id', 'desc')->with('categoria','negocio')->get();
          // }
        @endphp
        <div class="row">                    
          <div class="col-sm-4 text-center d-none d-sm-block"> 
            <img class="img-fluid" src="{{setting('admin.url')}}storage/{{$image}}" width="100%">                   
          </div>               
          <div class="col-sm-4 text-center">
            <h3 class="text-center">{{ $negocio->nombre }}</h3>                                               
            <table class="table">
                <tr>
                  <td class="text-center">
                    <b>{{ $negocio->estado ? "A B I E R T O" : "C E R R A D O"; }}</b><br>
                    <ul class="rating-stars">
                      <li style="width: {{ $negocio->rating }}" class="stars-active"> 
                        <i class="fa fa-star"></i> <i class="fa fa-star"></i> 
                        <i class="fa fa-star"></i> <i class="fa fa-star"></i> 
                        <i class="fa fa-star"></i> 
                      </li>
                      <li>
                        <i class="fa fa-star"></i> <i class="fa fa-star"></i> 
                        <i class="fa fa-star"></i> <i class="fa fa-star"></i> 
                        <i class="fa fa-star"></i> 
                      </li>
                    </ul>
                    {{ $negocio->tipo->nombre }}
                  </td>
                </tr>
                <tr class="text-center">
                  <td><span>Atencion:</span> <br> <b>{{$negocio->horario}}</b></td>
                </tr>
                <tr class="text-center">
                  <td><span>Direccion:</span> <br><b>{{$negocio->direccion}}</b></td>
                </tr>
                </div> 
                <tr id="panel_control" hidden>
                  <td>
                      <a href="#" onclick="resetear_pw()" class="btn btn-success">Panel del Negocio <i class="fa fa-sign-in"></i></a>
                  </td>
                </tr>
                <tr>
                  <td>
                    <div class="d-block d-sm-none">
                      <div class="swiffy-slider slider-item-ratio slider-item-ratio-16x9 slider-nav-animation slider-nav-animation-fadein slider-nav-dark slider-nav-autoplay" id="swiffy-animation">
                        <ul class="slider-container mb-3" id="container1">
                            <li id="slide2" class="slide-visible">
                              <img src="https://delivery.appxi.net//storage/banner-redes-sociales-rojo-azul-venta-hamburguesas_254431-71.jpg" alt="..." loading="lazy">
                            </li>
                            <li id="slide3" class="">
                              <iframe src="https://maps.google.com/maps?q={{$latitud}},{{$longitud}}&hl=es&z=14&amp;output=embed" frameborder="0" scrolling="no" marginheight="0" marginwidth="0"></iframe>
                            </li>
                        </ul>            
                        <button type="button" class="slider-nav" aria-label="Go to previous"></button>
                        <button type="button" class="slider-nav slider-nav-next" aria-label="Go to next"></button>            
                        <div class="slider-indicators">
                            <button aria-label="Go to slide" class=""></button>
                            <button aria-label="Go to slide" class="active"></button>      
                        </div> 
                    </div> 
                  </td>
                </tr>
            </table>
          </div>
          <div class="col-sm-4 d-none d-sm-block">
            <iframe width="100%" height="100%" id="gmap_canvas" src="https://maps.google.com/maps?q={{$latitud}},{{$longitud}}&hl=es&z=14&amp;output=embed" frameborder="0" scrolling="no" marginheight="0" marginwidth="0"></iframe>
          </div>   
        </div>      
      </div>
      </div>

        @if($negocio->tipo_id==1)
          @foreach ($categorias as $value)
              @php
                  $productos = App\Producto::where('negocio_id', $negocio->id )->where('ecommerce', 1)->where('categoria_id', $value->id)->orderBy('id', 'desc')->with('categoria','negocio')->get();
              @endphp
            
            <h2 class="text-center p-2">{{ $value->nombre }}</h2>
            <div class="d-block d-sm-none">
              <div class="swiffy-slider slider-item-reveal slider-nav-round slider-nav-page slider-nav-dark">
                <ul class="slider-container">
                  @foreach ($productos as $item)
                    <li>
                      <div class="card h-100">
                        <a href="{{ route('producto', $item->slug) }}" style="color:#38A54A;">
                          <div class="ratio ratio-1x1">
                              <img src="{{ ($item->image!=null) ? Voyager::image($item->thumbnail('cropped', 'image')) : 'storage/'.setting('productos.img_default_producto') }}" class="card-img-top" loading="lazy" alt="...">
                          </div>
                          <div class="card-body text-center">
                              <h3 class="flex-grow-1 h5">{{ $item->nombre }}</h3>
                              <p class="card-text">{{ $item->detalle }}</p>
                          </div>
                        </a>
                      </div>
                  </li>
                  @endforeach   
                </ul>        
                <button type="button" class="slider-nav" aria-label="Go left"></button>
                <button type="button" class="slider-nav slider-nav-next" aria-label="Go left"></button>
              </div>  
            </div> 

            <div class="d-none d-sm-block d-md-none d-lg-none">              
            </div>

            <div class="d-none d-sm-none d-md-block d-lg-none">  
              <div class="swiffy-slider slider-nav-round slider-nav-animation slider-nav-animation-fadein slider-item-first-visible slider-nav-dark">
                <ul class="slider-container">
                  @foreach ($productos as $item)
                    <li class="slide-visible">
                        <div class="card rounded-0 h-100">
                            <div class="row g-0 h-100">
                                <div class="col-md-6 col-xl-5 d-flex align-items-center p-2 p-md-3 p-xl-5">
                                    <div class="card-body p-1 p-md-3 p-xl-5">
                                        {{-- <p class="lead">Why use this slider</p> --}}
                                        <h2 class="card-title" style="color:#38A54A;">{{ $item->nombre }}</h2>
                                        <p class="card-text mt-3">{{ $item->detalle }}</p>
                                        {{-- <p>Super simple setup using just markup and few powerful configuration options</p> --}}
                                        {{-- <p class="card-text"><small class="text-muted">Remember to check out on mobile</small></p> --}}
                                    </div>
                                </div>
                                <div class="col-md-6 col-xl-7">
                                    <img src="{{ ($item->image!=null) ? Voyager::image($item->thumbnail('cropped', 'image')) : '/storage/'.setting('productos.img_default_producto') }}" class="card-img d-none d-md-block" loading="lazy" style="height: 100%; object-fit: cover;" alt="...">
                                    <img src="{{ ($item->image!=null) ? Voyager::image($item->thumbnail('cropped', 'image')) : '/storage/'.setting('productos.img_default_producto') }}" class="card-img d-block d-md-none" loading="lazy" style="width: 100%; object-fit: cover;" alt="...">
                                </div>
                            </div>
                        </div>
                    </li>
                    @endforeach
                </ul>
            
                <button type="button" class="slider-nav" aria-label="Go left"></button>
                <button type="button" class="slider-nav slider-nav-next" aria-label="Go left"></button>
            
                <div class="slider-indicators">
                    <button class="active" aria-label="Go to slide"></button>
                    <button aria-label="Go to slide" class=""></button>
                    <button aria-label="Go to slide" class=""></button>
                </div>
              </div>          
            </div>

            <div class="d-none d-sm-none d-md-none d-lg-block">     
              <div class="swiffy-slider slider-item-show4 slider-item-nosnap slider-nav-round slider-nav-dark slider-indicators-dark slider-indicators-outside slider-indicators-highlight slider-indicators-sm slider-nav-animation">
                  <ul class="slider-container">
                    @foreach ($productos as $item)
                      <li class="">
                        <div class="card shadow h-100">
                          <a href="{{ route('producto', $item->slug) }}" style="color:#38A54A;">
                            <div class="ratio ratio-16x9">
                                <img src="{{ ($item->image!=null) ? Voyager::image($item->thumbnail('cropped', 'image')) : '/storage/'.setting('productos.img_default_producto') }}" class="card-img-top" loading="lazy" alt="...">
                            </div>
                            <div class="card-body p-3 p-xl-5">
                                <h3 class="card-title h2 text-center" style="color: #38A54A">{{ $item->nombre }}</h3>
                                <p class="card-text text-center" style="color: #38A54A">{{ $item->detalle }}</p>
                                {{-- <div><a href="#" class="btn btn-primary">Go somewhere</a></div> --}}
                            </div>
                          </a>
                        </div>
                    </li>
                    @endforeach
                  </ul>
                  <button type="button" class="slider-nav" aria-label="Go left"></button>
                  <button type="button" class="slider-nav slider-nav-next" aria-label="Go left"></button>
                </div>
            </div>
          @endforeach

        @elseif($negocio->tipo_id==2 OR $negocio->tipo_id==3)
          <br>
          <div class="row">
            <div class="col-md-6 col-sm-6 center">
              <span class="center">Puede Buscar por Nombre Comercial, Genérico o Tipo de Medicamento</span>
            </div>
            <div class="col-md-4 col-sm-4 input-group-append center">
              {{-- <label for="input_busqueda">Buscar Producto</label> --}}
            <input class="form-control" type="text" name="input_busqueda" id="input_busqueda" placeholder="Ingrese Nombre Producto">
            <button class="btn btn-sm btn-success" onclick="BuscarProducto()"><i class="fa fa-search"></i> Buscar</button>          
            </div>           
          </div>
          <div class="row-sm  mt-3" id="productos_defecto">
            @foreach ($productos_varios as $item)
              <div class="col-md-3 col-sm-6">
                <figure class="card card-product">
                  @if ($item->image!=null)
                    <div class="img-wrap"> <a href="{{route('producto', $item->slug)}}"><img  src="{{ Voyager::image($item->thumbnail('cropped', 'image')) }}"></a></div>
                  @else
                    <div class="img-wrap"> <a href="{{route('producto', $item->slug)}}"><img  src="{{setting('admin.url')}}storage/{{setting('productos.img_default_producto')}}" ></a></div>
                  @endif
                  <figcaption class="info-wrap">
                    
                    <a href="{{route('producto', $item->slug)}}"><h4 class="title"><b>{{ $item->nombre }}</b></h4></a>
                    <p>{{ $item->detalle }}</p>
                    <div class="price-wrap">
                      @if ($item->precio > 0)
                        <span class="price-new"><h5><b>{{ $item->precio }} Bs.</b></h5></span>
                      @else
                        @php
                          $rel=App\RelProductoPrecio::where('producto_id', $item->id)->get();
                        @endphp 
                          @foreach ($rel as $item2)
                            @php
                              $precio_prod= App\Precio::find( $item2->precio_id);
                            @endphp
                              <span class="price-new"><b>{{ $precio_prod->nombre }} {{ $precio_prod->precio }} Bs.</b></span><br>
                          @endforeach                                              
                      @endif
                    </div>
                  </figcaption>
                </figure>
              </div>
            @endforeach
          </div>
        @endif
  </div>
</section>

<div class="modal modal-primary fade" tabindex="-1" id="modal-lista_extras" role="dialog">
  <div class="modal-dialog">
      <div class="modal-content">
          <div class="modal-header">
              <h4 class="modal-title"><i class="voyager-list-add"></i> Lista de extras</h4>
          </div>
          <div class="modal-body">
              <input type="text" name="producto_extra_id" id="producto_extra_id" hidden>
              <input type="text" name="tr_producto" id="tr_producto" hidden>

              <table class="table table-bordered table-hover" id="table-extras">
                  <thead>
                      <tr>
                          {{-- <th>Imagen</th> --}}
                          <th>ID</th>
                          <th>Extra</th>
                          <th>Precio</th>
                          <th>Cantidad</th>
                      </tr>
                  </thead>
                  <tbody>
                  </tbody>
              </table>
              {{-- <td style="text-align: right">
                  <input style="text-align:right" readonly min="0" type="number" name="total_extra" id="total_extra">
              </td> --}}
          </div>
          <div class="modal-footer">
              <button type="button" class="btn btn-primary pull-right" onclick="calcular_total_extra()" data-dismiss="modal">Añadir</button>
              <button type="button" class="btn btn-default pull-right" data-dismiss="modal">Cerrar</button>
          </div>
      </div>
  </div>
</div>  
@endsection
@section('javascript')
  <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
  {{-- <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.5.1/jquery.slim.min.js"></script> --}}
  {{-- <script src="{{ asset('js/carousel.js') }}"></script> --}}
  <script src="https://cdn.jsdelivr.net/npm/swiffy-slider@1.5.3/dist/js/swiffy-slider.min.js" crossorigin="anonymous" defer></script>
  <script type="text/javascript">
    $(document).ready(function() {
      var misession = localStorage.getItem('misession') ? JSON.parse(localStorage.getItem('misession')) : []
      if (!misession.name || !misession.phone || !misession.localidad) {
      pb.prompt(
        function (value) { 
          // 1
          localStorage.setItem('misession', JSON.stringify({name: value, phone: null, localidad: null}))
          pb.prompt(
            function (value) { 
              // 2
              misession = JSON.parse(localStorage.getItem('misession'))
              localStorage.setItem('misession', JSON.stringify({name: misession.name, phone: value, localidad: null}))
              pb.prompt(
                async function (value) { 
                  // 3
                  var milocation = await axios('https://delivery.appxi.net/api/poblacion/'+value)
                  misession = JSON.parse(localStorage.getItem('misession'))
                  localStorage.setItem('misession', JSON.stringify({name: misession.name, phone: misession.phone, localidad: milocation.data}))
                  //location.href = "{{setting('admin.url')}}marketplace?localidad="+value
                  location.reload()
                },
                'Gracias, en que localidad te encuentras ?',
                'select',
                '',
                'Enviar',
                'Cancelar'
              );
            },
            'Gracias, Ahora necesito tu whatsapp',
            'number',
            '',
            'Enviar',
            'Cancelar',
            {}
          );
          }, // Callback
          'Bienvenido a GoDelivery, Cual es tu Nombre Completo?',
          'text',
          '',
          'Enviar',
          'Cancelar'
        );            
      } else {
        @if(!isset($_GET['localidad']))
          misession = JSON.parse(localStorage.getItem('misession'))
          //location.href = "{{setting('admin.url')}}marketplace?localidad="+misession.localidad.id
          //location.reload()
        @endif
        $("#localidad").val(misession.localidad.id)
      }
    });
  </script>

  <script>
    $(document).ready( function(){
      localidad_validacion()
      activar_boton_panel()
      $('#input_busqueda').val("")
      // $('.carousel').carousel()
    });
    async function activar_boton_panel(){
      var user = JSON.parse(localStorage.getItem('misession'));
      if (user.phone=='{{$negocio->telefono}}') {
        $("#panel_control").attr("hidden",false); 
      }
    }
    async function localidad_validacion(){
      var user = JSON.parse(localStorage.getItem('misession'));
      localidad= user.localidad.id
      if ('{{$negocio->poblacion_id}}'!=localidad) {
        location.href="{{setting('admin.url')}}marketplace"
      }
    }
    async function agregar_carrito(id) {
      //console.log("Hola "+id)
      var producto= await axios("{{setting('admin.url')}}api/producto/"+id)
      //console.log(producto.data)
      var user = JSON.parse(localStorage.getItem('misession'));
      var telefono ='591'+user.phone+'@c.us'
      var nombre = user.name
      var localidad= user.localidad.id

      var cliente= await axios("{{setting('admin.url')}}api/cliente/"+telefono)
      if (cliente.data.poblacion_id) {
        
      }
      else{
        var midata={
          id:cliente.data.id,
          nombre:nombre,
        }
        await axios.post("{{setting('admin.url')}}api/cliente/update/nombre", midata)
        var midata={
          id:cliente.data.id,
          poblacion_id:localidad,
        }
        await axios.post("{{setting('admin.url')}}api/cliente/update/localidad", midata)
      }
      var data={
        product_id: id,
        product_name: producto.data.nombre,
        chatbot_id: telefono,
        precio: producto.data.precio,
        cantidad: parseInt($('#cantidad_producto').val()),
        negocio_id: producto.data.negocio.id,
        negocio_name: producto.data.negocio.nombre
      }
      var carrito= await axios.post("{{setting('admin.url')}}api/chatbot/cart/add", data)
      if (carrito.data) {
        var list = '*🎉 Producto agregado a tu carrito 🎉*\n'
        list += 'Si deseas agregar mas productos a tu carrito visita el mismo u otros negocios (A).\n'
        list += '------------------------------------------\n'
        list += '*H* .- VER MI CARRITO\n'
        list += '*G* .- SOLICITAR PEDIDO\n'
        list += '*A* .- TODOS LOS NEGOCIOS\n'
        list += '------------------------------------------\n'
        list += 'ENVIA UNA OPCION ejemplo: H o G'
        
        //Mensaje a Cliente
        pb.info(
          'Producto Agregado a Carrito Exitosamente, debes terminar el Pedido en WhatsApp o puedes seguir añadiendo más productos a tu Carrito.'
        );
        // toastr.succes("Producto Agregado a Carrito Exitosamente, debes terminar el Pedido en WhatsApp o puedes seguir añadiendo más productos a tu Carrito.")
        var data={
          message:list,
          phone:telefono
        }
        await axios.post("{{setting('admin.chatbot_url')}}chat", data)
      }
    }
    async function addextra(negocio_id , producto_id) {
        $("#table-extras tbody tr").remove();
        $("#producto_extra_id").val(producto_id);
        //$("#tr_producto").val(code);
        //console.log(extras)
        var mitable="";
        var extrasp=  await axios.get("{{ setting('admin.url') }}api/producto/extra/negocio/"+negocio_id);
        for(let index=0; index < extrasp.data.length; index++){
            mitable = mitable + "<tr><td><input class='form-control extraprodid' readonly value='"+extrasp.data[index].id+"'></td><td><input class='form-control extra-name' readonly value='"+extrasp.data[index].nombre+"'></td><td><input class='form-control extra-precio' readonly  value='"+extrasp.data[index].precio+" Bs."+"'></td><td><input class='form-control extra-cantidad' style='width:100px' type='number' min='0' value='0'  id='extra_"+extrasp.data[index].id+"'></td></tr>";
        }
        $('#table-extras').append(mitable);
    }
    async function calcular_total_extra(){
      var cantidad=[];
      var name=[];
      var precio=[];
      var idprod=[];
      var index_cantidad=0;
      var index_name_aux=0;
      var index_precio_aux=0;
      var index_cantidad_aux=0;
      var index_idprod_aux=0;    
      $('.extra-cantidad').each(function(){
          if($(this).val()>0){
              cantidad[index_cantidad_aux]=parseFloat($(this).val());
              index_cantidad_aux+=1;
              var index_name=0;
              $('.extra-name').each(function(){
                  if(index_name==index_cantidad){
                      name[index_name_aux]=$(this).val();
                      index_name_aux+=1;
                  }
                  index_name+=1;
              });

              var index_precio=0;
              $('.extra-precio').each(function(){
                  if(index_precio==index_cantidad){
                      precio[index_precio_aux]=parseFloat($(this).val());
                      index_precio_aux+=1;
                  }
                  index_precio+=1;
              });

              var index_idprod=0;
              $('.extraprodid').each(function(){
                  if(index_idprod==index_cantidad){
                      idprod[index_idprod_aux]=parseFloat($(this).val());
                      index_idprod_aux+=1;
                  }
                  index_idprod+=1;
              });
          }
          index_cantidad+=1;
      });
    }
    async function resetear_pw(){
      var user = JSON.parse(localStorage.getItem('misession'));
      var newpassword=Math.random().toString().substring(2, 8)
      var phone= '591'+user.phone+'@c.us'
      var midata={
        phone:phone,
        password:newpassword
      }
      var usuario= await axios.post("{{setting('admin.url')}}api/reset/pw/negocio", midata)
      var list=''
      list+='Credenciales para Ingresar al Sistema:\n'
      list+='Correo: '+usuario.data.email+' \n'
      list+='Contraseña: '+newpassword+' \n'
      list+='No comparta sus credenciales con nadie'
      var data={
          message:list,
          phone:phone
        }
      axios.post("{{setting('admin.chatbot_url')}}login", data)
      setTimeout(function(){
        location.href="/admin"
      }, 5000)
    }

    async function BuscarProducto(){
      var negocio= '{{$negocio->id}}'
      var parametro= $('#input_busqueda').val()
      var midata={
        criterio:parametro,
        negocio_id:negocio
      }
      var resultado=await axios.post("{{setting('admin.url')}}api/search/producto/negocio", midata)
      if (resultado.data.length>0) {
        var list=""
        var image="{{setting('admin.url')}}storage/{{setting('productos.img_default_producto')}}"
        for (let index = 0; index < resultado.data.length; index++) {
          var ruta="{{route('producto', 'mivariable')}}"
          ruta=ruta.replace('mivariable', resultado.data[index].slug)
          var titulo= resultado.data[index].titulo ? resultado.data[index].titulo : " "
          var etiqueta = resultado.data[index].etiqueta ? resultado.data[index].etiqueta : " "

          list+="<div class='col-md-3 col-sm-6'> <figure class='card card-product'><div class='img-wrap'> <a href="+ruta+"><img src="+image+"></a></div><figcaption class='info-wrap'><a href="+ruta+"><h4 class='title'><b>"+resultado.data[index].nombre+"</b></h4></a><p>"+titulo+"</p><div class='price-wrap'>"
          list+="<span class='price-new'><h5><b>"+resultado.data[index].precio+" Bs.</b></h5></span>"
          list+="</div></figcaption></figure></div>"

        }
        console.log(list)
        // $("#productos_defecto").remove();
        $('#productos_defecto').html(list)

      }
      else{
        pb.info(
          'No se obtuvieron resultados de su búsqueda.'
        );
      }
    }

    $('#input_busqueda').keypress(async function(event) {
      if ( event.which == 13 ) {
          BuscarProducto()
      }
    });

    $('#optradio').change(function() {
      // console.log(this.value)
      if (this.value == 0) {
        location.href = "/negocio/{{ $negocio->slug }}"
      }else {
        location.href = "?categoria="+this.value
      }
  });
  </script>
@endsection