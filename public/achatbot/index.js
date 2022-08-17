const express = require('express');
const axios = require('axios');
const qrcode = require("qrcode-terminal");
var qr = require('qr-image');
var path = require('path');
const cors = require('cors')
const { Client, MessageMedia, LocalAuth, Location, Buttons} = require("whatsapp-web.js");

const JSONdb = require('simple-json-db');
const status = new JSONdb('json/status.json');
const users = new JSONdb('json/users.json');
const pedidos = new JSONdb('json/pedidos.json');
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

app.listen(process.env.ACHATBOT_PORT, () => {
    console.log('CHATBOT ESTA LISTO EN EL PUERTO: '+process.env.ACHATBOT_PORT);
});

var micount = 0
client.on("qr", (qrwb) => {
    var qr_svg = qr.image(qrwb, { type: 'png' });
    qr_svg.pipe(require('fs').createWriteStream('public/qrwb.png'));
    qrcode.generate(qrwb, {small: true}, function (qrcode) {
        console.log(qrcode)
        console.log('Nuevo QR, recuerde que se genera cada 1 minuto, INTENTO #'+micount++)        
    })
});

client.on('ready', async () => {
	console.log('CHATBOT ESTA LISTO EN EL PUERTO: '+process.env.ACHATBOT_PORT);
});

client.on("authenticated", () => {
});

client.on("auth_failure", msg => {
    console.error('AUTHENTICATION FAILURE', msg);
})

client.on('message', async msg => {
    console.log('MESSAGE RECEIVED', msg);    
    if (users.has(msg.from)) {
        if (msg.body === 'reset') {
            status.set(msg.from, 0)
        }
        if (msg.body==='test') {
            // var pedido = pedidos.get(msg.from)
            var mipedido = await axios(process.env.APP_URL+'api/pedido/'+5)
            console.log(mipedido.data.ubicacion)
            // var mapa= new Location(-14.8000014, -64.8703225, "Al frente de X")
            // console.log(mapa)
            // client.sendMessage(msg.from, "Ubicacion")
            // client.sendMessage(msg.from, mapa)
            
            var ubicacion=''
            ubicacion +='Ubicación del Cliente: '+mipedido.data.cliente.nombre+'\n'
            ubicacion+= 'http://maps.google.com/maps?&z=12&mrt=yp&t=k&q='+mipedido.data.ubicacion.latitud+'+'+mipedido.data.ubicacion.longitud+'\n'
            ubicacion+='Descripción: '+mipedido.data.ubicacion.detalles
            client.sendMessage(msg.from, ubicacion)

            var send_negocios= await negocios_pedido(5)
            console.log(send_negocios)
            for (let index = 0; index < send_negocios.length; index++) {
                ubicacion=''
                ubicacion+=' Ubicación del Negocio: '+send_negocios[index].nombre+'\n'
                ubicacion+= 'http://maps.google.com/maps?&z=12&mrt=yp&t=k&q='+send_negocios[index].latitud+'+'+send_negocios[index].longitud
                client.sendMessage(msg.from, ubicacion)
            }
           
        }
        var miuser = users.get(msg.from)
        if (miuser.user.role_id === 4) {
            switch (status.get(msg.from)) {
                case 0:
                    if (msg.body.toUpperCase() === 'B'){
                        await axios(process.env.APP_URL+'api/mensajero/update/'+msg.from)
                        menu_mensajero(msg.from) 
                    }else if (msg.body.toUpperCase() === 'A'){
                        var encola = await axios(process.env.APP_URL+'api/pedidos/get/encola')
                        if (encola.data.length>0) {
                            var list = '*Pedidos en cola*\n'
                            list += '----------------------------------\n'
                            for (let index = 0; index < encola.data.length; index++) {
                                list += '*'+encola.data[index].id+'* .- '+encola.data[index].cliente.nombre+' ('+encola.data[index].published+')\n' 
                            }
                            list += '----------------------------------\n'
                            list +='*A.-* Elige un pedido para tomarlo. \n'
                            list +='*B.-* Regresar al Menú Principal\n'
                            list += '----------------------------------\n'
                            list += 'Envía una opción (ejemplo: *A*)'
                            client.sendMessage(msg.from, list)
                            status.set(msg.from, 0.1)
                        }
                        else{
                            client.sendMessage(msg.from, 'No hay Pedidos en cola por el momento.')
                        }                 
                    }else if (msg.body.toUpperCase() === 'C'){
                        var newpassword=Math.random().toString().substring(2, 8)
                        var phone= msg.from
                        var midata={
                          phone:phone,
                          password:newpassword
                        }
                        var usuario= await axios.post(process.env.APP_URL+'api/reset/pw/mensajero', midata)
                        var list=''
                        list+='Credenciales para Ingresar al Sistema:\n'
                        list+='Correo: '+usuario.data.email+' \n'
                        list+='Contraseña: '+newpassword+' \n'
                        list+='No comparta sus credenciales con nadie'
                        client.sendMessage(msg.from, list)

                    }else{
                        menu_mensajero(msg.from)
                    }
                    break;
                case 0.1:
                    if (msg.body.toUpperCase() === 'A'){
                        list=''
                        list+='Envía el Código del Pedido que quieres tomar\n'
                        list += '----------------------------------\n'
                        list+='Ejemplo: *1*\n'
                        list += '----------------------------------\n'
                        list += '*B.-* Menú Principal'
                        client.sendMessage(msg.from, list)
                        status.set(msg.from, 0.2)
                    }
                    else if(msg.body.toUpperCase() === 'B'){
                        status.set(msg.from, 0)
                        menu_mensajero(msg.from)
                    }
                    else{
                        client.sendMessage(msg.from, 'Envía una Opción Válida')
                    }

                    break;
                case 0.2:
                    if (Number.isInteger(parseInt(msg.body))) {
                        var mipedido_id = parseInt(msg.body)
                        //console.log(mipedido.data)
                        var asignar = await axios.post(process.env.APP_URL+'api/asignar/pedido', {
                            telefono: msg.from,
                            pedido_id: mipedido_id
                        })
                        var mipedido = await axios(process.env.APP_URL+'api/pedido/'+mipedido_id)

                        if (asignar.data) {
                            var pedido= await axios(process.env.APP_URL+'api/pedido/'+mipedido_id)
                            //notificacion a los negocios y concatenar ubicaciones del mensajero ---------------
                            var send_negocios= await negocios_pedido(mipedido_id)
                            var ubicacion=''
                            for (let index = 0; index < send_negocios.length; index++) {
                                ubicacion+='Ubicacion del Negocio: '+send_negocios[index].nombre+'\n'
                                ubicacion+= 'http://maps.google.com/maps?&z=12&mrt=yp&t=k&q='+send_negocios[index].latitud+'+'+send_negocios[index].longitud+'\n'
                                client.sendMessage(send_negocios[index].chatbot_id, 'El delivery: *'+pedido.data.mensajero.nombre+'* será el encargado de recoger el pedido *#'+mipedido_id+'*')                           
                            } 

                            var mitext='🎉Felicidades se te fue asignado el PEDIDO #'+mipedido_id+'🎉\n'
                            mitext+= '------------------------------------------\n'
                            mitext+= 'Porfavor, procede a ir lo antes posible a recoger el pedido a los negocios respectivos, envía tu *UBICACIÓN EN TIEMPO REAL* al cliente para iniciar el viaje porfavor.\n'
                            mitext+= '------------------------------------------\n'
                            mitext+=ubicacion
                            mitext+= '------------------------------------------\n'
                            mitext+= '*A* .- Ya recogi todos los productos\n'
                            mitext+= '*B* .- Cancelo el pedido\n'
                            mitext+= '------------------------------------------\n'
                            mitext+= 'Envia una opción (ejemplo: *A*)'                              
                            client.sendMessage(msg.from, mitext)
                            var contacto_cliente= await client.getContactById(pedido.data.cliente.chatbot_id)
                            client.sendMessage(msg.from, contacto_cliente);
                            // message for client
                            await axios.post(process.env.CHATBOT_URL+'message', {
                                phone: pedido.data.chatbot_id,
                                message: 'Tu pedido fue asignado al Delivery: *'+pedido.data.mensajero.nombre+'*, se te notificará cuando el delivery recoja tu pedido y esté de ida entregar.'
                            })                               
                                 
                            status.set(msg.from, 1)
                            pedidos.set(msg.from, mipedido.data)                                   
                        } else {
                            client.sendMessage(msg.from, 'El pedido *#'+mipedido_id+'* ya está asignado a otro Delivery, o no está disponible, intenta con otro pedido.')
                        }
                    }
                    else if(msg.body.toUpperCase() === 'B'){
                        status.set(msg.from, 0)
                        menu_mensajero(msg.from)
                    }
                    else{
                        // var encola = await axios(process.env.APP_URL+'api/pedidos/get/encola')
                        // var list = '*Pedidos en cola, elige uno para iniciar el proceso*\n'
                        // list += '----------------------------------\n'
                        // for (let index = 0; index < encola.data.length; index++) {
                        //     list += '*'+encola.data[index].id+'* .- '+encola.data[index].cliente.nombre+' ('+encola.data[index].published+')\n' 
                        // }
                        // list += '----------------------------------\n'
                        // list +='Envia el código del pedido'
                        // client.sendMessage(msg.from, list)
                        client.sendMessage(msg.from, 'Envía una Opción Válida')
                    }
                    break;
                case 1: //para recoger
                    if (msg.body.toUpperCase() === 'A') {
                        var pedido = pedidos.get(msg.from)

                        //Ubicacion del Cliente al Mensajero
                        var ubicacion=''
                        ubicacion +='Ubicación del Cliente: '+pedido.cliente.nombre+'\n'
                        ubicacion+= 'http://maps.google.com/maps?&z=12&mrt=yp&t=k&q='+pedido.ubicacion.latitud+'+'+pedido.ubicacion.longitud+'\n'
                        ubicacion+='Descripcion: '+pedido.ubicacion.detalles+'\n'
                        //Mensaje Completo
                        var mitext = 'Genial, ya recogiste el pedido completo, ahora llévalo hasta el cliente, no olvides enviar tu *UBICACIÓN EN TIEMPO REAL*\n'
                        mitext+= '------------------------------------------\n'
                        mitext+=ubicacion    
                        mitext+= '------------------------------------------\n'
                        mitext+= '*A* .- Ya entregué el pedido\n'
                        mitext+= '------------------------------------------\n'
                        mitext+= 'Envía una opción (ejemplo: *A*)'
                        status.set(msg.from, 2)
                        client.sendMessage(msg.from, mitext)
                        // var locaton= new Location(pedido.ubicacion.latitud, pedido.ubicacion.longitud, pedido.ubicacion.detalles)
                        // console.log(locaton)
                        // client.sendMessage(msg.from, locaton)

                    
                        // mitext=''
                        mitext = 'Tu pedido *#'+pedido.id+'* ya fue entregado al delivery asignado y está siendo llevado, porfavor mantente atento.'
                        // client.sendMessage(pedido.chatbot_id, mitext)
                        //CLIENTE
                        await axios.post(process.env.CHATBOT_URL+'message', {
                            phone: pedido.chatbot_id,
                            message: mitext
                        })  
                    }else if(msg.body === 'B' || msg.body === 'b') {
                        client.sendMessage(msg.from, 'Envia el motivo de tu cancelación.')
                        status.set(msg.from, 1.1)


                    }else{
                        client.sendMessage(msg.from, 'Envia una opción valida') 
                    }
                    break;
                case 1.1://para cancelar el pedido de parte del mensajero
                    var mensaje_cancelacion=msg.body
                    var pedido = pedidos.get(msg.from)
                    var mensajero_cancelacion=pedido.mensajero
                    var midata = {
                        telefono: msg.from,
                    }
                    var pedido_cancelado= await axios.post(process.env.APP_URL+'api/cancelar/pedido', midata)
                    if (pedido_cancelado.data) {
                        // var chofer= await axios(process.env.APP_URL+'api/search/mensajero/'+pedido.mensajero_id)
                        client.sendMessage(msg.from, 'Pedido #'+pedido.id+' cancelado esperamos que resuelva lo mas pronto posible sus inconvenientes.')
                        //Mensaje al Cliente de que su pedido fue cancelado
                        var mitext=''
                        mitext+= 'Su pedido #'+pedido.id+' ha sido cancelado por el chofer '+mensajero_cancelacion.nombre+'\n'
                        mitext+= 'El motivo fue el siguiente: *'+mensaje_cancelacion+'* \n'
                        mitext+= 'Estamos buscando otro chofer para llevar su pedido, lamentamos los inconvenientes.'
                        // client.sendMessage(pedido.cliente.chatbot_id, mitext)
                        await axios.post(process.env.CHATBOT_URL+'message', {
                            phone: pedido.chatbot_id,
                            message: mitext
                        })
                        //notificacion a los negocios de la cancelacion ---------------
                        var send_negocios= await negocios_pedido(pedido.id)
                        var mitext=''
                        mitext+= 'El pedido #'+pedido.id+' ha sido cancelado por el chofer '+pedido.mensajero.nombre+'\n'
                        mitext+= 'El motivo fue el siguiente: '+mensaje_cancelacion+'\n'
                        mitext+= 'Estamos buscando otro chofer para llevar el pedido, lamentamos los inconvenientes.'
                        mitext+= 'Porfavor esté atento a quién será el próximo Delivery asignado para entregar el pedido correspondiente.'
                    
                        for (let index = 0; index < send_negocios.length; index++) {              
                            client.sendMessage(send_negocios[index].chatbot_id, mitext)                           
                        }
                        status.set(msg.from, 0)    
                    }
                    else{
                        client.sendMessage(msg.from, 'Usted no tiene un pedido asignado para cancelarlo')
                    }

                    break;
                case 2: //para q entregar
                    if (msg.body.toUpperCase() === 'A') {
                        var pedido = pedidos.get(msg.from)
                        var mitext=''
                        mitext += 'El pedido *#'+pedido.id+'* fue entregado al cliente *'+pedido.cliente.nombre+'* correctamente, espera que el cliente confirme el mismo.'
                        client.sendMessage(msg.from, mitext)
                        mitext=''
                        mitext += 'El delivery confirmo que tu pedido ya fue entregado\n'
                        mitext = 'Ya llego tu pedido *#'+pedido.id+'* ?\n'
                        mitext += '*A* .- Si llegó\n'
                        mitext += '*B* .- No llegó\n'
                        mitext += '----------------------------------\n'
                        mitext += 'Envia una opción (ejemplo: *A*)'         
                        //client.sendMessage(pedido.chatbot_id, mitext)
                        //CLIENTE
                        await axios.post(process.env.CHATBOT_URL+'cart', {
                            phone: pedido.chatbot_id,
                            message: mitext,
                            status:2
                        })  
                         status.set(msg.from, 2.1)
                    } else {
                        client.sendMessage(msg.from, 'Envia una opción valida') 
                    }
                    break;
                    case 2.1://Mensajero a la Espera de La Confirmacion del Pedido
                        client.sendMessage(msg.from, 'Espere que el cliente confirme la llegada del Pedido.')
                        break;       
                    case 2.2://Pedido Faltante Segun Cliente

                        client.sendMessage(msg.from, 'Estás en estado de espera porque el Cliente no confirmó la llegada de su pedido.')

                        break
                    

                default:
                    //client.sendMessage(msg.from, 'Interactuando como '+micliente.data.modo+'\nEstate atento al proximo pedido.')
                    break;
            }
        }else if(miuser.user.role_id == 5) {
            switch (status.get(msg.from)) {
                case 0:

                    break;        
                default:
                    client.sendMessage(msg.from, 'Envia una opción valida')
                    break;
            }
        }else{
        }
    } else {
        var miuser = await axios(process.env.APP_URL+'api/user/get/phone/'+msg.from)
        if (miuser.data) {
            users.set(msg.from, miuser.data)
            status.set(msg.from, 0)
            // client.sendMessage(msg.from, 'Bienvenido a appxi.net')
            menu_mensajero(msg.from)
        } else {
            client.sendMessage(msg.from, 'No tiene registro.')
        }
    }
})

app.get('/', async (req, res) => {
    res.render('index', {count: micount});
});

app.post('/message', (req, res) => {
    var message = req.body ? req.body.message : req.query.message
    var phone = req.body ? req.body.phone : req.query.phone
    client.sendMessage(phone, message)   
    res.send('Mensaje Enviado') 
});

app.post('/chat', async (req, res) => {
    var message = req.body.message ? req.body.message : req.query.message
    var phone = req.body.phone ? req.body.phone : req.query.phone
    // status.set(phone, 1.1)
    var miclientelp = await axios(process.env.APP_URL+'api/cliente/'+phone)
    await cart_list(phone, miclientelp)
    client.sendMessage(phone, message)    
});

app.post('/login', async (req, res) => {
    var message = req.body.message ? req.body.message : req.query.message
    var phone = req.body.phone ? req.body.phone : req.query.phone
    client.sendMessage(phone, message)
    res.send('Mensaje Enviado') 
    
});

app.post('/update', (req, res) => {
    var message = req.body.message!='undefined' ? req.body.message : req.query.message
    var phone = req.body.phone!='undefined' ? req.body.phone : req.query.phone
    var mistatus = req.body.status!='undefined' ? req.body.status : req.query.status
    //var micliente = await axios(process.env.APP_URL+'api/cliente/'+phone)
    // client.sendMessage(phone, message)
    console.log(req.body)
    console.log(req.body.status)
    console.log(req.query.status)
    console.log(phone)
    console.log(message)
    console.log(mistatus)       
    status.set(phone, mistatus)
    res.send('Mensaje Enviado') 
});

const menu_mensajero = async (phone) => {
    // var michofer =  users.get(phone)
    var  michofer  = await axios(process.env.APP_URL+'api/user/get/phone/'+phone)
    var miestado = michofer.data.estado ? 'Libre' : 'Ocupado'
    var list = '*Hola*, '+michofer.data.nombre+' (#'+michofer.data.id+') soy el 🤖CHATBOT🤖 de: *'+process.env.APP_NAME+'* tu asistente.\n'
    list += '----------------------------------\n'
    list += '*Estado :* '+miestado+'\n'
    list += '*Nombres :* '+michofer.data.nombre+'\n'
    list += '*Localidad :* '+michofer.data.localidad.nombre+'\n'
    list += '*Deliverys :* '+michofer.data.pedidos.length+'\n'
    list += '----------------------------------\n'
    list += '*A* .- Ver pedidos en cola\n'
    list += '*B* .- Cambiar de estado (envia el emoji - libre/ocupado)\n'
    list += '*C* .- Obtener Credenciales (panel)\n'
    list += '----------------------------------\n'
    list += 'Panel de administracion\n'
    list += process.env.APP_URL+'admin'
    client.sendMessage(phone, list)
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

const menu_negocio = async (phone) => {
    var minegocio = await axios(process.env.APP_URL+'api/minegocio/'+phone)
    var miestado = (minegocio.data.estado === '1') ? 'Abierto' : 'Cerrado'
    var list = '*Hola*, '+minegocio.data.contacto+' soy el 🤖CHATBOT🤖 de: *'+process.env.APP_NAME+'* tu asistente de ventas.\n'
    list += '----------------------------------'+' \n'
    list += '*ID :* '+minegocio.data.id+'\n'
    list += '*Mi Negocio :* '+minegocio.data.nombre+'\n'
    list += '*Localidad :* '+minegocio.data.poblacion.nombre+'\n'
    list += '*Direccion :* '+minegocio.data.direccion+'\n'
    list += '*Productos :* '+minegocio.data.productos.length+'\n'
    list += '*Contacto :* '+minegocio.data.contacto+'\n'
    list += '*Estado :* '+miestado+'\n'
    list += '----------------------------------\n'
    list += '🔄 .- Cambiar de Estado (envia el emoji - Abierto/Cerrado)\n'
    list += '⏪ .- VOLVER COMO CLIENTE\n'
    list += '----------------------------------\n'
    list += '*Mi Tienda en Linea*\n'
    list += process.env.APP_URL+'negocio/'+minegocio.data.slug
    client.sendMessage(phone, list)
    status_negocio.set(phone, 0)
    return true
}

client.initialize();
