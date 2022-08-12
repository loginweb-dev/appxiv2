$(function() {
	// var INDEX = 0;
	$("#chat-submit").click( async function(e) {
		e.preventDefault();

		var msg = $("#chat-input").val();
		if (msg == '') {
			pb.error(
				'Ingres un texto valido'
			);
		  $(".chat-logs").stop().animate({ scrollTop: $(".chat-logs")[0].scrollHeight}, 1000);
		  return true;
		}
		var str="";
		str += "<div id='cm-msg-0' class=\"chat-msg self\">";
		str += "          <span class=\"msg-avatar\">";
		str += "            <img src=\"https://cmt.gob.bo//storage/landingpage/chat.png\">";
		str += "          <\/span>";
		str += "          <div class=\"cm-msg-text\">";
		str += msg;
		str += "          <\/div>";
		str += "        <\/div>";
		$(".chat-logs").append(str);
		var misession = localStorage.getItem('michat') ? JSON.parse(localStorage.getItem('michat')) : []
		if (msg.match(/hola/) || msg.match(/Hola/) || msg.match(/Buenas/)){
			var str="";
			str += "<div class=\"chat-msg user\">";
			str += "          <span class=\"msg-avatar\">";
			str += "            <img src=\"https://cmt.gob.bo//storage/users/default.png\">";
			str += "          <\/span>";
			str += "          <div class=\"cm-msg-text\">";
			str += "Hola, "+misession.name+" soy el 🤖CHATBOT🤖 de: GoDelivery tu asistente de ventas, visitas los negocios, llena tu carrito y reliza tu pedido.<br>"
			str += "----------------------------------<br>"
			str += "<strong>A .-</strong> TODOS LOS NEGOCIOS<br>"
			str += "B .- TODOS LOS PRODUCTOS<br>"
			str += "C .- BUSCAR UN PRODUCTO<br>"
			str += "----------------------------------<br>"
			str += "ENVIA UNA OPCION ejemplo: a o b"
			str += "     		<\/div>";
			str += "        <\/div>";
			$(".chat-logs").append(str);
		}else if (msg.match(/a/) || msg.match(/A/)){
			
		}
		$("#chat-input").val('');
		$(".chat-logs").stop().animate({ scrollTop: $(".chat-logs")[0].scrollHeight}, 1000);
	})

	$("#chat-circle").click(async function() {
	  pb.info(
		'Iniciando Chats'
	  );
	  var misession = JSON.parse(localStorage.getItem('misession'))
	  var str="";
	  str += "<div class=\"chat-msg user\">";
	  str += "          <span class=\"msg-avatar\">";
	  str += "            <img src=\"https://cmt.gob.bo//storage/users/default.png\">";
	  str += "          <\/span>";
	  str += "          <div class=\"cm-msg-text\">";
	  str += "Hola, "+misession.name+" soy el 🤖CHATBOT🤖 de: GoDelivery tu asistente de ventas, visitas los negocios, llena tu carrito y reliza tu pedido.<br>"
	  str += "----------------------------------<br>"
	  str += "<strong>A .-</strong> TODOS LOS NEGOCIOS<br>"
	  str += "B .- TODOS LOS PRODUCTOS<br>"
	  str += "C .- BUSCAR UN PRODUCTO<br>"
	  str += "----------------------------------<br>"
	  str += "ENVIA UNA OPCION ejemplo: a o b .."
	  str += "     		<\/div>";
	  str += "        <\/div>";
	  $(".chat-logs").append(str);
	  $("#chat-circle").toggle('scale');
	  $(".chat-box").toggle('scale');
	  $(".chat-logs").stop().animate({ scrollTop: $(".chat-logs")[0].scrollHeight}, 1000);
	})

	$(".chat-box-toggle").click(function() {
	  $("#chat-circle").toggle('scale')
	  $(".chat-box").toggle('scale')
	})
  })