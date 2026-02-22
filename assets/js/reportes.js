/**
 * Reportes y Estadísticas - Saho
 * Chart.js + AJAX para Dashboard, Ventas, Productos, Clientes, Financiero
 */
'use strict';

// ==========================================
// UTILIDADES
// ==========================================
var chartInstances = {};
var coloresPaleta = [
	'#e8630a', '#f59e0b', '#10b981', '#3b82f6', '#8b5cf6',
	'#ef4444', '#06b6d4', '#ec4899', '#84cc16', '#f97316'
];
var coloresPaletaAlpha = coloresPaleta.map(function(c) { return c + '33'; });

function formatMoney(n) {
	return '$' + parseFloat(n || 0).toLocaleString('es-MX', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
}

function formatNumber(n) {
	return parseInt(n || 0).toLocaleString('es-MX');
}

function ajaxGet(endpoint, params, callback) {
	$.ajax({
		url: IP_SERVER + 'reportes/' + endpoint,
		type: 'GET',
		data: params || {},
		dataType: 'json',
		success: function(resp) {
			if (resp.success && callback) callback(resp.data);
		},
		error: function(xhr) {
			console.error('Error en ' + endpoint, xhr.responseText);
		}
	});
}

function destroyChart(id) {
	if (chartInstances[id]) {
		chartInstances[id].destroy();
		delete chartInstances[id];
	}
}

function crearChart(id, config) {
	destroyChart(id);
	var ctx = document.getElementById(id);
	if (!ctx) return null;
	chartInstances[id] = new Chart(ctx.getContext('2d'), config);
	return chartInstances[id];
}

function calcPorcentaje(actual, anterior) {
	if (!anterior || anterior == 0) return actual > 0 ? 100 : 0;
	return ((actual - anterior) / anterior * 100).toFixed(1);
}

// ==========================================
// DASHBOARD PRINCIPAL (reportes/index)
// ==========================================
function cargarDashboard() {
	var desde = document.getElementById('kpiFechaDesde') ? document.getElementById('kpiFechaDesde').value : '';
	var hasta = document.getElementById('kpiFechaHasta') ? document.getElementById('kpiFechaHasta').value : '';
	var params = { fecha_desde: desde, fecha_hasta: hasta };

	// KPIs
	ajaxGet('kpis', params, function(data) {
		$('#kpiTotalVentas').text(formatNumber(data.total_ventas));
		$('#kpiMontoTotal').text(formatMoney(data.monto_total));
		$('#kpiPendienteCobro').text(formatMoney(data.total_por_cobrar));
		$('#kpiClientesActivos').text(formatNumber(data.clientes_activos));
		$('#kpiPromedioVenta').text(formatMoney(data.promedio_venta));
		$('#kpiFacturas').text(formatNumber(data.total_facturas));
		$('#kpiStockBajo').text(formatNumber(data.productos_stock_bajo));
		$('#kpiValorInventario').text(formatMoney(data.valor_inventario));
	});

	// Chart ventas por período
	cargarChartVentas();

	// Métodos de pago
	ajaxGet('ventasPorMetodoPago', params, function(data) {
		var labels = [], valores = [];
		data.forEach(function(r) {
			labels.push(r.metodo_pago ? r.metodo_pago.charAt(0).toUpperCase() + r.metodo_pago.slice(1) : 'N/A');
			valores.push(parseFloat(r.monto_total));
		});
		crearChart('chartMetodosPago', {
			type: 'doughnut',
			data: {
				labels: labels,
				datasets: [{ data: valores, backgroundColor: coloresPaleta.slice(0, labels.length), borderWidth: 2 }]
			},
			options: {
				responsive: true,
				maintainAspectRatio: false,
				plugins: {
					legend: { position: 'bottom', labels: { padding: 12, usePointStyle: true } }
				}
			}
		});
	});

	// Comparativo
	var mesActualDesde = desde || new Date().toISOString().slice(0, 7) + '-01';
	var mesActualHasta = hasta || new Date().toISOString().slice(0, 10);
	var d = new Date(mesActualDesde);
	d.setMonth(d.getMonth() - 1);
	var mesAnteriorDesde = d.toISOString().slice(0, 7) + '-01';
	var lastDay = new Date(d.getFullYear(), d.getMonth() + 1, 0);
	var mesAnteriorHasta = lastDay.toISOString().slice(0, 10);

	ajaxGet('comparativo', {
		fecha_desde_1: mesActualDesde, fecha_hasta_1: mesActualHasta,
		fecha_desde_2: mesAnteriorDesde, fecha_hasta_2: mesAnteriorHasta
	}, function(data) {
		var va = parseInt(data.actual.total_ventas || 0);
		var vp = parseInt(data.anterior.total_ventas || 0);
		var ma = parseFloat(data.actual.monto_total || 0);
		var mp = parseFloat(data.anterior.monto_total || 0);
		var pctV = calcPorcentaje(va, vp);
		var pctM = calcPorcentaje(ma, mp);

		$('#compVentasActual').text(formatNumber(va));
		$('#compVentasAnterior').text(formatNumber(vp));
		$('#compVentasBar').css('width', Math.min(Math.abs(pctV), 100) + '%');
		$('#compVentasBadge').text((pctV >= 0 ? '+' : '') + pctV + '%')
			.removeClass('bg-success bg-danger').addClass(pctV >= 0 ? 'bg-success' : 'bg-danger');

		$('#compMontoActual').text(formatMoney(ma));
		$('#compMontoAnterior').text(formatMoney(mp));
		$('#compMontoBar').css('width', Math.min(Math.abs(pctM), 100) + '%');
		$('#compMontoBadge').text((pctM >= 0 ? '+' : '') + pctM + '%')
			.removeClass('bg-success bg-danger').addClass(pctM >= 0 ? 'bg-success' : 'bg-danger');
	});

	// Facturado vs No facturado
	ajaxGet('facturadoVsNoFacturado', params, function(data) {
		crearChart('chartFacturado', {
			type: 'doughnut',
			data: {
				labels: ['Facturado', 'No Facturado'],
				datasets: [{
					data: [parseFloat(data.facturado_monto || 0), parseFloat(data.no_facturado_monto || 0)],
					backgroundColor: ['#10b981', '#ef4444'],
					borderWidth: 2
				}]
			},
			options: {
				responsive: true,
				maintainAspectRatio: false,
				plugins: {
					legend: { position: 'bottom', labels: { padding: 12, usePointStyle: true } }
				}
			}
		});
	});

	// Vendedores
	ajaxGet('ventasPorVendedor', params, function(data) {
		var html = '';
		data.slice(0, 5).forEach(function(r, i) {
			html += '<div class="list-group-item d-flex justify-content-between align-items-center px-3 py-2">';
			html += '<div><span class="badge bg-light text-dark me-2">' + (i + 1) + '</span>' + (r.vendedor || 'N/A') + '</div>';
			html += '<div class="text-end"><span class="fw-bold">' + formatMoney(r.monto_total) + '</span><br><small class="text-muted">' + r.total_ventas + ' ventas</small></div>';
			html += '</div>';
		});
		$('#listaVendedores').html(html || '<div class="text-center py-4 text-muted">Sin datos</div>');
	});
}

function cargarChartVentas() {
	var desde = document.getElementById('kpiFechaDesde') ? document.getElementById('kpiFechaDesde').value : '';
	var hasta = document.getElementById('kpiFechaHasta') ? document.getElementById('kpiFechaHasta').value : '';
	var agrupacion = document.getElementById('chartAgrupacion') ? document.getElementById('chartAgrupacion').value : 'mes';

	ajaxGet('ventasPorPeriodo', { agrupacion: agrupacion, fecha_desde: desde, fecha_hasta: hasta }, function(data) {
		var labels = [], ventas = [], montos = [];
		data.forEach(function(r) {
			labels.push(r.periodo);
			ventas.push(parseInt(r.total_ventas));
			montos.push(parseFloat(r.monto_total));
		});
		crearChart('chartVentasPeriodo', {
			type: 'line',
			data: {
				labels: labels,
				datasets: [
					{
						label: 'Monto ($)',
						data: montos,
						borderColor: '#e8630a',
						backgroundColor: 'rgba(232,99,10,0.1)',
						fill: true,
						tension: 0.4,
						yAxisID: 'y'
					},
					{
						label: 'N° Ventas',
						data: ventas,
						borderColor: '#3b82f6',
						backgroundColor: 'rgba(59,130,246,0.1)',
						fill: false,
						tension: 0.4,
						yAxisID: 'y1'
					}
				]
			},
			options: {
				responsive: true,
				maintainAspectRatio: false,
				interaction: { mode: 'index', intersect: false },
				plugins: { legend: { position: 'top', labels: { usePointStyle: true } } },
				scales: {
					y: { type: 'linear', position: 'left', title: { display: true, text: 'Monto ($)' }, beginAtZero: true },
					y1: { type: 'linear', position: 'right', title: { display: true, text: 'Ventas' }, beginAtZero: true, grid: { drawOnChartArea: false } }
				}
			}
		});
	});
}

// ==========================================
// REPORTE DE VENTAS (reportes/ventas)
// ==========================================
function cargarReporteVentas() {
	var desde = $('#ventaFechaDesde').val();
	var hasta = $('#ventaFechaHasta').val();
	var agrup = $('#ventaAgrupacion').val();
	var params = { fecha_desde: desde, fecha_hasta: hasta };

	// Ventas por período + KPIs + tabla
	ajaxGet('ventasPorPeriodo', $.extend({ agrupacion: agrup }, params), function(data) {
		var labels = [], ventas = [], montos = [];
		var totalV = 0, totalM = 0, maxV = 0;
		data.forEach(function(r) {
			labels.push(r.periodo);
			ventas.push(parseInt(r.total_ventas));
			montos.push(parseFloat(r.monto_total));
			totalV += parseInt(r.total_ventas);
			totalM += parseFloat(r.monto_total);
			if (parseFloat(r.monto_total) > maxV) maxV = parseFloat(r.monto_total);
		});

		$('#rvTotalVentas').text(formatNumber(totalV));
		$('#rvMontoTotal').text(formatMoney(totalM));
		$('#rvPromedio').text(formatMoney(totalV > 0 ? totalM / totalV : 0));
		$('#rvVentaMayor').text(formatMoney(maxV));

		crearChart('chartVentasPeriodoDetalle', {
			type: 'bar',
			data: {
				labels: labels,
				datasets: [
					{
						label: 'Monto ($)',
						data: montos,
						backgroundColor: 'rgba(232,99,10,0.7)',
						borderColor: '#e8630a',
						borderWidth: 1,
						yAxisID: 'y'
					},
					{
						label: 'N° Ventas',
						data: ventas,
						type: 'line',
						borderColor: '#3b82f6',
						backgroundColor: 'transparent',
						tension: 0.4,
						yAxisID: 'y1'
					}
				]
			},
			options: {
				responsive: true,
				maintainAspectRatio: false,
				plugins: { legend: { position: 'top', labels: { usePointStyle: true } } },
				scales: {
					y: { beginAtZero: true, position: 'left', title: { display: true, text: 'Monto ($)' } },
					y1: { beginAtZero: true, position: 'right', title: { display: true, text: 'Ventas' }, grid: { drawOnChartArea: false } }
				}
			}
		});

		// Tabla
		var tbody = '';
		data.forEach(function(r) {
			tbody += '<tr>';
			tbody += '<td>' + r.periodo + '</td>';
			tbody += '<td class="text-center">' + formatNumber(r.total_ventas) + '</td>';
			tbody += '<td class="text-end">' + formatMoney(r.monto_total) + '</td>';
			tbody += '<td class="text-end">' + formatMoney(r.total_ventas > 0 ? r.monto_total / r.total_ventas : 0) + '</td>';
			tbody += '</tr>';
		});
		$('#tbodyVentasPeriodo').html(tbody || '<tr><td colspan="4" class="text-center text-muted">Sin datos</td></tr>');
	});

	// Métodos de pago
	ajaxGet('ventasPorMetodoPago', params, function(data) {
		var labels = [], valores = [];
		data.forEach(function(r) {
			labels.push(r.metodo_pago ? r.metodo_pago.charAt(0).toUpperCase() + r.metodo_pago.slice(1) : 'N/A');
			valores.push(parseFloat(r.monto_total));
		});
		crearChart('chartMetodosPagoDetalle', {
			type: 'pie',
			data: {
				labels: labels,
				datasets: [{ data: valores, backgroundColor: coloresPaleta.slice(0, labels.length), borderWidth: 2 }]
			},
			options: {
				responsive: true,
				maintainAspectRatio: false,
				plugins: { legend: { position: 'bottom', labels: { padding: 12, usePointStyle: true } } }
			}
		});
	});

	// Vendedores
	ajaxGet('ventasPorVendedor', params, function(data) {
		var labels = [], valores = [];
		data.forEach(function(r) {
			labels.push(r.vendedor || 'N/A');
			valores.push(parseFloat(r.monto_total));
		});
		crearChart('chartVendedoresDetalle', {
			type: 'bar',
			data: {
				labels: labels,
				datasets: [{
					label: 'Monto ($)',
					data: valores,
					backgroundColor: coloresPaleta.slice(0, labels.length).map(function(c) { return c + 'AA'; }),
					borderColor: coloresPaleta.slice(0, labels.length),
					borderWidth: 1
				}]
			},
			options: {
				responsive: true,
				maintainAspectRatio: false,
				indexAxis: 'y',
				plugins: { legend: { display: false } },
				scales: { x: { beginAtZero: true, title: { display: true, text: 'Monto ($)' } } }
			}
		});
	});

	// Facturado vs no facturado
	ajaxGet('facturadoVsNoFacturado', params, function(data) {
		crearChart('chartFacturadoDetalle', {
			type: 'doughnut',
			data: {
				labels: ['Facturado (' + (data.facturado_cantidad || 0) + ')', 'No Facturado (' + (data.no_facturado_cantidad || 0) + ')'],
				datasets: [{
					data: [parseFloat(data.facturado_monto || 0), parseFloat(data.no_facturado_monto || 0)],
					backgroundColor: ['#10b981', '#ef4444'],
					borderWidth: 2
				}]
			},
			options: {
				responsive: true,
				maintainAspectRatio: false,
				plugins: { legend: { position: 'bottom', labels: { padding: 12, usePointStyle: true } } }
			}
		});
	});
}

// ==========================================
// REPORTE DE PRODUCTOS (reportes/productos)
// ==========================================
function cargarReporteProductos() {
	var limite = $('#prodLimite').val() || 20;
	var diasVencer = $('#prodDiasVencer').val() || 30;
	var params = { limite: limite };

	// Más vendidos
	ajaxGet('productosMasVendidos', params, function(data) {
		var labels = [], valores = [], tbody = '';
		data.forEach(function(r, i) {
			labels.push(r.nombre.length > 20 ? r.nombre.substring(0, 20) + '...' : r.nombre);
			valores.push(parseInt(r.total_vendido));
			tbody += '<tr>';
			tbody += '<td>' + (i + 1) + '</td>';
			tbody += '<td>' + r.nombre + '</td>';
			tbody += '<td class="text-center">' + formatNumber(r.total_vendido) + '</td>';
			tbody += '<td class="text-end">' + formatMoney(r.total_ingresos) + '</td>';
			tbody += '</tr>';
		});
		crearChart('chartMasVendidos', {
			type: 'bar',
			data: {
				labels: labels,
				datasets: [{
					label: 'Unidades Vendidas',
					data: valores,
					backgroundColor: 'rgba(232,99,10,0.7)',
					borderColor: '#e8630a',
					borderWidth: 1
				}]
			},
			options: {
				responsive: true,
				maintainAspectRatio: false,
				indexAxis: 'y',
				plugins: { legend: { display: false } },
				scales: { x: { beginAtZero: true } }
			}
		});
		$('#tbodyMasVendidos').html(tbody || '<tr><td colspan="4" class="text-center text-muted">Sin datos</td></tr>');
	});

	// Menos vendidos
	ajaxGet('productosMenosVendidos', params, function(data) {
		var tbody = '';
		data.forEach(function(r, i) {
			tbody += '<tr>';
			tbody += '<td>' + (i + 1) + '</td>';
			tbody += '<td>' + (r.codigo || '-') + '</td>';
			tbody += '<td>' + r.nombre + '</td>';
			tbody += '<td class="text-center">' + formatNumber(r.total_vendido) + '</td>';
			tbody += '<td class="text-end">' + formatMoney(r.total_ingresos) + '</td>';
			tbody += '<td class="text-center">' + formatNumber(r.stock) + '</td>';
			tbody += '</tr>';
		});
		$('#tbodyMenosVendidos').html(tbody || '<tr><td colspan="6" class="text-center text-muted">Sin datos</td></tr>');
	});

	// Stock bajo
	ajaxGet('productosStockBajo', {}, function(data) {
		var tbody = '';
		$('#badgeStockBajo').text(data.length);
		data.forEach(function(r) {
			var deficit = parseInt(r.stock_minimo) - parseInt(r.stock);
			var costoRepo = deficit * parseFloat(r.precio_compra || 0);
			tbody += '<tr>';
			tbody += '<td>' + (r.codigo || '-') + '</td>';
			tbody += '<td>' + r.nombre + '</td>';
			tbody += '<td class="text-center"><span class="badge bg-danger">' + r.stock + '</span></td>';
			tbody += '<td class="text-center">' + r.stock_minimo + '</td>';
			tbody += '<td class="text-center"><span class="text-danger fw-bold">' + deficit + '</span></td>';
			tbody += '<td class="text-end">' + formatMoney(r.precio_compra) + '</td>';
			tbody += '<td class="text-end">' + formatMoney(costoRepo) + '</td>';
			tbody += '</tr>';
		});
		$('#tbodyStockBajo').html(tbody || '<tr><td colspan="7" class="text-center text-muted">No hay productos con stock bajo</td></tr>');
	});

	// Por vencer
	$('#spanDiasVencer').text(diasVencer);
	ajaxGet('productosPorVencer', { dias: diasVencer }, function(data) {
		var tbody = '';
		data.forEach(function(r) {
			var d = parseInt(r.dias_restantes);
			var badge = d <= 7 ? 'bg-danger' : (d <= 15 ? 'bg-warning text-dark' : 'bg-success');
			var estado = d <= 0 ? 'Vencido' : (d <= 7 ? 'Crítico' : (d <= 15 ? 'Próximo' : 'OK'));
			tbody += '<tr>';
			tbody += '<td>' + (r.codigo || '-') + '</td>';
			tbody += '<td>' + r.nombre + '</td>';
			tbody += '<td class="text-center">' + formatNumber(r.stock) + '</td>';
			tbody += '<td class="text-center">' + r.fecha_vencimiento + '</td>';
			tbody += '<td class="text-center"><span class="badge ' + badge + '">' + d + ' días</span></td>';
			tbody += '<td><span class="badge ' + badge + '">' + estado + '</span></td>';
			tbody += '</tr>';
		});
		$('#tbodyPorVencer').html(tbody || '<tr><td colspan="6" class="text-center text-muted">No hay productos próximos a vencer</td></tr>');
	});

	// Rotación
	ajaxGet('rotacionInventario', {}, function(data) {
		var labels = [], valores = [], tbody = '';
		data.slice(0, 15).forEach(function(r) {
			labels.push(r.nombre.length > 18 ? r.nombre.substring(0, 18) + '...' : r.nombre);
			valores.push(parseFloat(r.rotacion));
		});
		data.forEach(function(r) {
			tbody += '<tr>';
			tbody += '<td>' + r.nombre + '</td>';
			tbody += '<td class="text-center">' + formatNumber(r.total_vendido) + '</td>';
			tbody += '<td class="text-center">' + formatNumber(r.stock) + '</td>';
			tbody += '<td class="text-end">' + parseFloat(r.rotacion).toFixed(2) + '</td>';
			tbody += '</tr>';
		});
		crearChart('chartRotacion', {
			type: 'bar',
			data: {
				labels: labels,
				datasets: [{
					label: 'Índice de Rotación',
					data: valores,
					backgroundColor: valores.map(function(v) {
						return v > 3 ? 'rgba(16,185,129,0.7)' : (v > 1 ? 'rgba(245,158,11,0.7)' : 'rgba(239,68,68,0.7)');
					}),
					borderWidth: 1
				}]
			},
			options: {
				responsive: true,
				maintainAspectRatio: false,
				indexAxis: 'y',
				plugins: { legend: { display: false } },
				scales: { x: { beginAtZero: true, title: { display: true, text: 'Rotación' } } }
			}
		});
		$('#tbodyRotacion').html(tbody || '<tr><td colspan="4" class="text-center text-muted">Sin datos</td></tr>');
	});
}

// ==========================================
// REPORTE DE CLIENTES (reportes/clientes)
// ==========================================
function cargarReporteClientes() {
	var limite = $('#cliLimite').val() || 20;
	var diasInactivo = $('#cliDiasInactivo').val() || 90;
	var params = { limite: limite };

	// Más compras
	ajaxGet('clientesMasCompras', params, function(data) {
		var labels = [], valores = [], tbody = '';
		data.forEach(function(r, i) {
			labels.push(r.cliente.length > 18 ? r.cliente.substring(0, 18) + '...' : r.cliente);
			valores.push(parseInt(r.total_compras));
			tbody += '<tr>';
			tbody += '<td>' + (i + 1) + '</td>';
			tbody += '<td>' + r.cliente + '</td>';
			tbody += '<td class="text-center">' + formatNumber(r.total_compras) + '</td>';
			tbody += '<td class="text-end">' + formatMoney(r.monto_total) + '</td>';
			tbody += '</tr>';
		});
		crearChart('chartTopCompras', {
			type: 'bar',
			data: {
				labels: labels,
				datasets: [{
					label: 'N° Compras',
					data: valores,
					backgroundColor: 'rgba(232,99,10,0.7)',
					borderColor: '#e8630a',
					borderWidth: 1
				}]
			},
			options: {
				responsive: true,
				maintainAspectRatio: false,
				indexAxis: 'y',
				plugins: { legend: { display: false } },
				scales: { x: { beginAtZero: true } }
			}
		});
		$('#tbodyTopCompras').html(tbody || '<tr><td colspan="4" class="text-center text-muted">Sin datos</td></tr>');
	});

	// Mayor facturación
	ajaxGet('clientesMayorFacturacion', params, function(data) {
		var labels = [], valores = [], tbody = '';
		data.forEach(function(r, i) {
			labels.push(r.cliente.length > 18 ? r.cliente.substring(0, 18) + '...' : r.cliente);
			valores.push(parseFloat(r.total_facturado));
			tbody += '<tr>';
			tbody += '<td>' + (i + 1) + '</td>';
			tbody += '<td>' + r.cliente + '</td>';
			tbody += '<td class="text-end">' + formatMoney(r.total_facturado) + '</td>';
			tbody += '<td class="text-center">' + formatNumber(r.total_facturas) + '</td>';
			tbody += '</tr>';
		});
		crearChart('chartTopFacturacion', {
			type: 'bar',
			data: {
				labels: labels,
				datasets: [{
					label: 'Total Facturado ($)',
					data: valores,
					backgroundColor: 'rgba(59,130,246,0.7)',
					borderColor: '#3b82f6',
					borderWidth: 1
				}]
			},
			options: {
				responsive: true,
				maintainAspectRatio: false,
				indexAxis: 'y',
				plugins: { legend: { display: false } },
				scales: { x: { beginAtZero: true } }
			}
		});
		$('#tbodyTopFacturacion').html(tbody || '<tr><td colspan="4" class="text-center text-muted">Sin datos</td></tr>');
	});

	// Inactivos
	$('#spanDiasInactivo').text(diasInactivo);
	ajaxGet('clientesInactivos', { dias: diasInactivo }, function(data) {
		var tbody = '';
		data.forEach(function(r) {
			tbody += '<tr>';
			tbody += '<td>' + r.cliente + '</td>';
			tbody += '<td>' + (r.numero_documento || '-') + '</td>';
			tbody += '<td>' + (r.correo || '-') + '</td>';
			tbody += '<td class="text-center">' + (r.ultima_compra || 'Nunca') + '</td>';
			tbody += '<td class="text-center"><span class="badge bg-warning text-dark">' + (r.dias_inactivo || '∞') + '</span></td>';
			tbody += '</tr>';
		});
		$('#tbodyInactivos').html(tbody || '<tr><td colspan="5" class="text-center text-muted">No hay clientes inactivos</td></tr>');
	});
}

function cargarHistorialCliente() {
	var id = $('#selectClienteHistorial').val();
	if (!id) {
		Swal.fire('Atención', 'Seleccione un cliente', 'warning');
		return;
	}
	ajaxGet('historialCliente/' + id, {}, function(data) {
		$('#panelHistorial').show();
		$('#hcTotalCompras').text(formatNumber(data.resumen ? data.resumen.total_compras : 0));
		$('#hcMontoTotal').text(formatMoney(data.resumen ? data.resumen.monto_total : 0));
		$('#hcPrimeraCompra').text(data.resumen && data.resumen.primera_compra ? data.resumen.primera_compra : '-');
		$('#hcUltimaCompra').text(data.resumen && data.resumen.ultima_compra ? data.resumen.ultima_compra : '-');

		var tbody = '';
		if (data.ventas && data.ventas.length) {
			data.ventas.forEach(function(r) {
				var estado = r.estado == 1
					? '<span class="badge bg-success">Completada</span>'
					: '<span class="badge bg-danger">Anulada</span>';
				tbody += '<tr>';
				tbody += '<td>' + (r.fec_creacion || '-') + '</td>';
				tbody += '<td>' + r.id + '</td>';
				tbody += '<td>' + (r.total_productos || '-') + '</td>';
				tbody += '<td class="text-end">' + formatMoney(r.subtotal) + '</td>';
				tbody += '<td class="text-end">' + formatMoney(r.impuesto) + '</td>';
				tbody += '<td class="text-end">' + formatMoney(r.total) + '</td>';
				tbody += '<td>' + estado + '</td>';
				tbody += '</tr>';
			});
		}
		$('#tbodyHistorial').html(tbody || '<tr><td colspan="7" class="text-center text-muted">Sin compras registradas</td></tr>');
	});
}

// ==========================================
// REPORTE FINANCIERO (reportes/financiero)
// ==========================================
function cargarReporteFinanciero() {
	var desde = $('#finFechaDesde').val();
	var hasta = $('#finFechaHasta').val();
	var params = { fecha_desde: desde, fecha_hasta: hasta };

	// Resumen financiero
	ajaxGet('resumenFinanciero', params, function(data) {
		$('#finIngresos').text(formatMoney(data.ingresos_totales));
		$('#finImpuestos').text(formatMoney(data.total_impuestos));
		$('#finDescuentos').text(formatMoney(data.total_descuentos));

		// Tabla resumen por método
		if (data.por_metodo && data.por_metodo.length) {
			var tbody = '', totalTx = 0, totalMonto = 0;
			data.por_metodo.forEach(function(r) {
				totalTx += parseInt(r.total_transacciones || 0);
				totalMonto += parseFloat(r.monto_total || 0);
			});
			data.por_metodo.forEach(function(r) {
				var pct = totalMonto > 0 ? (parseFloat(r.monto_total) / totalMonto * 100).toFixed(1) : 0;
				tbody += '<tr>';
				tbody += '<td>' + (r.metodo_pago ? r.metodo_pago.charAt(0).toUpperCase() + r.metodo_pago.slice(1) : 'N/A') + '</td>';
				tbody += '<td class="text-center">' + formatNumber(r.total_transacciones) + '</td>';
				tbody += '<td class="text-end">' + formatMoney(r.monto_total) + '</td>';
				tbody += '<td class="text-end">' + pct + '%</td>';
				tbody += '</tr>';
			});
			$('#tbodyResumenFinanciero').html(tbody);
			$('#tfResumenTx').text(formatNumber(totalTx));
			$('#tfResumenMonto').text(formatMoney(totalMonto));
		}
	});

	// Cuentas por cobrar
	ajaxGet('cuentasPorCobrar', {}, function(data) {
		$('#badgePorCobrar').text(data.length);
		var tbody = '', totalPend = 0;
		data.forEach(function(r) {
			totalPend += parseFloat(r.saldo_pendiente || 0);
			tbody += '<tr>';
			tbody += '<td>' + (r.cliente || 'N/A') + '</td>';
			tbody += '<td class="text-center">' + r.id_venta + '</td>';
			tbody += '<td class="text-center">' + (r.fecha_venta || '-') + '</td>';
			tbody += '<td class="text-end">' + formatMoney(r.total_venta) + '</td>';
			tbody += '<td class="text-end">' + formatMoney(r.total_pagado) + '</td>';
			tbody += '<td class="text-end fw-bold text-danger">' + formatMoney(r.saldo_pendiente) + '</td>';
			tbody += '</tr>';
		});
		$('#tbodyCuentasCobrar').html(tbody || '<tr><td colspan="6" class="text-center text-muted">Sin cuentas pendientes</td></tr>');
		$('#tfTotalPendiente').text(formatMoney(totalPend));
		$('#finPorCobrar').text(formatMoney(totalPend));
	});

	// Ingresos por día
	ajaxGet('ingresosPorDia', params, function(data) {
		var labels = [], valores = [];
		data.forEach(function(r) {
			labels.push(r.fecha);
			valores.push(parseFloat(r.monto_total));
		});
		crearChart('chartIngresosDia', {
			type: 'line',
			data: {
				labels: labels,
				datasets: [{
					label: 'Ingresos ($)',
					data: valores,
					borderColor: '#10b981',
					backgroundColor: 'rgba(16,185,129,0.1)',
					fill: true,
					tension: 0.3
				}]
			},
			options: {
				responsive: true,
				maintainAspectRatio: false,
				plugins: { legend: { position: 'top', labels: { usePointStyle: true } } },
				scales: { y: { beginAtZero: true, title: { display: true, text: 'Monto ($)' } } }
			}
		});
	});

	// Flujo de caja
	ajaxGet('flujoCaja', params, function(data) {
		var labels = [], aperturas = [], cierres = [], tbody = '';
		data.forEach(function(r) {
			labels.push(r.fecha_cierre);
			aperturas.push(parseFloat(r.monto_apertura));
			cierres.push(parseFloat(r.monto_cierre));
			var diff = parseFloat(r.monto_cierre) - (parseFloat(r.monto_apertura) + parseFloat(r.total_ventas));
			var cls = diff < 0 ? 'text-danger' : 'text-success';
			tbody += '<tr>';
			tbody += '<td>' + r.fecha_cierre + '</td>';
			tbody += '<td>' + (r.usuario || '-') + '</td>';
			tbody += '<td class="text-end">' + formatMoney(r.monto_apertura) + '</td>';
			tbody += '<td class="text-end">' + formatMoney(r.total_ventas) + '</td>';
			tbody += '<td class="text-end">' + formatMoney(r.monto_cierre) + '</td>';
			tbody += '<td class="text-end ' + cls + ' fw-bold">' + formatMoney(diff) + '</td>';
			tbody += '</tr>';
		});
		crearChart('chartFlujoCaja', {
			type: 'bar',
			data: {
				labels: labels,
				datasets: [
					{ label: 'Apertura', data: aperturas, backgroundColor: 'rgba(59,130,246,0.7)', borderWidth: 1 },
					{ label: 'Cierre', data: cierres, backgroundColor: 'rgba(16,185,129,0.7)', borderWidth: 1 }
				]
			},
			options: {
				responsive: true,
				maintainAspectRatio: false,
				plugins: { legend: { position: 'top', labels: { usePointStyle: true } } },
				scales: { y: { beginAtZero: true } }
			}
		});
		$('#tbodyFlujoCaja').html(tbody || '<tr><td colspan="6" class="text-center text-muted">Sin cierres de caja</td></tr>');
	});
}
