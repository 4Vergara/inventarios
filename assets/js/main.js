'use strict';
var valtoken = '';
var varToken = 'token_app';
var appversion = 'v1.0.78';
var TABLA_CONFIGURACION = {
	responsive: {
		details: false
	},
	language: {
		url: IP_SERVER + 'assets/datatables/es-ES.json'
	},
};
$(function () {
	window.alert = function (message) {
		mensaje('Alerta', message);
	};
	checkES6Support();
	$(document).ajaxStart(function () {
		loading(true);
	});
	$(document).ajaxComplete(function (a, b) {
		if (b.status !== 200) {
			mensaje(b.statusText, b.status);
		} else if (b.responseJSON) {
			var { error, mensaje: msg, toas: toastMsg, salir } = b.responseJSON;
			if (error) {
				mensaje('Mensaje...', parsestring(error), 'error');
			}
			if (msg) {
				mensaje('', parsestring(msg), 'info');
			}
			if (toastMsg) {
				toas(parsestring(toastMsg), 'info');
			}
			if (salir) {
				setTimeout(salirapp, 3500);
			}
		}
		loading(false);
	});
	loading(false);
	var menu = $('#side-main-menu li a');
	menu.map(function (k, v) {
		if (v.href == location.href) {
			v.parentNode.classList.add('active');
			var el = $(v).closest('ul').closest('li').addClass('active');
			$('a', el).click();
		}
	});
	$('#versionapp').text(appversion);
});
function formToObjet(form) {
	let obj = {};
	Array.from(form.elements).forEach(element => {
		if (element.name) {
			if (element.type === 'radio' || element.type === 'checkbox') {
				if (element.checked) {
					if (element.name.endsWith('[]')) {
						let key = element.name.slice(0, -2);
						if (!obj[key]) {
							obj[key] = [];
						}
						obj[key].push(element.value);
					} else {
						obj[element.name] = element.value;
					}
				}
			} else {
				if (element.name.endsWith('[]')) {
					let key = element.name.slice(0, -2);
					if (!obj[key]) {
						obj[key] = [];
					}
					obj[key].push(element.value);
				} else {
					obj[element.name] = element.value;
				}
			}
		}
	});
	return obj;
}
function parsestring(msj, separador) {
	if (typeof msj === 'string' || msj instanceof String) {
		return msj;
	} else if (msj instanceof Array || msj.constructor === Array) {
		return msj.join(separador || '<hr>');
	} else if (msj instanceof Object || msj.constructor === Object) {
		return Object.entries(msj).map(function (entry) { return entry.join(':'); }).join(separador || '<hr>');
	}
}
function loading(estado) {
	if (estado) {
		$('#divpreload').show();
	} else $('#divpreload').hide(300);
}
function salirapp() {
	localStorage.removeItem(varToken);
	localStorage.removeItem('versionmanual');
	$.post(IP_SERVER + 'login/salir', { token: valtoken }, function () {
		window.location.replace(IP_SERVER + 'login');
	});
}
function error(er, code) {
	mensaje('Error: ' + er.status, 'Error al cargar la pagina ' + code, 'error');
}
function mensaje(titulo, texto, tipo) {
	loading(false);
	var param = Swal.mixin({
		customClass: {
			confirmButton: "btn"
		},
		confirmButtonColor: "#3085d6",
	});
	param.fire({
		icon: tipo || 'warning',
		title: titulo || '',
		html: texto || '',
	})
	// icon = warning, success, question, info, error
}
function toas(mensaje, icono, posicion) {
	var Toast = Swal.mixin({
		toast: true,
		position: posicion || "top-end",
		showConfirmButton: false,
		timer: 3000,
		timerProgressBar: true,
		didOpen: (toast) => {
			toast.onmouseenter = Swal.stopTimer;
			toast.onmouseleave = Swal.resumeTimer;
		}
	});
	Toast.fire({
		icon: icono || 'success',
		title: parsestring(mensaje)
	});
}
function confirmar(t, form, tipo) {
	Swal.fire({
		title: t || 'Confirmar',
		showCancelButton: true,
		showCancelButton: true,
		icon: tipo || "warning",
		confirmButtonColor: "#d33",
		confirmButtonText: "Si",
		cancelButtonText: 'Cancelar'
	}).then((result) => {
		if (result.isConfirmed) {
			form.submit()
		}
	});
}
function closeWindow() {
	window.open('', '_parent', '');
	window.close();
}
function encode64(value, encodeTimes, isJson) {
	encodeTimes = typeof encodeTimes !== 'undefined' ? encodeTimes : 1;
	isJson = typeof isJson !== 'undefined' ? isJson : true;
	var encode = isJson ? JSON.stringify(value) : value;
	encode = Utf8Encode(encode);
	for (var i = 0; i < encodeTimes; i++) {
		encode = btoa(encode);
	}
	return encode.replace(/=/g, '').replace(/\+/g, '-').replace(/\//g, '_');
}
function decode64(value, decodeTimes, isJson) {
	decodeTimes = typeof decodeTimes !== 'undefined' ? decodeTimes : 1;
	isJson = typeof isJson !== 'undefined' ? isJson : true;
	value = value.replace(/-/g, '+').replace(/_/g, '/');
	while (value.length % 4) {
		value += '=';
	}
	for (var i = 0; i < decodeTimes; i++) {
		value = atob(value);
	}
	value = Utf8Decode(value);
	if (isJson) {
		value = JSON.parse(value);
	}
	return value;
}
function JSONparse(t) {
	try {
		return JSON.parse(t);
	} catch (e) {
		return window.history.back(), {};
	}
}
function eventoeliminar(reques, options) {
	var {
		title = '¿Está seguro?',
		text = '¡El registro se eliminará para siempre!',
		icon = 'warning',
		sicolor = '#d24d4d',
		nocolor = '#8e8e8e',
		sibtn = 'Sí, eliminar!',
		nobtn = 'Cancelar'
	} = options || {};
	Swal.fire({
		title: title,
		html: text,
		icon: icon,
		showCancelButton: true,
		confirmButtonColor: sicolor,
		cancelButtonColor: nocolor,
		confirmButtonText: sibtn,
		cancelButtonText: nobtn,
		reverseButtons: true
	}).then((result) => {
		reques(result.isConfirmed);
	});
}
function eventomensaje(reques, options) {
	var {
		title = '¿Está seguro?',
		text = '¡El registro se eliminará para siempre!',
		icon = 'warning',
		sicolor = '#3085d6',
		nocolor = '#d33',
		sibtn = 'Sí, eliminar!',
		nobtn = 'Cancelar',
		inputLabel = '¿Por qué?',
		inputPlaceholder = 'Escriba aquí...'
	} = options || {};
	Swal.fire({
		title: title,
		html: text,
		input: "text",
		inputLabel: inputLabel,
		inputPlaceholder: inputPlaceholder,
		icon: icon,
		showCancelButton: true,
		confirmButtonColor: sicolor,
		cancelButtonColor: nocolor,
		confirmButtonText: sibtn,
		cancelButtonText: nobtn,
		reverseButtons: true
	}).then(reques);
}
function lafecha(timestamp) {
	var fecha = new Date(timestamp);
	var anio = fecha.getFullYear();
	var mes = String(fecha.getMonth() + 1).padStart(2, '0');
	var dia = String(fecha.getDate()).padStart(2, '0');
	var horas = fecha.getHours();
	var amPm = fecha.getHours() >= 12 ? 'PM' : 'AM';
	var hora = String(horas % 12 || 12).padStart(2, '0');
	var minutos = String(fecha.getMinutes()).padStart(2, '0');
	var segundos = String(fecha.getSeconds()).padStart(2, '0');
	var amd = `${anio}-${mes}-${dia}`;
	var hms = `${hora}:${minutos}:${segundos} ${amPm}`;
	return {
		fecha: amd,
		hora: hms,
		fechahora: amd + ' ' + hms,
		fh: amd + '<br>' + hms
	};
	// return `${anio}-${mes}-${dia} ${hora}:${minutos}:${segundos} ${amPm}`;
}
function getBase64(file, callback) {
	if (window.FileReader) {
		var reader = new FileReader();
		reader.onload = function () {
			callback(reader.result);
		};
		reader.onerror = function (error) {
			console.log('Error: ', error);
		};
		reader.readAsDataURL(file);
	} else {
		console.log('FileReader is not supported in this browser.');
	}
}
function Utf8Encode(strUni) {
	return String(strUni).replace(
		/[\u0080-\u07ff]/g,  // U+0080 - U+07FF => 2 bytes 110yyyyy, 10zzzzzz
		function (c) {
			var cc = c.charCodeAt(0);
			return String.fromCharCode(0xc0 | cc >> 6, 0x80 | cc & 0x3f);
		}
	).replace(
		/[\ud800-\udbff][\udc00-\udfff]/g,  // surrogate pair
		function (c) {
			var high = c.charCodeAt(0);
			var low = c.charCodeAt(1);
			var cc = ((high & 0x03ff) << 10 | (low & 0x03ff)) + 0x10000;
			// U+10000 - U+10FFFF => 4 bytes 11110www 10xxxxxx, 10yyyyyy, 10zzzzzz
			return String.fromCharCode(0xf0 | cc >> 18, 0x80 | cc >> 12 & 0x3f, 0x80 | cc >> 6 & 0x3f, 0x80 | cc & 0x3f);
		}
	).replace(
		/[\u0800-\uffff]/g,  // U+0800 - U+FFFF => 3 bytes 1110xxxx, 10yyyyyy, 10zzzzzz
		function (c) {
			var cc = c.charCodeAt(0);
			return String.fromCharCode(0xe0 | cc >> 12, 0x80 | cc >> 6 & 0x3f, 0x80 | cc & 0x3f);
		}
	);
}
function Utf8Decode(strUtf) {
	// note: decode 2-byte chars last as decoded 2-byte strings could appear to be 3-byte or 4-byte char!
	return String(strUtf).replace(
		/[\u00f0-\u00f7][\u0080-\u00bf][\u0080-\u00bf][\u0080-\u00bf]/g,  // 4-byte chars
		function (c) {  // (note parentheses for precedence)
			var cc = ((c.charCodeAt(0) & 0x07) << 18) | ((c.charCodeAt(1) & 0x3f) << 12) | ((c.charCodeAt(2) & 0x3f) << 6) | ( c.charCodeAt(3) & 0x3f);
			var tmp = cc - 0x10000;
			// TODO: throw error(invalid utf8) if tmp > 0xfffff
			return String.fromCharCode(0xd800 + (tmp >> 10), 0xdc00 + (tmp & 0x3ff)); // surrogate pair
		}
	).replace(
		/[\u00e0-\u00ef][\u0080-\u00bf][\u0080-\u00bf]/g,  // 3-byte chars
		function (c) {  // (note parentheses for precedence)
			var cc = ((c.charCodeAt(0) & 0x0f) << 12) | ((c.charCodeAt(1) & 0x3f) << 6) | ( c.charCodeAt(2) & 0x3f);
			return String.fromCharCode(cc);
		}
	).replace(
		/[\u00c0-\u00df][\u0080-\u00bf]/g,                 // 2-byte chars
		function (c) {  // (note parentheses for precedence)
			var cc = (c.charCodeAt(0) & 0x1f) << 6 | c.charCodeAt(1) & 0x3f;
			return String.fromCharCode(cc);
		}
	);
}
function checkES6Support() {
	try {
		// Verificar algunas características de ES6
		if (typeof Symbol === "undefined" ||
			typeof Promise === "undefined" ||
			typeof Map === "undefined" ||
			!Array.prototype.includes ||
			!String.prototype.startsWith) {
			throw new Error('Su navegador es muy antiguo. Por favor, actualice su navegador para una mejor experiencia.');
		}
	} catch (e) {
		console.log(e);
		alert(e.message);
	}
}