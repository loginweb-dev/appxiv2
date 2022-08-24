const express = require('express');
const axios = require('axios');
const qrcode = require("qrcode-terminal");
var qr = require('qr-image');
var path = require('path');
const cors = require('cors')
const { Client, MessageMedia, LocalAuth, Location, Buttons} = require("whatsapp-web.js");
// const googleMapsClient = require('@google/maps').createClient({
//     key: 'AIzaSyDH_m3M3RHw6s6AZeubtUZ8XIW7jC2MjCU'
// });
const {googleMapsClient} = require("@googlemaps/google-maps-services-js");
var shortUrl = require("node-url-shortener");
// const { io } = require("socket.io-client");
// const socket = io("https://socket.appxi.net");

const JSONdb = require('simple-json-db');
const option_categoria= new JSONdb('json/option_categoria.json');
const producto_buscado= new JSONdb('json/producto_buscado.json');
const producto_mas_vendido= new JSONdb('json/producto_mas_vendido.json');
const negocios = new JSONdb('json/negocios.json');
const categorias = new JSONdb('json/categorias.json');
const productos = new JSONdb('json/productos.json');
const carts = new JSONdb('json/carts.json');
const locations = new JSONdb('json/locations.json');
const localidades = new JSONdb('json/localidades.json');
const status = new JSONdb('json/status.json');
const pedidos = new JSONdb('json/pedidos.json');
const status_mensajero = new JSONdb('json/status_mensajero.json');
const tipos = new JSONdb('json/tipos.json');
const extras = new JSONdb('json/extras.json');
const extra_carts = new JSONdb('json/extra_carts.json');
const pedidosencola = new JSONdb('json/pedidosencola.json');
require('dotenv').config({ path: '../../.env' })

const app = express();
app.use(cors())
app.use(express.json())
app.set("view engine", "ejs");
app.use(express.static(path.join(__dirname, 'public')));

const client = new Client({
    authStrategy: new LocalAuth({
        clientId: "client-one"
    }),
    puppeteer: {
        headless: true,
        ignoreDefaultArgs: ['--disable-extensions'],
        args: ['--no-sandbox']
    }
});

app.listen(process.env.CHATBOT_PORT, () => {
    console.log('CHATBOT ESTA LISTO EN EL PUERTO: '+process.env.CHATBOT_PORT);
});

var micount = 0
var miwweb = false
client.on("qr", (qrwb) => {
    var qr_svg = qr.image(qrwb, { type: 'png' });
    qr_svg.pipe(require('fs').createWriteStream('public/qrwb.png'));
    qrcode.generate(qrwb, {small: true}, function (qrcode) {
        console.log(qrcode)
        console.log('Nuevo QR, recuerde que se genera cada 1 minuto, INTENTO #'+micount++)
        
    })
});

client.on('ready', async () => {
    miwweb = true
	console.log('CHATBOT ESTA LISTO EN EL PUERTO: '+process.env.CHATBOT_PORT);
});

client.on("authenticated", () => {
});

client.on("auth_failure", msg => {
    console.error('AUTHENTICATION FAILURE', msg);
})

client.on('message', async msg => {
    console.log('MESSAGE RECEIVED', msg);
    var micliente = await axios(process.env.APP_URL+'api/cliente/'+msg.from)
    var mioption = ['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z']
    switch (msg.type) {
        case 'chat':
            if (micliente.data.nombre) {
                var status_cliente=status.get(msg.from)
                if (msg.body === 'reset') {
                    status.set(msg.from, 0.6)
                    reset_cliente(msg.from, 0.6)
                    await axios.post(process.env.APP_URL+'api/chatbot/cart/clean', {chatbot_id: msg.from})                    
                    // var mitipos = await axios(process.env.APP_URL+'api/tipo/negocios')
                    // for (let index = 0; index < mitipos.data.length; index++) {
                    //     tipos.set('A'+mitipos.data[index].id, mitipos.data[index].id);
                    // }
                    // await axios.post(process.env.ACHATBOT_URL+'update', {
                    //     phone: '59170269362@c.us',
                    //     status: 3
                    // })
                }
                if (msg.body.toUpperCase() === 'MENU' && (parseFloat(status_cliente)< 2)) {
                    status.set(msg.from, 0.6)
                    // await axios.post(process.env.APP_URL+'api/chatbot/cart/clean', {chatbot_id: msg.from})                    
                    // var mitipos = await axios(process.env.APP_URL+'api/tipo/negocios')
                    // for (let index = 0; index < mitipos.data.length; index++) {
                    //     tipos.set('A'+mitipos.data[index].id, mitipos.data[index].id);
                    // }
                }
                if (msg.body.toUpperCase() === 'CLEAN' && (parseFloat(status_cliente)< 2)) {
                    status.set(msg.from, 0.6)
                    reset_cliente(msg.from, 0.6)
                    await axios.post(process.env.APP_URL+'api/chatbot/cart/clean', {chatbot_id: msg.from})                    
                    // var mitipos = await axios(process.env.APP_URL+'api/tipo/negocios')
                    // for (let index = 0; index < mitipos.data.length; index++) {
                    //     tipos.set('A'+mitipos.data[index].id, mitipos.data[index].id);
                    // }
                }                                  
                switch (status.get(msg.from)) {
                    case 0: //estado inicial
                        switch (true) {
                            case (msg.body === 'hola') || (msg.body === 'HOLA') || (msg.body === 'Hola') || (msg.body === 'Buenas')|| (msg.body === 'buenas') || (msg.body === 'BUENAS') || (msg.body === '0'):
                                menu_principal(micliente, msg.from)
                                status.set(msg.from, 0)
                                break;
                            default:
                                if (msg.body === '❌' || msg.body === '🚮' || msg.body === 'eliminar' || msg.body === 'Eliminar' || msg.body === 'vaciar' || msg.body === 'Vaciar') {
                                    await axios.post(process.env.APP_URL+'api/chatbot/cart/clean', {chatbot_id: msg.from})
                                    status.set(msg.from, 0)
                                    client.sendMessage(msg.from, '❌ Carrito vacio 🚮')                            
                                    negocios_list(msg.from, micliente)
                                }else if(msg.body === 'Carrito' || msg.body === 'carrito' || msg.body === 'Pedir' || msg.body === 'pedir' || msg.body === 'Ver' || msg.body === 'ver'){
                                    await cart_list(msg.from, micliente)
                                    list = '*A* .- Enviar pedido\n'
                                    list += '*B* .- Seguir comprando\n'                           
                                    list += '----------------------------------\n'
                                    list += 'Envía una opción (ejemplo: *A*)'
                                    client.sendMessage(msg.from, list)  
                                    status.set(msg.from, 1.1)
                                    
                                }else{
                                    client.sendMessage(msg.from, 'Envía una opción válida')
                                }
                                break;
                        }
                        break;
                    case 0.89:
                        var validar = false
                        var miproductos= producto_buscado.get(msg.from)
                        for (let index = 0; index < miproductos.length; index++) {
                            if (miproductos[index].option === msg.body.toUpperCase()) {
                                validar = true
                                productos.set(msg.from, miproductos[index].producto)
                                break;
                            }
                        }
                        if (validar) {
                            var media = ''
                            var miprecios = []
                            var miproducto = await axios(process.env.APP_URL+'api/producto/'+productos.get(msg.from).id)
                            if (miproducto.data.image) {
                                media = MessageMedia.fromFilePath('../../storage/app/public/'+miproducto.data.image)
                            } else {
                                media = MessageMedia.fromFilePath('imgs/default.png')
                            }
                            if (miproducto.data.precio != 0) {                                                    
                                var list = miproducto.data.nombre+' '+miproducto.data.precio+'Bs.\n'
                                list += miproducto.data.detalle+'\n'
                                list += '--------------------------\n'
                                list += '*A* .- Añadir a carrito\n'
                                list += '*B* .- Seguir comprando\n'
                                list += '----------------------------------\n'
                                list += 'Envía una opción (ejemplo: *A*)\n'
                                list += '----------------------------------\n'
                                list += 'Visita el Producto en Línea en:\n'
                                list += process.env.APP_URL+miproducto.data.negocio.slug+'/'+miproducto.data.slug


                                status.set(msg.from, 0.3)
                                miprecios=miproducto.data.precio
                                client.sendMessage(msg.from, media, {caption: list})                                                    
                            } else {                                 
                                var list = miproducto.data.nombre+'\n'
                                list += miproducto.data.detalle+'\n'
                                list += '--------------------------'+'\n'
                                for (let index = 0; index < miproducto.data.precios.length; index++) {
                                    var precio = await axios(process.env.APP_URL+'api/precio/'+miproducto.data.precios[index].precio_id)
                                    list += '*'+mioption[index]+'* .- '+precio.data.nombre+' '+precio.data.precio+'Bs.\n'
                                    miprecios.push({opcion: mioption[index], precio: precio.data.precio})
                                }                                                    
                                list += '----------------------------------\n'
                                list += 'Envía una opción (ejemplo: *A*)'
                                list += 'Visita el Producto en Línea en:\n'
                                list += process.env.APP_URL+miproducto.data.negocio.slug+'/'+miproducto.data.slug
                                status.set(msg.from, 0.4)
                                client.sendMessage(msg.from, media, {caption: list})                                                    
                            }
                            if (miproducto.data.extra) {
                                var miextras = await axios(process.env.APP_URL+'api/producto/extra/negocio/'+miproducto.data.negocio_id)
                                carts.set(msg.from, {id: miproducto.data.id, nombre: miproducto.data.nombre, precio: miprecios, extra: miextras.data, negocio_id: miproducto.data.negocio_id, negocio_nombre: miproducto.data.negocio.nombre})
                            }else{
                                carts.set(msg.from, {id: miproducto.data.id, nombre: miproducto.data.nombre, precio: miprecios, extra: false, negocio_id: miproducto.data.negocio_id, negocio_nombre: miproducto.data.negocio.nombre})
                            }
                        }
                        else{
                            client.sendMessage(msg.from, 'Envía una opción válida')
                        }
                        break;
                    case 0.9: 
                        var validar = false
                        var miproductos = await axios.post(process.env.APP_URL+'api/productos/categoria', {
                            negocio: negocios.get(msg.from).id,
                            categoria: categorias.get(msg.from).id
                        })
                        for (let index = 0; index < miproductos.data.length; index++) {
                            if (mioption[index] === msg.body.toUpperCase()) {
                                validar = true
                                productos.set(msg.from, miproductos.data[index])
                                break;
                            }
                        }
                        if (validar) {
                            var media = ''
                            var miprecios = []
                            var miproducto = await axios(process.env.APP_URL+'api/producto/'+productos.get(msg.from).id)
                            if (miproducto.data.image) {
                                media = MessageMedia.fromFilePath('../../storage/app/public/'+miproducto.data.image)
                            } else {
                                media = MessageMedia.fromFilePath('imgs/default.png')
                            }
                            if (miproducto.data.precio != 0) {                                                    
                                var list = miproducto.data.nombre+' '+miproducto.data.precio+'Bs.\n'
                                list += miproducto.data.detalle+'\n'
                                list += '--------------------------\n'
                                list += '*A* .- Añadir a carrito\n'
                                list += '*B* .- Seguir comprando\n'
                                list += '----------------------------------\n'
                                list += 'Envía una opción (ejemplo: *A*)\n'
                                list += '----------------------------------\n'
                                list += 'Visita el Producto en Línea en:\n'
                                list += process.env.APP_URL+miproducto.data.negocio.slug+'/'+miproducto.data.slug
                                status.set(msg.from, 0.3)
                                miprecios=miproducto.data.precio
                                client.sendMessage(msg.from, media, {caption: list})                                                    
                            } else {                                 
                                var list = miproducto.data.nombre+'\n'
                                list += miproducto.data.detalle+'\n'
                                list += '--------------------------'+'\n'
                                for (let index = 0; index < miproducto.data.precios.length; index++) {
                                    var precio = await axios(process.env.APP_URL+'api/precio/'+miproducto.data.precios[index].precio_id)
                                    list += '*'+mioption[index]+'* .- '+precio.data.nombre+' '+precio.data.precio+'Bs.\n'
                                    miprecios.push({opcion: mioption[index], precio: precio.data.precio})
                                }                                                    
                                list += '----------------------------------\n'
                                list += 'Envía una opción (ejemplo: *A*)\n'
                                list += '----------------------------------\n'
                                list += 'Visita el Producto en Línea en:\n'
                                list += process.env.APP_URL+miproducto.data.negocio.slug+'/'+miproducto.data.slug
                                status.set(msg.from, 0.4)
                                client.sendMessage(msg.from, media, {caption: list})                                                    
                            }
                            if (miproducto.data.extra) {
                                var miextras = await axios(process.env.APP_URL+'api/producto/extra/negocio/'+miproducto.data.negocio_id)
                                carts.set(msg.from, {id: miproducto.data.id, nombre: miproducto.data.nombre, precio: miprecios, extra: miextras.data, negocio_id: miproducto.data.negocio_id, negocio_nombre: miproducto.data.negocio.nombre})
                            }else{
                                carts.set(msg.from, {id: miproducto.data.id, nombre: miproducto.data.nombre, precio: miprecios, extra: false, negocio_id: miproducto.data.negocio_id, negocio_nombre: miproducto.data.negocio.nombre})
                            }
                        } else {
                            client.sendMessage(msg.from, 'Envía una opción válida')
                        }
                        break;
                    case 0.79:
                        if ((msg.body.toUpperCase()!= 'MENU') && (msg.body.toUpperCase()!= 'CLEAN') && (msg.body.toUpperCase()!= 'RESET')) {
                            var prod_buscar= msg.body.toUpperCase()
                            var minegocio = negocios.get(msg.from)
                            var resultado= await axios.post(process.env.APP_URL+'api/search/producto/negocio/chatbot', {
                                negocio_id: minegocio.id,
                                criterio: prod_buscar
                            })
                            if (resultado.data.length>0) {
                                var vector=[]
                                var list = '*Productos Disponibles*\n'
                                for (let index = 0; index < resultado.data.length; index++) {
                                    list += '*'+mioption[index]+'* .- '+resultado.data[index].nombre+'\n'
                                    vector.push({option: mioption[index], producto:resultado.data[index]})
                                }
                                list += '----------------------------------\n'
                                list += 'Envía una opción (ejemplo: *A*)'
                                status.set(msg.from, 0.89)
                                producto_buscado.set(msg.from, vector)
                                client.sendMessage(msg.from, list) 
                            }
                            else{
                                client.sendMessage(msg.from, 'No se encontraron productos relacionados.')
                            }
                        }
                        break;
                    case 0.791:
                        var miproductos= producto_mas_vendido.get(msg.from)
                        var validador = false
                        for (let index = 0; index < miproductos.length; index++) {
                            if(miproductos[index].option==msg.body.toUpperCase()){
                                productos.set(msg.from, miproductos[index].producto)
                                validador=true
                            }
                        }
                        if (validador) {
                            var media = ''
                            var miprecios = []
                            var miproducto = await axios(process.env.APP_URL+'api/producto/'+productos.get(msg.from).id)
                            if (miproducto.data.image) {
                                media = MessageMedia.fromFilePath('../../storage/app/public/'+miproducto.data.image)
                            } else {
                                media = MessageMedia.fromFilePath('imgs/default.png')
                            }
                            if (miproducto.data.precio != 0) {                                                    
                                var list = miproducto.data.nombre+' '+miproducto.data.precio+'Bs.\n'
                                list += miproducto.data.detalle+'\n'
                                list += '--------------------------\n'
                                list += '*A* .- Añadir a carrito\n'
                                list += '*B* .- Seguir comprando\n'
                                list += '----------------------------------\n'
                                list += 'Envía una opción (ejemplo: *A*)\n'
                                list += '----------------------------------\n'
                                list += 'Visita el Producto en Línea en:\n'
                                list += process.env.APP_URL+miproducto.data.negocio.slug+'/'+miproducto.data.slug
                                status.set(msg.from, 0.3)
                                miprecios=miproducto.data.precio
                                client.sendMessage(msg.from, media, {caption: list})                                                    
                            } else {                                 
                                var list = miproducto.data.nombre+'\n'
                                list += miproducto.data.detalle+'\n'
                                list += '--------------------------'+'\n'
                                for (let index = 0; index < miproducto.data.precios.length; index++) {
                                    var precio = await axios(process.env.APP_URL+'api/precio/'+miproducto.data.precios[index].precio_id)
                                    list += '*'+mioption[index]+'* .- '+precio.data.nombre+' '+precio.data.precio+'Bs.\n'
                                    miprecios.push({opcion: mioption[index], precio: precio.data.precio})
                                }                                                    
                                list += '----------------------------------\n'
                                list += 'Envía una opción (ejemplo: *A*)\n'
                                list += '----------------------------------\n'
                                list += 'Visita el Producto en Línea en:\n'
                                list += process.env.APP_URL+miproducto.data.negocio.slug+'/'+miproducto.data.slug
                                status.set(msg.from, 0.4)
                                client.sendMessage(msg.from, media, {caption: list})                                                    
                            }
                            if (miproducto.data.extra) {
                                var miextras = await axios(process.env.APP_URL+'api/producto/extra/negocio/'+miproducto.data.negocio_id)
                                carts.set(msg.from, {id: miproducto.data.id, nombre: miproducto.data.nombre, precio: miprecios, extra: miextras.data, negocio_id: miproducto.data.negocio_id, negocio_nombre: miproducto.data.negocio.nombre})
                            }else{
                                carts.set(msg.from, {id: miproducto.data.id, nombre: miproducto.data.nombre, precio: miprecios, extra: false, negocio_id: miproducto.data.negocio_id, negocio_nombre: miproducto.data.negocio.nombre})
                            }
                        }
                        break;
                    case 0.8: //listar productos segun categorias
                        var validar = false
                        // var micategoria = 0
                        var minegocio = negocios.get(msg.from)
                        
                        //Comentado Momentaneamente
                        // var micategorias = await axios(process.env.APP_URL+'api/negocio/categorias/'+minegocio.tipo_id)
                        // // console.log(categorias.data)
                        // for (let index = 0; index < micategorias.data.length; index++) {
                        //     if (mioption[index] === msg.body.toUpperCase()) {
                        //         validar = true
                        //         // micategoria = categorias.data[index].id
                        //         categorias.set(msg.from, micategorias.data[index])
                        //         break;
                        //     }
                        // }

                        var midatacat=option_categoria.get(msg.from)
                        for (let index = 0; index < midatacat.length; index++) {
                            if (midatacat[index].option === msg.body.toUpperCase()) {
                                validar = true
                                categorias.set(msg.from, midatacat[index].categoria)
                                break;
                            }
                        }
                        console.log(validar)
                        if (validar) {
                            var miproductos = await axios.post(process.env.APP_URL+'api/productos/categoria', {
                                negocio: minegocio.id,
                                categoria: categorias.get(msg.from).id
                            })

                            console.log(miproductos.data)

                            if (miproductos.data.length>0) {
                                var list = '*Productos Disponibles*\n'
                                for (let index = 0; index < miproductos.data.length; index++) {
                                    list += '*'+mioption[index]+'* .- '+miproductos.data[index].nombre+'\n'
                                }
                                list += '----------------------------------\n'
                                list += 'Envía una opción (ejemplo: *A*)'
                                status.set(msg.from, 0.9)
                                client.sendMessage(msg.from, list) 
                            }
                            else{
                                client.sendMessage(msg.from, 'Envía una opción válida')
                            }
                            
                        } else {
                            client.sendMessage(msg.from, 'Envía una opción válida')
                        }
                        break;
                    case 0.69:
                        var validar = false
                        var mitipo = tipos.get(msg.from)
                        var miresponse = await axios.post(process.env.APP_URL+'api/negocios/by/tipo', {
                            localidad: micliente.data.poblacion_id,
                            tipo: mitipo.id
                        })
                        for (let index = 0; index < miresponse.data.length; index++) {
                            if (mioption[index] === msg.body.toUpperCase()) {
                                validar = true
                                negocios.set(msg.from, miresponse.data[index])
                                break;
                            }
                        }
                        if (validar) {
                            var minegocio = negocios.get(msg.from)
                            var mitipo = tipos.get(msg.from)
                            if (micliente.data.poblacion_id== minegocio.poblacion_id) {
                                var miestado = (minegocio.estado == 1) ? 'Abierto' : 'Cerrado'
                                var list = '*'+minegocio.nombre.toUpperCase()+'*\n'
                                list += '*Estado:* '+miestado+'\n'
                                list += '*Horario:* '+minegocio.horario+'\n'
                                list += '*Dirección:* '+minegocio.direccion+'\n'
                                list += '*Tipo:* '+mitipo.nombre+'\n'
                                list += '----------------------------------'+'\n'
                                list += '*A*.- Buscar un Producto\n'
                                list += '*B*.- Mostrar los productos mas vendidos del Negocio\n' 
                                list += '----------------------------------\n'
                                list += 'Envía una opción (ejemplo: *A*)\n'
                                list += '----------------------------------'+'\n'
                                list += '*MENU*.- Regresar al Menú Principal.\n'
                                list += '----------------------------------'+'\n'
                                list += '*Nuestra tienda en línea en:*\n'
                                list += minegocio.link
                                var mimedia = minegocio.logo ? MessageMedia.fromFilePath('../../storage/app/public/'+minegocio.logo) : MessageMedia.fromFilePath('imgs/mitienda.png')
                                status.set(msg.from, 0.691)
                                client.sendMessage(msg.from, mimedia, {caption: list})                                
                            }
                            else{
                                client.sendMessage(msg.from, '📍 El negocio solicitado no se encuentra en tu Localidad 📍')
                            }           
                        } else {
                            client.sendMessage(msg.from, 'Envía una opción válida')
                        }
                        break;
                    case 0.691:
                        if (msg.body.toUpperCase() === 'A') {
                            var list=''
                            list += 'Envía el nombre, detalle, u otra descripción del producto para buscarlo.\n'
                            list += 'Ejemplo: *Producto X*\n' 
                            client.sendMessage(msg.from, list)
                            status.set(msg.from, 0.79)
                        }
                        else if((msg.body.toUpperCase() === 'B')){
                            var minegocio = negocios.get(msg.from)
                            var misproductos = await axios(process.env.APP_URL+'api/productos/negocio/rank/'+minegocio.id)
                            if (misproductos.data.length>0) {
                                var list=''
                                var vector=[]
                                list +='--------Productos Mas Vendidos---------\n'
                                for (let index = 0; index < misproductos.data.length; index++) {
                                    list += '*'+mioption[index]+'* .- '+misproductos.data[index].nombre+'\n'
                                    vector.push({option: mioption[index], producto:misproductos.data[index] })
                                }
                                list += '----------------------------------\n'
                                list += 'Envía una opción (ejemplo: *A*)\n'
                                list += '----------------------------------'+'\n'
                                list += '*MENU*.- Regresar al Menú Principal.\n'
                                list += '----------------------------------'+'\n'
                                list += '*Nuestra tienda en línea en:*\n'
                                list += minegocio.link
                                client.sendMessage(msg.from, list)
                                producto_mas_vendido.set(msg.from, vector)
                                status.set(msg.from, 0.791)
                            }
                           
                        }
                        else{
                            client.sendMessage(msg.from, 'Envía una opción válida')
                        }
                        break;
                    case 0.7:
                        var validar = false
                        var mitipo = tipos.get(msg.from)
                        var miresponse = await axios.post(process.env.APP_URL+'api/negocios/by/tipo', {
                            localidad: micliente.data.poblacion_id,
                            tipo: mitipo.id
                        })
                        for (let index = 0; index < miresponse.data.length; index++) {
                            if (mioption[index] === msg.body.toUpperCase()) {
                                validar = true
                                negocios.set(msg.from, miresponse.data[index])
                                break;
                            }
                        }
                        if (validar) {
                            var minegocio = negocios.get(msg.from)
                            var mitipo = tipos.get(msg.from)
                            if (micliente.data.poblacion_id== minegocio.poblacion_id) {
                                var miestado = (minegocio.estado == 1) ? 'Abierto' : 'Cerrado'
                                var list = '*'+minegocio.nombre.toUpperCase()+'*\n'
                                list += '*Estado:* '+miestado+'\n'
                                list += '*Horario:* '+minegocio.horario+'\n'
                                list += '*Dirección:* '+minegocio.direccion+'\n'
                                list += '*Tipo:* '+mitipo.nombre+'\n'
                                list += '----------------------------------'+'\n'
                                list += '*Catálogo*\n'
                                var contador=0
                                var midatacat=[]
                                var micategorias = await axios(process.env.APP_URL+'api/negocio/categorias/'+minegocio.tipo_id)
                                for (let index = 0; index < micategorias.data.length; index++) {
                                    var validador= await Categorias_Prod(micategorias.data[index].id, minegocio)
                                    if(validador){
                                        midatacat.push({option: mioption[contador], categoria:micategorias.data[index]})
                                        list += '*'+mioption[contador]+'* .- '+micategorias.data[index].nombre+'\n'
                                        contador+=1
                                    }
                                }
                                option_categoria.set(msg.from, midatacat)       
                                list += '----------------------------------\n'
                                list += 'Envía una opción (ejemplo: *A*)\n'
                                list += '----------------------------------'+'\n'
                                list += '*MENU*.- Regresar al Menú Principal.\n'
                                list += '----------------------------------'+'\n'
                                list += '*Nuestra tienda en línea en:*\n'
                                list += minegocio.link
                                var mimedia = minegocio.logo ? MessageMedia.fromFilePath('../../storage/app/public/'+minegocio.logo) : MessageMedia.fromFilePath('imgs/mitienda.png')
                                status.set(msg.from, 0.8)
                                client.sendMessage(msg.from, mimedia, {caption: list})                                
                            }
                            else{
                                client.sendMessage(msg.from, '📍 El negocio solicitado no se encuentra en tu Localidad 📍')
                            }           
                        } else {
                            client.sendMessage(msg.from, 'Envía una opción válida')
                        }
                        break;
                    case 0.6: //elegir opcion de tipos
                        var validar = false
                        var mitipos = await axios(process.env.APP_URL+'api/tipo/negocios')
                        for (let index = 0; index < mitipos.data.length; index++) {
                            if (mioption[index] === msg.body.toUpperCase()) {
                                validar = true
                                tipos.set(msg.from, mitipos.data[index])
                                break;
                            }
                        }
                        if (validar) {
                            var mitipo = tipos.get(msg.from)
                            var miresponse = await axios.post(process.env.APP_URL+'api/negocios/by/tipo', {
                                localidad: micliente.data.poblacion_id,
                                tipo: mitipo.id
                            })
                            if (miresponse.data.length>0 || mitipo.id==6) {
                                var list = '*🏚️ NEGOCIOS ('+mitipo.nombre+') 🏚️* \n'+micliente.data.localidad.nombre+'\n'
                                list += '----------------------------------\n'
                                for (let index = 0; index < miresponse.data.length; index++) {
                                    list += '*'+mioption[index]+'* .- '+miresponse.data[index].nombre+'\n'                                    
                                }
                                list += '----------------------------------\n'
                                list += 'Envía una opción (ejemplo: *A*)\n'
                                list += '----------------------------------\n'
                                list += '*MENU*.- Regresar al Menú Principal.\n'
                                list += '----------------------------------\n'
                                list += 'Visita nuestro marketplace en:\n'
                                list += process.env.APP_URL
                                if (mitipo.id==1) {
                                    status.set(msg.from, 0.7)
                                    client.sendMessage(msg.from, list)
                                }
                                else if(mitipo.id!= 1 && mitipo.id!=6){
                                    status.set(msg.from, 0.69)
                                    client.sendMessage(msg.from, list)
                                }
                                else if(mitipo.id==6){
                                    client.sendMessage(msg.from, 'En Desarrollo, próximamente disponible')
                                }
                            }
                            else{
                                client.sendMessage(msg.from, 'No hay Negocios disponibles de tipo: *'+mitipo.nombre+'* en su localidad.')
                            }
                        } 
                        else {
                            // client.sendMessage(msg.from, 'Envía una opción válida')
                            menu_principal(micliente, msg.from)
                        }
                        break;
                    case 0.4:
                        var miproducto = carts.get(msg.from)
                        var validar = false
                        for (let index = 0; index < miproducto.precio.length; index++) {
                            if (msg.body.toUpperCase() === miproducto.precio[index].opcion) {
                                carts.set(msg.from, {id: miproducto.id, nombre: miproducto.nombre, precio: miproducto.precio[index].precio, extra: miproducto.extra, negocio_id: miproducto.negocio_id, negocio_nombre: miproducto.negocio_nombre})
                                validar = true
                            }                                
                        }                     
                        if (validar) {
                            list = '*A* .- Añadir a carrito\n'  
                            list += '*B* .- Seguir comprando\n'   
                            if (miproducto.extra) {
                                list += '*C* .- Ver extras del producto\n'
                            }
                            list += '----------------------------------\n'
                            list += 'Envía una opción (ejemplo: *A*)'
                            status.set(msg.from, 0.3)
                            client.sendMessage(msg.from, list)
                        } else {
                            client.sendMessage(msg.from, 'Ingresa un opción válida')
                        }                  
                        break;
                    case 0.3: //agregar producto + precios + extreas
                        if (msg.body === 'A' || msg.body === 'a') {
                            // var miproducto = carts.get(msg.from)
                            var miproducto = await axios(process.env.APP_URL+'api/producto/'+productos.get(msg.from).id)
                            if (miproducto.data.extra) {
                                console.log(miproducto.data)
                                var list = 'Te puede interesar los *EXTRAS* para el producto seleccionado, '
                                // for (let index = 0; index < miproducto.data.extra.length; index++) {
                                //     list += miproducto.data.extra[index].nombre+'('+miproducto.data.extra[index].precio+'Bs), '
                                // }
                                list += '\n------------------------------------------\n'
                                list += 'Deseas agregar extras ?\n'
                                list += '*A* .- Si quiero\n'
                                list += '*B* .- Esta vez no\n'
                                list += '----------------------------------\n'
                                list += 'Envía una opción (ejemplo: *A*)'
                                client.sendMessage(msg.from, list)
                                status.set(msg.from, 0.5)
                             } //else if (msg.body === 'b' || msg.body === 'B') {
                            //     client.sendMessage(msg.from, 'Genial, ingresa una cantidad (1-9) para agregar el producto: *'+miproducto.nombre+'*')
                            //     status.set(msg.from, 1)
                            // }else{
                            //     client.sendMessage(msg.from, 'Envía una opción válida')
                            // }
                            else{
                                client.sendMessage(msg.from, 'Genial, ingresa una cantidad (1-9) para agregar el producto: *'+miproducto.data.nombre+'*')
                                status.set(msg.from, 1)                            
                            }                                    
                        }else if (msg.body === 'B' || msg.body === 'b') {
                            menu_principal(micliente, msg.from)
                            status.set(msg.from, 0.6)
                        }else if (msg.body === 'c' || msg.body === 'C') {
                            extras_view(msg.from)
                        } else {
                            client.sendMessage(msg.from, 'Envía una opción válida')
                        }
                        break;
                    case 0.5:
                        var miproducto = carts.get(msg.from)
                        if (msg.body === 'A' || msg.body === 'a') {   
                            var midata = {
                                product_id: miproducto.id,
                                product_name: miproducto.nombre,
                                chatbot_id: msg.from,
                                precio: miproducto.precio,
                                cantidad: 1,
                                negocio_id: miproducto.negocio_id,
                                negocio_name: miproducto.negocio_nombre
                            }
                            console.log(midata)
                            await axios.post(process.env.APP_URL+'api/chatbot/cart/add', midata)
                            extras_list(msg.from)
                            status.set(msg.from, 1.2)
                        }else if (msg.body === 'B' || msg.body === 'b') {
                            client.sendMessage(msg.from, 'Genial, ingresa una cantidad para agregar a tu carrito (1-9)\nProducto: *'+miproducto.nombre+'*')
                            status.set(msg.from, 1)
                        }else{
                            client.sendMessage(msg.from, 'Envía una opción válida')
                        }
                        break;
                    case 0.2: // cantidad del produto
                        if (msg.body === 'A' || msg.body === 'a') {
                            var midata = {
                                phone: msg.from,
                                modo: 'mensajero'
                            }
                            await axios.post(process.env.APP_URL+'api/cliente/modo/update', midata)
                            status.set(msg.from, 0)
                            menu_mensajero(msg.from)
                        }else if (msg.body === 'B' || msg.body === 'b') {
                            var midata = {
                                phone: msg.from,
                                modo: 'negocio'
                            }
                            await axios.post(process.env.APP_URL+'api/cliente/modo/update', midata)
                            status.set(msg.from, 0)
                            menu_negocio(msg.from)
                        }else if (msg.body === 'C' || msg.body === 'c') {
                            status.set(msg.from, 0)
                            menu_principal(micliente, msg.from)
                        } else {
                            client.sendMessage(msg.from, 'Ingresa una opción válida')
                        }
                        break;
                    case 1: //agragar a carrito desde Y
                        if (Number.isInteger(parseInt(msg.body)) && parseInt(msg.body) > 0 && parseInt(msg.body) <= 9) {
                            var miprodcuto = carts.get(msg.from)                                         
                            var midata = {
                                product_id: miprodcuto.id,
                                product_name: miprodcuto.nombre,
                                chatbot_id: msg.from,
                                precio: miprodcuto.precio,
                                cantidad: msg.body,
                                negocio_id: miprodcuto.negocio_id,
                                negocio_name: miprodcuto.negocio_nombre
                            }
                            await axios.post(process.env.APP_URL+'api/chatbot/cart/add', midata)
                            await cart_list(msg.from, micliente)
                            var list = '*A* .- Enviar pedido\n'  
                            list += '*B* .- Seguir comprando\n'
                            list += '----------------------------------\n'
                            list += 'Envía una opción (ejemplo: *A*)'
                            client.sendMessage(msg.from, list)
                            status.set(msg.from, 1.1)                                     
                        } else {
                            client.sendMessage(msg.from, 'Ingresa una cantidad válida (1-9)')
                        }
                        break;
                    case 1.1: 
                        if (msg.body === 'B' || msg.body === 'b'){
                            menu_principal(micliente, msg.from)
                            status.set(msg.from, 0.6)
                        }else if (msg.body === 'C' || msg.body === 'c'){
                            extras_list(msg.from)
                            status.set(msg.from, 1.2)
                        }else if (msg.body === 'A' || msg.body === 'a'){
                            var list = '🤖Inicio del pedido🤖\n'
                            list += '------------------------------------------\n'
                            list += '*A* .- Envía tu ubicacion (mapa), no olvides habilitar tu GPS.\n'
                            list += '*B* .- Envía tu ultima ubicacion registrada.\n' 
                            list += '*C* .- Seguir comprando.\n'   
                            list += '----------------------------------\n'
                            list += 'Envía una opción (ejemplo: *A*)'
                            client.sendMessage(msg.from, list)
                            status.set(msg.from, 1.3)
                        }else{
                            client.sendMessage(msg.from, 'Ingresa una opción válida')
                        }
                        break;
                    case 1.2: // set extras 
                        var miproducto = carts.get(msg.from)
                        var validar = false
                        var extra_id = 0
                        var extra_nombre = null
                        var milist = ['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K']
                        for (let index = 0; index < miproducto.extra.length; index++) {
                            if (milist[index] === msg.body.toUpperCase()) {
                                validar = true
                                extra_id = miproducto.extra[index].id
                                extra_nombre = miproducto.extra[index].nombre
                                break;
                            }
                        }
                        if (validar) {
                            extra_carts.set(msg.from, extra_id)
                            status.set(msg.from, 1.9)      
                            client.sendMessage(msg.from, 'Ingresa una cantidad (1-9)\nExtra: *'+extra_nombre+'*')
                        } else {
                            await extras_list(msg.from)
                            client.sendMessage(msg.from, 'Envía una opción válida')
                        }
                        break;
                    case 1.9: // set cantidad extra
                        if (Number.isInteger(parseInt(msg.body)) && parseInt(msg.body) <= 9 && parseInt(msg.body) > 0) {
                            var miproducto = carts.get(msg.from)
                            var extra = await axios(process.env.APP_URL+'api/producto/extra/get/'+extra_carts.get(msg.from))
                            var cart = await axios(process.env.APP_URL+'api/cart/producto/get/'+msg.from)
                            var midata = {
                                extra_id: extra.data.id,
                                precio: extra.data.precio,
                                cantidad: parseInt(msg.body),
                                total: parseFloat(extra.data.precio) * parseInt(msg.body),
                                carrito_id: cart.data.id,
                                producto_id: miproducto.id
                            }
                            await axios.post(process.env.APP_URL+'api/carrito/add/extras', midata)
                            await cart_list(msg.from, micliente)
                            list = '*A* .- Enviar pedido\n'
                            list += '*B* .- Seguir comprando\n'
                            list += '*C* .- Agregar mas extras\n'                              
                            list += '----------------------------------\n'
                            list += 'Envía una opción (ejemplo: *A*)'
                            client.sendMessage(msg.from, list)  
                            status.set(msg.from, 1.1)
                        } else {
                            client.sendMessage(msg.from, 'Ingresa una cantidad válida (1-9)')
                        }                         
                        break;
                    case 1.3: //estado para mapa
                        if (msg.body === 'A' || msg.body === 'a'){
                            client.sendMessage(msg.from, 'Envía tu ubicacion (mapa)\nNo olvides activar tu GPS.')
                            status.set(msg.from, 1.4)
                        } else if (msg.body === 'B' || msg.body === 'b'){
                            var milocation = await axios(process.env.APP_URL+'api/ubicacion/'+locations.get(msg.from))                           
                            if (milocation.data) {
                                client.sendMessage(msg.from, 'Ubicacion elegida: '+milocation.data.detalles)
                                pasarelas_list(msg.from)
                                status.set(msg.from, 1.6)
                            } else {
                                client.sendMessage(msg.from, 'No tienes ubicacion registrada\nEnvía tu ubicacion (mapa), No olvides activar tu GPS.')
                                status.set(msg.from, 1.4)
                            }
                        } else if (msg.body === 'C' || msg.body === 'c'){
                            status.set(msg.from, 0)
                            menu_principal(micliente, msg.from)
                        } else {
                            client.sendMessage(msg.from, 'Intenta con una opción válida.')
                        }
                        break;    
                    case 1.4:
                        client.sendMessage(msg.from, 'Envía tu ubicacion (mapa)\nNo olvides activar tu GPS.')
                        break;      
                    case 1.5: // registrar descripcion de la ubicacion
                            var milocation = locations.get(msg.from)
                            await axios.post(process.env.APP_URL+'api/ubicacion/update', {id: milocation, detalle: msg.body})                        
                            pasarelas_list(msg.from)
                            status.set(msg.from, 1.6)
                        break;                        
                    case 1.6: // enviar pedido
                        var micart = await axios.post(process.env.APP_URL+'api/chatbot/cart/get', {chatbot_id: msg.from})
                        if (micart.data.length != 0)
                        {
                            if (micart.data.length != 0 && micliente.data.ubicaciones.length != 0){
                                switch (true) {
                                    case (msg.body.toUpperCase() === 'A' || msg.body.toUpperCase() === 'a'):
                                        var chatbot_id=msg.from
                                        var pago_id=1
                                        var cliente_id=micliente
                                        var estado_cliente= 2.1
                                        await enviar_pedido(chatbot_id, pago_id, cliente_id, estado_cliente)
                                        // var midata = {
                                        //     chatbot_id: msg.from,
                                        //     pago_id: 1,
                                        //     cliente_id: micliente.data.id,
                                        //     ubicacion_id: locations.get(msg.from)
                                        // }
                                        // console.log(midata)

                                        // var newpedido = await axios.post(process.env.APP_URL+'api/pedido/save', midata)
                                        // //Lógica para Agrupar Negocios y actuzliar pedido--------------------------
                                        // var negocios3= await axios(process.env.APP_URL+'api/pedido/negocios/'+newpedido.data.id)
                                        // var send_negocios = []
                                        // var searchrep = []
                                        // for (let index = 0; index < negocios3.data.length; index++) {
                                        //     if(searchrep[index] === negocios3.data[index].negocio.id){
                                        //         // ?
                                        //     }else{
                                        //         var rep=0;
                                        //         for (let j = 0; j < send_negocios.length; j++) {
                                        //             if(send_negocios[j].id==negocios3.data[index].negocio.id){
                                        //                 rep+=1;
                                        //             }                                
                                        //         }
                                        //         if(rep==0){
                                        //             send_negocios.push(negocios3.data[index].negocio)
                                        //         }
                                        //     }
                                        //     searchrep.push(negocios3.data[index].negocio.id)
                                        // }
                                        // var midata2={
                                        //     pedido_id: newpedido.data.id,
                                        //     negocios: send_negocios.length,
                                        //     total_delivery: send_negocios.length * parseFloat(micliente.data.localidad.tarifa)
                                        // }
                                        // await axios.post(process.env.APP_URL+'api/update/pedido/delivery', midata2)
                                        // var mipedido = await axios(process.env.APP_URL+'api/pedido/'+newpedido.data.id)

                                        // var list = '🕦 *Pedido #'+mipedido.data.id+' Enviado* 🕦 \n Se te notificará el proceso de tu pedido, por este mismo medio, *GRACIAS POR TU PREFERENCIA*'
                                        // client.sendMessage(msg.from, list)
                                        // status.set(msg.from, 2.1)
                                        // //Enviar pedidos por negocio----------------------
                                        // for (let index = 0; index < send_negocios.length; index++) {
                                        //     var total_pedido_actual=0
                                        //     var total_extras =0
                                        //     var mismg=''
                                        //     mismg += 'Hola, *'+negocios3.data[index].negocio_name+'* tienes un pedido solicitado, con el siguiente detalle: \n'
                                        //     // mismg += '------------------------------------------\n'
                                        //     mismg += '*Pedido #:* '+negocios3.data[index].pedido_id+'\n'
                                        //     mismg += '*Cliente:* '+mipedido.data.cliente.nombre+'\n'
                                        //     // mismg += '*Fecha:* '+negocios3.data[index].published+'\n'
                                        //     mismg += '------------------------------------------\n'
                                        //     for (let j = 0; j < negocios3.data.length; j++) {
                                        //         if (send_negocios[index].id== negocios3.data[j].negocio.id) {
                                        //             total_pedido_actual+=negocios3.data[j].total
                                        //             mismg += '*Producto:* '+negocios3.data[j].cantidad+' '+negocios3.data[j].producto_name+'\n'
                                        //             var epp = 0
                                        //             var miextra = await axios(process.env.APP_URL+'api/extra/'+mipedido.data.productos[j].id)
                                        //             if (miextra.data) {
                                        //                 for (let x = 0; x < miextra.data.length; x++) {
                                        //                     mismg += '   -> '+miextra.data[x].cantidad+' '+miextra.data[x].extra.nombre+' (extra)\n'
                                        //                     total_extras+= parseFloat(miextra.data[x].total)
                                        //                     epp += total_extras

                                        //                 }
                                        //             }

                                        //             mismg += '*SubTotal:* '+(negocios3.data[j].total+epp)+' Bs.\n'
                                        //             // mismg += '------------------------------------------\n'
                                        //             var telef_negocio=negocios3.data[j].negocio.telefono
                                        //             var telef_negocio='591'+telef_negocio+'@c.us'
                                        //         }
                                        //     }
                                        //     mismg += '*Total:* '+(total_pedido_actual+total_extras)+' Bs.\n'
                                        //     // mismg += '*Extras:* '+total_extras+' Bs.\n'
                                        //     mismg += '------------------------------------------\n'
                                        //     mismg += 'La asignación a un Delivery está en proceso, ve realizando el pedido porfavor.'
                                        //     // client.sendMessage(telef_negocio, mismg)
                                        //     await axios.post(process.env.ACHATBOT_URL+'message', {
                                        //         phone: telef_negocio,
                                        //         message: mismg
                                        //     })
                                        // }

                                        // //ENVIAR PEDIDOS A MENSAJEROS-------------------------
                                        // ubic_cliente=''
                                        // ubic_cliente +='Ubicación del Cliente: '+mipedido.data.cliente.nombre+' - '
                                        // ubic_cliente +=mipedido.data.ubicacion.detalles
                                        // var mensajeroslibre = await axios(process.env.APP_URL+'api/mensajeros/libre/'+micliente.data.poblacion_id)
                                        // for (let index = 0; index < mensajeroslibre.data.length; index++) {   
                                        //     var total_mensajero = 0
                                        //     var cantidad_mensajero = 0 
                                        //     var mitext = '' 
                                        //     mitext += 'Hola, *'+mensajeroslibre.data[index].nombre+'* hay un pedido disponible con el siguiente detalle:\n'                       
                                        //     mitext += '------------------------------------------\n'
                                        //     mitext += '*Pedido :* #'+mipedido.data.id+'\n'
                                        //     mitext += '*Cliente :* '+mipedido.data.cliente.nombre+'\n'
                                        //     mitext += '*Ubicacion :* '+mipedido.data.ubicacion.detalles+'\n'
                                        //     // mitext += '------------------------------------------\n'
                                        //     var total_extras = 0
                                        //     for (let j = 0; j < mipedido.data.productos.length; j++) {
                                        //         mitext += mipedido.data.productos[j].cantidad+' '+mipedido.data.productos[j].producto_name+' ('+mipedido.data.productos[j].negocio_name+')\n'
                                        //         var miextra = await axios(process.env.APP_URL+'api/extra/'+mipedido.data.productos[j].id)
                                        //         if (miextra.data) {
                                        //             for (let x = 0; x < miextra.data.length; x++) {
                                        //                 mitext += '   -> '+miextra.data[x].cantidad+' '+miextra.data[x].extra.nombre+' (extra)\n'
                                        //                 total_extras+= parseFloat(miextra.data[x].total)
                                        //             }
                                        //         }
                                        //         total_mensajero += mipedido.data.productos[j].total 
                                        //         cantidad_mensajero += mipedido.data.productos[j].cantidad
                                        //     }
                                        //     mitext += '------------------------------------------\n'
                                        //     mitext += '*Productos:* '+cantidad_mensajero+' Cant.\n'                                               
                                        //     mitext += '*Negocios:* '+send_negocios.length+' Cant.\n'
                                        //     mitext += '*Extras:* '+total_extras+' Bs.\n'
                                        //     mitext += '*Delivery:* '+((send_negocios.length)*parseFloat(micliente.data.localidad.tarifa))+' Bs.\n'
                                        //     mitext += '*Total:* '+(total_extras+total_mensajero+((send_negocios.length)*parseFloat(micliente.data.localidad.tarifa)))+' Bs.\n'
                                        //     mitext += '------------------------------------------\n'
                                        //     mitext += 'QUIERES TOMAR EL PEDIDO *#'+mipedido.data.id+'* ?\n'
                                        //     mitext += '*A* .- Ver todos lo pedidos en cola\n'
                                        //     mitext += '----------------------------------\n'
                                        //     mitext += 'Envía una opción (ejemplo: *A*)'
                                        //     await axios.post(process.env.ACHATBOT_URL+'message', {
                                        //         phone: mensajeroslibre.data[index].telefono,
                                        //         message: mitext
                                        //     })
                                        // }
                                        // pedidos.set(msg.from, mipedido.data) // pedido
                                        break;
                                    case (msg.body.toUpperCase() === 'B' || msg.body.toUpperCase() === 'b'):
                                        var chatbot_id=msg.from
                                        var pago_id=2
                                        var cliente_id=micliente
                                        var estado_cliente= 1.7
                                        await enviar_pedido(chatbot_id, pago_id, cliente_id, estado_cliente)

                                        var mipedido_banipay=pedidos.get(msg.from)
                                        var bp_array={
                                            "paymentId": mipedido_banipay.id,
                                            "gloss": "Pago por Servicio Delivery y Productos",
                                            "amount": (mipedido_banipay.total + mipedido_banipay.total_delivery),
                                            "currency": "BOB",
                                            "singleUse": "true",
                                            "expiration": "1/00:05",
                                            "affiliate": process.env.BANIPAY_AFFILIATE,
                                            "business": process.env.BANIPAY_BUSINESS,
                                            "code": "",
                                            "type": "Banipay",
                                            "idCommercial": process.env.BANIPAY_IDCOMMERCIAL
                                        }
                                        var banipay = await axios.post("https://v2.banipay.me/api/pagos/qr-payment", bp_array)
                                        const media = new MessageMedia('image/png', banipay.data.image);
                                        var midata2={
                                            externalId:banipay.data.externalId,
                                            identifier: banipay.data.identifier,
                                            image: banipay.data.image,
                                            id_banipay: banipay.data.id
                                        }
                                        await axios.post(process.env.APP_URL+'api/banipay/dos/save', midata2)
                                        var list = '🕦 *Pedido #'+mipedido_banipay.id+' Enviado* 🕦 \n Se te notificará el proceso de tu pedido, por este mismo medio. \n 🎉 *GRACIAS POR TU PREFERENCIA* 🎉\n'
                                        list += '-----------------\n'
                                        list += 'Instrucciones para Pagar con QR: \n'
                                        list += 'Paso 1.- Escanea el QR desde la App de tu Banco \n'
                                        list += 'Paso 2.- Realiza la transacción\n'
                                        list += 'Paso 3.- Envía: una captura(imagen) comprobante del pago, para verificar el estado de la transacción'
                                        client.sendMessage(msg.from, media, {caption: list})
                                        status.set(msg.from, 1.7)
                                        break;
                                    case (msg.body.toUpperCase() === 'C' || msg.body.toUpperCase() === 'c'):
                                        client.sendMessage(msg.from, 'En Desarrollo, elige otro método de pago porfavor, disculpas por las molestias.')
                                        pasarelas_list(msg.from)
                                        break;
                                    default:
                                        await pasarelas_list(msg.from)
                                        client.sendMessage(msg.from, 'Envía una opción válida')
                                        break;
                                }
                            }
                        }else {
                            client.sendMessage(msg.from, 'Tu carrito está vacio')
                            status.set(msg.from, 0)
                        }
                        break;
                    case 1.7: // pago  QR
                        var mipedido = await axios(process.env.APP_URL+'api/pedido/'+(pedidos.get(msg.from)).id)
                        var transaccion= await axios("https://modal-flask-dev-q5zse.ondigitalocean.app/consultQR?id="+mipedido.data.banipaydos.externalId)
                        if (transaccion.data.status=="PROCESADO") {
                            client.sendMessage(msg.from, 'Envía una imagen o captura del pago por QR para comprobar el estado de la transacción.')
                        } else {
                            client.sendMessage(msg.from, 'Espera que el Delivery llegue con tu pedido porfavor.')
                        }
                        break;
                    case 2: // esperando pedido      
                        if (msg.body === 'A' || msg.body === 'a') {
                            var mipedido = await axios(process.env.APP_URL+'api/pedido/'+pedidos.get(msg.from).id)
                            client.sendMessage(msg.from, 'Genial, gracias por confiar en *appxi.net*, envía un comentario sobre nuestro servicio porfavor🙏🏼.')
                            await axios(process.env.APP_URL+'api/entregando/pedido/'+pedidos.get(msg.from).id)
                            // menu_principal(micliente, msg.from)
                            await axios.post(process.env.ACHATBOT_URL+'update', {
                                phone: mipedido.data.mensajero.telefono,
                                status: 0
                            })
                            await axios.post(process.env.ACHATBOT_URL+'message', {
                                phone: mipedido.data.mensajero.telefono,
                                message: 'El cliente confirmó el pedido #'+pedidos.get(msg.from).id,
                            })
                            status.set(msg.from, 3)
                        } else if (msg.body === 'B' || msg.body === 'b') {
                            var mipedido = await axios(process.env.APP_URL+'api/pedido/'+pedidos.get(msg.from).id)
                            await axios.post(process.env.ACHATBOT_URL+'message', {
                                phone: mipedido.data.mensajero.telefono,
                                message: 'El cliente NO confirmó el pedido #'+pedidos.get(msg.from).id+', porfavor envía una imagen.',
                            })
                            await axios.post(process.env.ACHATBOT_URL+'update', {
                                phone: mipedido.data.mensajero.telefono,
                                message: null,
                                status: 2.2
                            })
                            client.sendMessage(msg.from, 'El Administrador se pondrá en contacto con usted, lamentamos los inconvenientes.')
                            client.sendMessage(micliente.data.localidad.admin_phone, 'Hay un Problema con el Pedido #'+pedidos.get(msg.from).id+', porfavor ponte en contacto con ambas partes.')
                            status.set(msg.from, 2.2)
                        }else{
                            client.sendMessage(msg.from, 'Envía una opción válida.')
                        }                
                        break;
                    case 2.2:
                        client.sendMessage(msg.from, 'Espere que el Administrador se ponga en contacto con usted porfavor.')
                        break;
                    case 2.1: 
                        client.sendMessage(msg.from, 'Espera a que el sistema asigne un delivery para tu pedido.')
                        break;
                    case 3: // esperando comentario    
                        var midata = {
                            telefono: msg.from,
                            description: msg.body
                        }
                        var pedido_comentario = await axios.post(process.env.APP_URL+'api/pedido/comentario', midata)
                        var mitext = 'Tu comentario: *'+msg.body+'* respecto al pedido *#'+pedido_comentario.data.id+'*, fue registrado exitosamente.\n'
                        mitext+= 'Como último paso te pedimos que mandes una puntuacion del 1-10 para calificar el pedido.'
                        status.set(msg.from, 3.1)
                        // pedidos.delete(msg.from)
                        // pedidosencola.delete(pedido_comentario.data.mensajero.telefono)                                
                        client.sendMessage(msg.from, mitext)

                        //habiliar a mensajero
                        // status_mensajero.set(pedido_comentario.data.mensajero.telefono, 0)
                        // listar pedidos en cola
                        // client.sendMessage(pedido_comentario.data.mensajero.telefono, 'Ahora estas libre para recibir mas pedidos, estate atento al proximo.')
                        break;
                    case 3.1://Calificando el Pedido
                        if (Number.isInteger(parseInt(msg.body)) && parseInt(msg.body) > 0 && parseInt(msg.body) <= 10) {
                            var midata={
                                puntuacion: parseInt(msg.body)*10,
                                telefono: msg.from
                            }
                            var pedido_puntuacion = await axios.post(process.env.APP_URL+'api/chatbot/pedido/puntuacion', midata)
                            var mitext = 'Tu puntuación: *'+msg.body+'* respecto al pedido *#'+pedido_puntuacion.data.id+'*, fue registrado exitosamente, Gracias por tu preferencia.'
                            client.sendMessage(msg.from, mitext)
                            menu_principal(micliente, msg.from)
                            status.set(msg.from, 0.6)

                        }
                        else{
                            client.sendMessage(msg.from, 'Ingresa una cantidad válida (1-10)')
                        }
                        break;
                    case 4: //opcion para la app
                        if (msg.body.toUpperCase() == 'A') {
                            var list = '🤖Inicio del pedido🤖\n'
                            list += '------------------------------------------\n'
                            list += '*A* .- Envía tu ubicacion (mapa), no olvides habilitar tu GPS.\n'
                            list += '*B* .- Envía tu ultima ubicacion registrada.\n' 
                            list += '*C* .- Seguir comprando.\n'   
                            list += '----------------------------------\n'
                            list += 'Envía una opción (ejemplo: *A*)'
                            client.sendMessage(msg.from, list)
                            status.set(msg.from, 1.3)
                        }else if(msg.body.toUpperCase() == 'B') {
                            status.set(msg.from, 0.6)
                            menu_principal(micliente, msg.from)
                        }else if(msg.body.toUpperCase() == 'C') {
                            await axios.post(process.env.APP_URL+'api/chatbot/cart/clean', {chatbot_id: msg.from})   
                            status.set(msg.from, 0.6)  
                            menu_principal(micliente, msg.from)
                        } else {
                            client.sendMessage(msg.from, 'Envía una opción válida')
                        }
                        break;
                    default:
                        client.sendMessage(msg.from, 'Intenta con otra opción\nEstado: '+status.get(msg.from))
                        break;
                    }
            }else{
                if (msg.body.length >= 8) {
                    var miclienteu = await axios.post(process.env.APP_URL+'api/cliente/update/nombre', {id: micliente.data.id, nombre: msg.body})
                    menu_principal(miclienteu, msg.from)
                    status.set(msg.from, 0.6)
                } else {
                    var list = '*Bienvenido*, soy el 🤖CHATBOT🤖 DE : '+process.env.APP_NAME+'\n'
                    list += '*🙋‍♀️Cual es tu Nombre Completo ?🙋‍♂️* \n'
                    list += '*8 caracteres minimo* \n'
                    client.sendMessage(msg.from, list)
                }
            }
            break;
        case 'image':
            switch (status.get(msg.from)) {
                case 1.7:// guardar comprobante para cualquier cosa....
                    var mipedido = await axios(process.env.APP_URL+'api/pedido/'+(pedidos.get(msg.from)).id)
                    var transaccion= await axios("https://modal-flask-dev-q5zse.ondigitalocean.app/consultQR?id="+mipedido.data.banipaydos.externalId)
                    if (transaccion.data.status=="PROCESADO") {
                        client.sendMessage(msg.from, 'Transacción de la Venta #'+mipedido.data.id+' aún *NO Realizada*')
                    } else {
                        client.sendMessage(msg.from, 'Transacción de la Venta #'+(pedidos.get(msg.from)).id+' realizada exitosamente')
                        var send_negocios= await negocios_pedido(pedidos.get(msg.from))
                        for (let index = 0; index < send_negocios.length; index++) {
                            mitext= ''
                            mitext+= 'El Pedido #'+pedidos.get(msg.from)+' del Cliente '+mipedido.data.cliente.nombre+'  fue pagado exitosamente por transferencia \n'
                            var telefono_negocio= '591'+send_negocios[index].telefono+'@c.us'
                            await axios.post(process.env.ACHATBOT_URL+'message', {
                                phone: telefono_negocio,
                                message: mitext
                            })
                            // client.sendMessage(telefono_negocio, mitext)                           
                        }

                        await axios(process.env.APP_URL+'api/chatbot/pedido/pago/estado/'+mipedido.data.id)

                        if (mipedido.data.mensajero.id!=1) {
                            await axios.post(process.env.ACHATBOT_URL+'message', {
                                phone: mipedido.data.mensajero.telefono,
                                message: 'El Pedido #'+(pedidos.get(msg.from)).id+' del Cliente '+mipedido.data.cliente.nombre+'  fue pagado exitosamente por transferencia'
                            })
                        }
                       
                        // client.sendMessage(mipedido.data.mensajero.telefono, 'El Pedido #'+pedidos.get(msg.from)+' del Cliente '+mipedido.data.cliente.nombre+'  fue pagado exitosamente por transferencia')
                        // status.set(msg.from, 2)
                    }
                    break;
                default:
                    client.sendMessage(msg.from, 'Intenta con otra opción' )
                    break;
            }
            break;
        case 'location':
            switch (status.get(msg.from)) {
                case 1.4:
                    var micart = await axios.post(process.env.APP_URL+'api/chatbot/cart/get', {chatbot_id: msg.from})
                    if (micart.data.length != 0)
                    {
                        var micliente = await axios(process.env.APP_URL+'api/cliente/'+msg.from)
                        var midata = {
                            cliente_id: micliente.data.id,
                            latitud: msg.location.latitude,
                            longitud: msg.location.longitude
                        }
                        var miubicacion = await axios.post(process.env.APP_URL+'api/ubicacion/save', midata)
                        locations.set(msg.from, miubicacion.data.id)
                        client.sendMessage(msg.from, 'Gracias, para poder llegar mas rápido a tu ubicación (mapa), envía una descripción de tu ubicación\nEjemplo: Al frente de la tienda X.')
                        status.set(msg.from, 1.5)//estado mapa
                    } else {
                        client.sendMessage(msg.from, '*Tu carrito está vacio*\n *A* .- TODOS LOS NEGOCIOS')
                    }
                    break;
                default:
                    break;
            }
            break;
        case 'call_log':

            break;
        default:
            break;
    }
})

app.get('/', async (req, res) => {
    res.render('index', {count: micount, wweb: miwweb});
});

app.post('/getpin', async (req, res) => {
    var phone= '591'+req.body.phone+'@c.us'
    var newpassword=Math.random().toString().substring(2, 6)
    var midata={
        phone: phone,
        nombre: req.body.nombre,
        localidad: req.body.localidad,
        pin: newpassword,
        correo: req.body.phone+'@appxi.net'
    }
    var micliente = await axios.post(process.env.APP_URL+'api/app/cliente', midata)
    message = 'Hola, *'+micliente.data.nombre+'* soy el 🤖CHATBOT🤖 de: *'+process.env.APP_NAME+'* tu asistente digital en ventas y viajes, visita tu comercio o taxi preferido.\n'
    message += '----------------------------------\n'
    message +='Tu Pin para confirmar tu identidad es: *'+newpassword+'*\n'
    message += '----------------------------------\n'
    message += 'Visita nuestro marketplace en:\n'
    message += process.env.APP_URL
    client.sendMessage(phone, message)       
    res.send('Mensaje Enviado') 
});

app.post('/setpin', async (req, res) => {
    var miphone= '591'+req.body.phone +'@c.us'
    var validar = await axios.post(process.env.APP_URL+'api/app/setauth', {
        phone: miphone,
        pin: req.body.pin
    })
    console.log(validar.data.user)
    if (!validar.data.message) {        
        client.sendMessage(miphone, 'Felicidades, Credenciales Correctas')       
        console.log(validar.data.user)
        res.send(validar.data)

    }
    else{        
        client.sendMessage(miphone, 'Error, Credenciales Incorrectas')       
        res.send(false)
    }
});

app.post('/cart', async (req, res) => {
    var message = req.body ? req.body.message : req.query.message
    var phone = req.body ? req.body.phone : req.query.phone
    var mistatus = req.body ? req.body.status : req.query.status
    var micliente = await axios(process.env.APP_URL+'api/cliente/'+phone)
    if (mistatus != 2) {
        await cart_list(phone, micliente)
    }
    client.sendMessage(phone, message)       
    status.set(phone, mistatus)
    res.send('Mensaje Enviado') 
});

app.post('/reset/cliente', async (req, res) => {
    var phone = req.body ? req.body.phone : req.query.phone
    var mistatus = req.body ? req.body.mistatus : req.query.mistatus
    await reset_cliente(phone, mistatus)
    res.send('Mensaje Enviado') 
});

app.post('/message', (req, res) => {
    var message = req.body ? req.body.message : req.query.message
    var phone = req.body ? req.body.phone : req.query.phone
    client.sendMessage(phone, message)  
    res.send('Mensaje Enviado') 
});

app.post('/newproduct', async (req, res) => {
    var message = req.body.message
    var phone = req.body.phone    
    var micliente = await axios(process.env.APP_URL+'api/cliente/'+phone)
    await cart_list(phone, micliente)
    client.sendMessage(phone, message)  
    status.set(phone, 4)
    res.send('Mensaje Enviado') 
});

app.post('/savepedido', async (req, res) => {
    var message = 'Pedido Realizado'
    var phone = req.body.phone    
    var pasarela = req.body.pasarela    
    var location = req.body.location  
    // var cliente_id = await axios()
    var micliente = await axios(process.env.APP_URL+'api/cliente/'+phone)
    var efectivo = 2.1 // QR 1.7
    enviar_pedido(phone, pasarela, micliente, efectivo)
    client.sendMessage(phone, message)  
    // status.set(phone, 0)
    res.send('Mensaje Enviado') 
});

const Categorias_Prod = async(categoria_id, minegocio) =>{
    var validador= false;
    for (let index = 0; index < minegocio.productos.length; index++) {
        if (minegocio.productos[index].categoria_id==categoria_id) {
            validador=true;
        }
    }
    return validador;
}

const negocios_pedido = async(id) =>{
    var negocios3= await axios(process.env.APP_URL+'api/pedido/negocios/'+id)
    var send_negocios = []
    var searchrep = []
    for (let index = 0; index < negocios3.data.length; index++) {
        if(searchrep[index] === negocios3.data[index].negocio.id){
        }else{
            var rep=0;
            for (let j = 0; j < send_negocios.length; j++) {
                if(send_negocios[j].id==negocios3.data[index].negocio.id){
                    rep+=1;
                }                                
            }
            if(rep==0){
                send_negocios.push(negocios3.data[index].negocio)
            }
        }
        searchrep.push(negocios3.data[index].negocio.id)
    }
    return send_negocios;
}

const negocios_carrito = async(chatbot_id) =>{
    var negocios3= await axios(process.env.APP_URL+'api/cart/negocios/'+chatbot_id)
    var send_negocios = []
    var searchrep = []
    for (let index = 0; index < negocios3.data.length; index++) {
        if(searchrep[index] === negocios3.data[index].negocio.id){
        }else{
            var rep=0;
            for (let j = 0; j < send_negocios.length; j++) {
                if(send_negocios[j].id==negocios3.data[index].negocio.id){
                    rep+=1;
                }                                
            }
            if(rep==0){
                send_negocios.push(negocios3.data[index].negocio)
            }
        }
        searchrep.push(negocios3.data[index].negocio.id)
    }
    return send_negocios;
}

const menu_principal = async (micliente, phone) => {
    var mioption = ['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z']
    var list = 'Hola, *'+micliente.data.nombre+'* soy el 🤖CHATBOT🤖 de: *'+process.env.APP_NAME+'* tu asistente digital en ventas y viajes, visita tu comercio o taxi preferido.\n'
    list += '----------------------------------\n'
    var mitipos = await axios(process.env.APP_URL+'api/tipo/negocios')
    for (let index = 0; index < mitipos.data.length; index++) {
        list += '*'+mioption[index]+'* .- '+mitipos.data[index].nombre+'\n'
    }
    list += '----------------------------------\n'
    list += 'Envía una opción (ejemplo: *A*)\n'
    list += '----------------------------------\n'
    list += 'Visita nuestro marketplace en:\n'
    list += process.env.APP_URL
    client.sendMessage(phone, list)
}

const cart_list = async (phone, micliente) => {
    var miresponse = await axios.post(process.env.APP_URL+'api/chatbot/cart/get', {chatbot_id: phone})
    // var micant = await axios(process.env.APP_URL+'api/pedido/carrito/negocios/'+phone)
    var send_negocios= await negocios_carrito(phone)
    var len_ubi= micliente.data.ubicaciones.length
    var ubicacion_actual = micliente.data.ubicaciones[(len_ubi-1)]
    var total_delivery=await calcular_delivery(send_negocios, ubicacion_actual)
    if (miresponse.data.length != 0) {
        var list = '*Lista de productos en tu carrito*\n'
        var total = 0
        var total_extras = 0
        list += '------------------------------------------\n'
        for (let index = 0; index < miresponse.data.length; index++) {
            list += miresponse.data[index].cantidad+' '+miresponse.data[index].producto_name+' '+miresponse.data[index].precio+'Bs. ('+miresponse.data[index].negocio_name+')\n'    
            if (miresponse.data[index].extras.length != 0) {
                for (let j = 0; j < miresponse.data[index].extras.length; j++) {
                    var extra = await axios(process.env.APP_URL+'api/producto/extra/get/'+miresponse.data[index].extras[j].extra_id)
                    list += '   -> '+miresponse.data[index].extras[j].cantidad+' '+extra.data.nombre+' '+miresponse.data[index].extras[j].precio+'Bs. (extra)\n'
                    total_extras+= parseFloat(miresponse.data[index].extras[j].total)
                }
            }
            total += miresponse.data[index].precio * miresponse.data[index].cantidad
        }
        list += '\nEnvía: *CLEAN* para vaciar\n'
        list += '------------------------------------------ \n'
        list += '*PRODUCTOS* .- '+total+' Bs. \n'
        list += '*EXTRAS* .- '+total_extras+' Bs. \n'
        list += '*DELIVERY* .- '+total_delivery+' Bs.\n'
        list += '*TOTAL* .- '+(total + total_extras + parseFloat(total_delivery))+' Bs.'
        client.sendMessage(phone, list)
    } else {
        client.sendMessage(phone, 'Tu carrito está vacio')
    }
}

const extras_list = async (phone) => {
    var miproducto = carts.get(phone)
    var milist = ['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z']
    var list = '*Elije un extra para agreagar a tu producto:* \n'+miproducto.nombre+'\n'
    list += '------------------------------------------\n'
    for (let index = 0; index < miproducto.extra.length; index++) {
        list += '*'+milist[index]+'* .- '+miproducto.extra[index].nombre+' ('+miproducto.extra[index].precio+'Bs.)\n'
    }
    list += '----------------------------------\n'
    list += 'Envía una opción (ejemplo: *A*)'
    client.sendMessage(phone, list)
}

const extras_view = async (phone) => {
    var miprodcuto = carts.get(phone)
    var product = await axios(process.env.APP_URL+'api/producto/'+miprodcuto.id)
    var miresponse = await axios(process.env.APP_URL+'api/producto/extra/negocio/'+product.data.negocio_id)
    var list = ''
    for (let index = 0; index < miresponse.data.length; index++) {
        list += '*X'+miresponse.data[index].id+'* .- '+miresponse.data[index].nombre+' ('+miresponse.data[index].precio+' Bs.)\n'
        extras.set('X'+miresponse.data[index].id, miresponse.data[index].id)
    }
    client.sendMessage(phone, list)
}

const negocios_list = async (phone, micliente) =>{
    var miresponse = await axios(process.env.APP_URL+'api/negocios/'+micliente.data.poblacion_id)
    var list = '*🏚️ NEGOCIOS DISPONIBLES 🏚️* \n'
    list += micliente.data.localidad.nombre+'\n'
    list += '----------------------------------'+' \n'
    for (let index = 0; index < miresponse.data.length; index++) {
        list += '*N'+miresponse.data[index].id+'* .- '+miresponse.data[index].nombre+' - ('+miresponse.data[index].productos.length+')\n'
        negocios.set('N'+miresponse.data[index].id, miresponse.data[index].id);
    }
    list += 'Envía una opción ejemplo: (N'+miresponse.data[0].id+')\n'
    list += '----------------------------------\n'
    list += '*MENU*.- Regresar al Menú Principal.\n'
    list += '----------------------------------\n'
    list += 'Visita nuestro marketplace en:\n'
    list += process.env.APP_URL
    client.sendMessage(phone, list)
}

const pasarelas_list = async (phone) =>{
    var pagos = await axios(process.env.APP_URL+'api/chatbot/pasarelas/get')
    var miopcion = ['A', 'B', 'C', 'D']
    var list = '*PUEDES PAGAR TU PEDIDO POR ESTOS METODOS*\n'
    list += '----------------------------------\n'
    for (let index = 0; index < pagos.data.length; index++) {
        list += '*'+miopcion[index]+'* .- '+pagos.data[index].title+'\n'
    }
    list += '----------------------------------\n'
    list += 'Envía una opción (ejemplo: *A*)'
    client.sendMessage(phone, list)
}

const reset_cliente = async (phone, mistatus) =>{
    status.set(phone, mistatus)
    carts.delete(phone)
    categorias.delete(phone)
    extra_carts.delete(phone)
    extras.delete(phone)
    locations.delete(phone)
    negocios.delete(phone)
    option_categoria.delete(phone)
    pedidos.delete(phone)
    pedidosencola.delete(phone)
    producto_buscado.delete(phone)
    producto_mas_vendido.delete(phone)
    productos.delete(phone)
    tipos.delete(phone)

    client.sendMessage(phone, 'Se limpió su historial, está libre y habilitado para realizar pedidos.')
}

const enviar_pedido = async (chatbot_id, pago_id, cliente_id, estado_cliente) => {
    var midata = {
        chatbot_id: chatbot_id,
        pago_id: pago_id,
        cliente_id: cliente_id.data.id,
        ubicacion_id: locations.get(chatbot_id)
    }
    console.log(midata)

    var newpedido = await axios.post(process.env.APP_URL+'api/pedido/save', midata)
    //Lógica para Agrupar Negocios y actuzliar pedido--------------------------
    var negocios3= await axios(process.env.APP_URL+'api/pedido/negocios/'+newpedido.data.id)
    var send_negocios = []
    var searchrep = []
    for (let index = 0; index < negocios3.data.length; index++) {
        if(searchrep[index] === negocios3.data[index].negocio.id){
            // ?
        }else{
            var rep=0;
            for (let j = 0; j < send_negocios.length; j++) {
                if(send_negocios[j].id==negocios3.data[index].negocio.id){
                    rep+=1;
                }                                
            }
            if(rep==0){
                send_negocios.push(negocios3.data[index].negocio)
            }
        }
        searchrep.push(negocios3.data[index].negocio.id)
    }
    var len_ubi= cliente_id.data.ubicaciones.length
    var ubicacion_actual = cliente_id.data.ubicaciones[(len_ubi-1)]
    var total_delivery=await calcular_delivery(send_negocios, ubicacion_actual)

    var midata2={
        pedido_id: newpedido.data.id,
        negocios: send_negocios.length,
        total_delivery: total_delivery
    }
    await axios.post(process.env.APP_URL+'api/update/pedido/delivery', midata2)
    var mipedido = await axios(process.env.APP_URL+'api/pedido/'+newpedido.data.id)

    var list = '🕦 *Pedido #'+mipedido.data.id+' Enviado* 🕦 \n Se te notificará el proceso de tu pedido, por este mismo medio, *GRACIAS POR TU PREFERENCIA*'
    client.sendMessage(chatbot_id, list)
    status.set(chatbot_id, estado_cliente)
    //Enviar pedidos por negocio----------------------
    for (let index = 0; index < send_negocios.length; index++) {
        var total_pedido_actual=0
        var total_extras =0
        var mismg=''
        mismg += 'Hola, *'+negocios3.data[index].negocio_name+'* tienes un pedido solicitado, con el siguiente detalle: \n'
        // mismg += '------------------------------------------\n'
        mismg += '*Pedido #:* '+negocios3.data[index].pedido_id+'\n'
        mismg += '*Cliente:* '+mipedido.data.cliente.nombre+'\n'
        // mismg += '*Fecha:* '+negocios3.data[index].published+'\n'
        mismg += '------------------------------------------\n'
        for (let j = 0; j < negocios3.data.length; j++) {
            if (send_negocios[index].id== negocios3.data[j].negocio.id) {
                total_pedido_actual+=negocios3.data[j].total
                mismg += '*Producto:* '+negocios3.data[j].cantidad+' '+negocios3.data[j].producto_name+'\n'
                var epp = 0
                var miextra = await axios(process.env.APP_URL+'api/extra/'+mipedido.data.productos[j].id)
                if (miextra.data) {
                    for (let x = 0; x < miextra.data.length; x++) {
                        mismg += '   -> '+miextra.data[x].cantidad+' '+miextra.data[x].extra.nombre+' (extra)\n'
                        total_extras+= parseFloat(miextra.data[x].total)
                        epp += total_extras

                    }
                }

                mismg += '*SubTotal:* '+(negocios3.data[j].total+epp)+' Bs.\n'
                // mismg += '------------------------------------------\n'
                var telef_negocio=negocios3.data[j].negocio.telefono
                var telef_negocio='591'+telef_negocio+'@c.us'
            }
        }
        mismg += '*Total:* '+(total_pedido_actual+total_extras)+' Bs.\n'
        // mismg += '*Extras:* '+total_extras+' Bs.\n'
        mismg += '------------------------------------------\n'
        mismg += 'La asignación a un Delivery está en proceso, ve realizando el pedido porfavor.'
        // client.sendMessage(telef_negocio, mismg)
        await axios.post(process.env.ACHATBOT_URL+'message', {
            phone: telef_negocio,
            message: mismg
        })
    }

    //ENVIAR PEDIDOS A MENSAJEROS-------------------------
    ubic_cliente=''
    ubic_cliente +='Ubicación del Cliente: '+mipedido.data.cliente.nombre+' - '
    ubic_cliente +=mipedido.data.ubicacion.detalles
    var mensajeroslibre = await axios(process.env.APP_URL+'api/mensajeros/libre/'+cliente_id.data.poblacion_id)
    for (let index = 0; index < mensajeroslibre.data.length; index++) {   
        var total_mensajero = 0
        var cantidad_mensajero = 0 
        var mitext = '' 
        mitext += 'Hola, *'+mensajeroslibre.data[index].nombre+'* hay un pedido disponible con el siguiente detalle:\n'                       
        mitext += '------------------------------------------\n'
        mitext += '*Pedido :* #'+mipedido.data.id+'\n'
        mitext += '*Cliente :* '+mipedido.data.cliente.nombre+'\n'
        mitext += '*Ubicacion :* '+mipedido.data.ubicacion.detalles+'\n'
        // mitext += '------------------------------------------\n'
        var total_extras = 0
        for (let j = 0; j < mipedido.data.productos.length; j++) {
            mitext += mipedido.data.productos[j].cantidad+' '+mipedido.data.productos[j].producto_name+' ('+mipedido.data.productos[j].negocio_name+')\n'
            var miextra = await axios(process.env.APP_URL+'api/extra/'+mipedido.data.productos[j].id)
            if (miextra.data) {
                for (let x = 0; x < miextra.data.length; x++) {
                    mitext += '   -> '+miextra.data[x].cantidad+' '+miextra.data[x].extra.nombre+' (extra)\n'
                    total_extras+= parseFloat(miextra.data[x].total)
                }
            }
            total_mensajero += mipedido.data.productos[j].total 
            cantidad_mensajero += mipedido.data.productos[j].cantidad
        }
        mitext += '------------------------------------------\n'
        mitext += '*Productos:* '+cantidad_mensajero+' Cant.\n'                                               
        mitext += '*Negocios:* '+send_negocios.length+' Cant.\n'
        mitext += '*Extras:* '+total_extras+' Bs.\n'
        mitext += '*Delivery:* '+total_delivery+' Bs.\n'
        mitext += '*Total:* '+(total_extras+total_mensajero+parseFloat(total_delivery))+' Bs.\n'
        mitext += '------------------------------------------\n'
        mitext += 'QUIERES TOMAR EL PEDIDO *#'+mipedido.data.id+'* ?\n'
        mitext += '*A* .- Ver todos lo pedidos en cola\n'
        mitext += '----------------------------------\n'
        mitext += 'Envía una opción (ejemplo: *A*)'
        await axios.post(process.env.ACHATBOT_URL+'message', {
            phone: mensajeroslibre.data[index].telefono,
            message: mitext
        })
    }
    pedidos.set(chatbot_id, mipedido.data) // pedido
}


const calcular_delivery = async (send_negocios, milocation) => {
    var calc_envio=0
    switch (send_negocios.length) {
        case 1:
            //var minegocio = await axios('https://appxi.net/api/app/negocio/by/'+minegocios[0])
            var tiempo_total=0
            var distancia_total=0
            var points = {
                origin: { lat: parseFloat(send_negocios[0].latitud), lng: parseFloat(send_negocios[0].longitud) },
                destination: { lat: parseFloat(milocation.latitud), lng: parseFloat(milocation.longitud) },
                //travelMode: google.maps.DirectionsTravelMode.DRIVING
            };	
                // var directionsService = new google.maps.DirectionsService();
                // directionsService.route(points, async function(response, status) {
                //     if (status == google.maps.DirectionsStatus.OK) {
                //         directionsDisplay.setDirections(response)
                //         tiempo_total=response.routes[0].legs[0].duration.value
                //         distancia_total= response.routes[0].legs[0].distance.value
                //         calc_envio= await calcular_costo(response.routes[0].legs[0].distance.value)
                //         console.log(response.routes[0].legs[0].duration.value)
                //         console.log(response.routes[0].legs[0].distance.value)
                //         console.log(await calcular_costo(response.routes[0].legs[0].distance.value))
                //     }
                // });
                const miclient = new googleMapsClient({})
                miclient
                    .elevation({
                        params: {
                        locations: [{ lat: 45, lng: -110 }],
                        key: "AIzaSyDH_m3M3RHw6s6AZeubtUZ8XIW7jC2MjCU",
                        },
                        timeout: 1000, // milliseconds
                    })
                    .then((r) => {
                        console.log(r.data.results[0].elevation);
                    })
                    .catch((e) => {
                        console.log(e.response.data.error_message);
                    });
            break;
        case 2:

            // var minegocio1 = await axios('https://appxi.net/api/app/negocio/by/'+minegocios[0])
            // var minegocio2 = await axios('https://appxi.net/api/app/negocio/by/'+minegocios[1])
            var tiempo_total = 0
            var distancia_total = 0					
            var points1 = {
                origin: { lat: parseFloat(send_negocios[0].latitud), lng: parseFloat(send_negocios[0].data.longitud) },
                destination: { lat: parseFloat(send_negocios[1].latitud), lng: parseFloat(send_negocios[1].longitud) },
                travelMode: google.maps.DirectionsTravelMode.DRIVING
            };
           
            directionsService1.route(points1, async function(response, status) {
                if (status == google.maps.DirectionsStatus.OK) {
                    directionsDisplay1.setDirections(response)
                    tiempo_total += response.routes[0].legs[0].duration.value
                    distancia_total += response.routes[0].legs[0].distance.value
                    // console.log(response.routes[0].legs[0].distance.value)
                }
            });					
            var points2 = {
                origin: { lat: parseFloat(send_negocios[0].latitud), lng: parseFloat(send_negocios[0].longitud) },
                destination: { lat: parseFloat(milocation.latitud), lng: parseFloat(milocation.longitud) },
                travelMode: google.maps.DirectionsTravelMode.DRIVING
            };	
            directionsService2.route(points2, async function(response, status) {
                if (status == google.maps.DirectionsStatus.OK) {
                    directionsDisplay2.setDirections(response)
                    tiempo_total += response.routes[0].legs[0].duration.value
                    distancia_total += response.routes[0].legs[0].distance.value
                    // console.log(response.routes[0].legs[0].distance.value)
                    // $("#mitiempo").html(formatMoney(tiempo_total/60, ".", ","))
                    // $("#midistancia").html(formatMoney(distancia_total/1000, ".", ","))
                    calc_envio = await calcular_costo(distancia_total)
                    
                }
            });					
        
            break;
        case 3:
            // var minegocio1 = await axios('https://appxi.net/api/app/negocio/by/'+minegocios[0])
            // var minegocio2 = await axios('https://appxi.net/api/app/negocio/by/'+minegocios[1])
            // var minegocio3 = await axios('https://appxi.net/api/app/negocio/by/'+minegocios[2])
            var tiempo_total = 0
            var distancia_total = 0				

            //1
            var points1 = {
                origin: { lat: parseFloat(send_negocios[0].latitud), lng: parseFloat(send_negocios[0].longitud) },
                destination: { lat: parseFloat(send_negocios[1].latitud), lng: parseFloat(send_negocios[1].longitud) },
                travelMode: google.maps.DirectionsTravelMode.DRIVING
            };
            
            directionsService1.route(points1, async function(response, status) {
                if (status == google.maps.DirectionsStatus.OK) {
                    directionsDisplay1.setDirections(response)
                    tiempo_total += response.routes[0].legs[0].duration.value
                    distancia_total += response.routes[0].legs[0].distance.value
                    // console.log(response.routes[0].legs[0].distance.value)
                }
            });	

            //2
            var points2 = {
                origin: { lat: parseFloat(send_negocios[1].latitud), lng: parseFloat(send_negocios[1].longitud) },
                destination: { lat: parseFloat(send_negocios[2].latitud), lng: parseFloat(send_negocios[2].longitud) },
                travelMode: google.maps.DirectionsTravelMode.DRIVING
            };
          
            directionsService2.route(points2, async function(response, status) {
                if (status == google.maps.DirectionsStatus.OK) {
                    directionsDisplay1.setDirections(response)
                    tiempo_total += response.routes[0].legs[0].duration.value
                    distancia_total += response.routes[0].legs[0].distance.value
                    // console.log(response.routes[0].legs[0].distance.value)
                }
            });	

            // 3			
            var points3 = {
                origin: { lat: parseFloat(send_negocios[2].latitud), lng: parseFloat(send_negocios[2].longitud) },
                destination: { lat: parseFloat(milocation.latitud), lng: parseFloat(milocation.longitud) },
                travelMode: google.maps.DirectionsTravelMode.DRIVING
            };	
            
            directionsService3.route(points3, async function(response, status) {
                if (status == google.maps.DirectionsStatus.OK) {
                    directionsDisplay3.setDirections(response)
                    tiempo_total += response.routes[0].legs[0].duration.value
                    distancia_total += response.routes[0].legs[0].distance.value
                    // console.log(response.routes[0].legs[0].distance.value)
                    // $("#mitiempo").html(formatMoney(tiempo_total/60, ".", ","))
                    // $("#midistancia").html(formatMoney(distancia_total/1000, ".", ","))
                    calc_envio = await calcular_costo(distancia_total)
                }
            });	
    
            break;
        default:
            break;
    }
    return Math.round(calc_envio);
}

const calcular_costo = async (mivalue) => {
    var km = mivalue / 1000
    if (km < 3) {
        return 4
    } else if(km < 5) {
        return 6
    }else if(km < 7){
        return 8
    }else{
        return 12
    }
}
client.initialize();
