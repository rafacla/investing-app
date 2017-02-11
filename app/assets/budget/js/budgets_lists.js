var oldBudget;
var request;
var gastos = new Array();

$(function() {
	calculaSumarias();
	
	$("#mesano").datepicker( {
		format: "yyyymm",
		startView: "months", 
		minViewMode: "months",
		autoclose: true
	});
	
	$( "#mesano" ).change(function() {
		var pathArray = window.location.pathname.split( '/' );
		window.location.href =  window.location.origin + '/' + pathArray[1] + '/' + pathArray[2] + '/' + pathArray[3] + '/' + $('#mesano').val();
	});
	
	var $adicionaCatItem = $("#adicionaCatItem");
	
	$adicionaCatItem.on("click", "a", function() {
		$adicionaCatItem.hide();
	});
	
	$(document).on('click', function(e) {
		if($(e.target).is('#cat_grupo_id')) {
			$adicionaCatItem.css({
				display: "block",
				left: e.pageX,
				top: e.pageY
			});
			$adicionaCatItem.removeClass('editar');
			$('#categoriagrupo_id').val($(e.target).attr('data-grupo-id'));
			$('#categoriaitem_id').val(0);
			$('#adicionaCatItem #label').text('Adicionar item a categoria:');
			$('#novaCategoria').val('');
			$('#novaCategoria').focus();
		} else if ($(e.target).is('#cat_grupo_edit')) {
			$('#editaCatGrupo').css({
				display: "block",
				left: e.pageX,
				top: e.pageY
			});
			$('#editaCatGrupo').addClass('editar');
			$('#editaCatGrupo #categoria').val($(e.target).attr('data-valor'));
			$('#editaCatGrupo #categoriagrupo_id').val($(e.target).attr('data-grupo-id'));
			$('#editaCatGrupo #apagarCategoria').attr('href',base_url+'deletaCategoriaGrupo/'+$(e.target).attr('data-grupo-id'));
		} else if($(e.target).is('#cat_edit')) {
			$adicionaCatItem.css({
				display: "block",
				left: e.pageX,
				top: e.pageY
			});
			$adicionaCatItem.addClass('editar');
			$('#adicionaCatItem #novaCategoria').val($(e.target).attr('data-valor'));
			$('#adicionaCatItem #categoriagrupo_id').val($(e.target).attr('data-grupo-id'));
			$('#adicionaCatItem #categoriaitem_id').val($(e.target).attr('data-catid'));
			$('#adicionaCatItem #label').text('Editar categoria:');
			$('#adicionaCatItem #apagarCategoria').attr('href',base_url+'deletaCategoria/'+$(e.target).attr('data-catid'));
			$('#adicionaCatItem #novaCategoria').focus();
		} else if ($(e.target).is('td')) {
			if (e.ctrlKey) {
				$(e.target).closest('tr').addClass('selected');
				$(e.target).closest('tr').find(':checkbox').prop('checked', true);
				listaGastos();
			} else {
				$(':checkbox').prop('checked', false);
				$('tr.selected').removeClass('selected');
				$(e.target).closest('tr').addClass('selected');
				$(e.target).closest('tr').find(':checkbox').prop('checked', true);
				listaGastos();
			}
		} else {
			$adicionaCatItem.hide();
		}
	});
	
	$(document).on('keydown',function(e) {
		var code = e.keyCode || e.which;
		if(code == 27) { //ESC
			$('#adicionaCatItem').hide();
			$('#editaCatGrupo').hide();
		}
	});
	
	$(':checkbox').on('change',function(e) {
		//console.log(e.target);
		listaGastos();
	});
	
	$('.orcado').on('keydown',function(e) {
		var code = e.keyCode || e.which;
		if(code == 27) { //ESC
			$(e.target).val(oldBudget.toFixed(2));
			$(e.target).select();
		} else if (code==38) {// cima
			if ($(e.target).closest('tr').attr('data-index')>1) {
				dI = $(e.target).closest('tr').attr('data-index')-1;
				dII = $(e.target).closest('tr').attr('data-index')-2;
				if ($('#tbBudgets').find("[data-index='"+dI+"']").hasClass('filha')) {
					$('#tbBudgets').find("[data-index='"+dI+"']").find('#orcado').focus();
					//$('#tbBudgets').find("[data-index='"+dI+"']").find('#orcado').select();
				} else if ($('#tbBudgets').find("[data-index='"+dII+"']").hasClass('filha')) {
					$('#tbBudgets').find("[data-index='"+dII+"']").find('#orcado').focus();
					//$('#tbBudgets').find("[data-index='"+dII+"']").find('#orcado').select();
				}				
			}
		} else if (code==40) { // down
			dI = +$(e.target).closest('tr').attr('data-index')+1;
			dII = +$(e.target).closest('tr').attr('data-index')+2;
			if ($('#tbBudgets').find("[data-index='"+dI+"']").hasClass('filha')) {
				$('#tbBudgets').find("[data-index='"+dI+"']").find('#orcado').focus();
				//$('#tbBudgets').find("[data-index='"+dI+"']").find('#orcado').select();
			} else if ($('#tbBudgets').find("[data-index='"+dII+"']").hasClass('filha')) {
				$('#tbBudgets').find("[data-index='"+dII+"']").find('#orcado').focus();
				//$('#tbBudgets').find("[data-index='"+dII+"']").find('#orcado').select();
			}				
		} else if (code==13) { //enter
			$(e.target).focusout();
			$(e.target).focus();
			$(e.target).select();
		}
	});
	
	$('.orcado').on('focusin',function(e) {
		oldBudget = parseFloat($(e.target).val());
		$(e.target).select();
	});
	
	$('.orcado').on('focusout',function(e) {
		if (!isNaN(parseFloat($(e.target).val()))) {
			Dif = parseFloat($(e.target).val())-oldBudget;
			disponivel = parseFloat($('#disp_'+$(e.target).attr('data-budgetID')).text());
			$('#disp_'+$(e.target).attr('data-budgetID')).text(parseFloat(+disponivel+Dif).toFixed(2));
			if (parseFloat(+disponivel+Dif)>0) {
				$('#disp_'+$(e.target).attr('data-budgetID')).removeClass('menorZero');
				$('#disp_'+$(e.target).attr('data-budgetID')).removeClass('zero');
				$('#disp_'+$(e.target).attr('data-budgetID')).addClass('maiorZero');
			}else if  (parseFloat(+disponivel+Dif)==0) {
				$('#disp_'+$(e.target).attr('data-budgetID')).removeClass('menorZero');
				$('#disp_'+$(e.target).attr('data-budgetID')).removeClass('maiorZero');
				$('#disp_'+$(e.target).attr('data-budgetID')).addClass('zero');
			} else {
				$('#disp_'+$(e.target).attr('data-budgetID')).removeClass('maiorZero');
				$('#disp_'+$(e.target).attr('data-budgetID')).addClass('menorZero');
				$('#disp_'+$(e.target).attr('data-budgetID')).removeClass('zero');
			}
			
			$.post(base_url+"alteraBudget", 
				{ 
					categoriaitem_id: $(e.target).attr('data-budgetID'),
					budget_valor: $(e.target).val(),
					mesano: $('#mesano').val() 
				});
			//	.done(function(msg){  })
			//	.fail(function(xhr, status, error) {	});
			
			$('tr.orcado .valor').text(parseFloat((Dif*1)+parseFloat($('tr.orcado .valor').text()).toFixed(2)*1).toFixed(2));
			$('.valorBudget .valor').text((parseFloat($('.receitas .valor').text())-parseFloat($('tr.orcado .valor').text())-parseFloat($('.excedente .valor').text())).toFixed(2));
			if (parseFloat($('.valorBudget .valor').text())>=0) {
				$('.valorBudget').removeClass('negativo');
				$('.valorBudget').addClass('positivo');
			} else {
				$('.valorBudget').addClass('negativo');
				$('.valorBudget').removeClass('positivo');
			}
			valorMod = $(e.target).val();
			$(e.target).val(parseFloat(valorMod).toFixed(2));
			calculaSumarias();
			corrigeGraficoBudget(($('tr.orcado .valor').text()),($('#legendaReceita .valor').text()),($('#legendaGastos .valor').text()));
		} else {
			$(e.target).val(oldBudget.toFixed(2));
		}
	});
});

function corrigeGraficoBudget(budget,receitas,gastos) {
	if (receitas == 0) {
		budget_ratio = 1;
	} else {
		budget_ratio = (budget/receitas);
	}
	if (receitas == 0) {
		if (budget == 0) {
			gasto_ratio = 1;
		} else {
			gasto_ratio =(gastos)/budget;
		}
	} else {
		gasto_ratio = (gastos)/receitas;
	}
	if (budget_ratio > 1) {
		pb_budget = 1 ;
	} else {
		pb_budget = budget_ratio;
	}
	if (gasto_ratio > 1) {
		pb_gasto = 1;
	} else {
		pb_gasto = gasto_ratio;
	}
	if (pb_gasto > pb_budget) {
		pb_budget_s="0%";
		pb_gasto_s = pb_gasto*100 + "%";
	} else {
		pb_budget_s = (pb_budget - pb_gasto)*100 + "%";
		pb_gasto_s = pb_gasto*100 + "%";
	}
	$('#pgb-orcado').width(pb_budget_s);
	$('#legendaOrcado .valor').text(parseFloat(budget).toFixed(2));
	$('#legendaReceita .valor').text(parseFloat(receitas).toFixed(2));
	$('#pgb-gasto').width(pb_gasto_s);
	$('#legendaGastos .valor').text(parseFloat(gastos).toFixed(2));
	
	
	if (pb_gasto > pb_budget) {
		$('#pgb-gasto').removeClass('progress-bar-success');
		$('#pgb-gasto').addClass('progress-bar-danger');
		$('#pgb-gasto_l').removeClass('progress-bar-success');
		$('#pgb-gasto_l').addClass('progress-bar-danger');
		$('#legendaGastos .descricao').text('Sobregastos:');
	} else {
		$('#pgb-gasto').addClass('progress-bar-success');
		$('#pgb-gasto').removeClass('progress-bar-danger');
		$('#pgb-gasto_l').addClass('progress-bar-success');
		$('#pgb-gasto_l').removeClass('progress-bar-danger');
		$('#legendaGastos .descricao').text('Gastos:');
	}
	
	if (budget > receitas) {
		$('#pgb-orcado').addClass('progress-bar-info');
		$('#pgb-orcado_l').addClass('progress-bar-info');
		$('#legendaOrcado .descricao').text('Sobreorçado:');
	} else {
		$('#pgb-orcado').removeClass('progress-bar-info');
		$('#pgb-orcado_l').removeClass('progress-bar-info');
		$('#legendaOrcado .descricao').text('Orçado:');
	}
}

function calculaSumarias() {
	$('.sumaria').each( function() {
		var sum = 0;
		$('[data-parent="'+$(this).attr('id')+'"]').find('.orcado').each(function() {
			sum +=+($(this).find('input').val());
		});
		$(this).find('.orcado').text(sum.toFixed(2));
		var sum = 0;
		$('[data-parent="'+$(this).attr('id')+'"]').find('.gasto').each(function() {
			sum +=+($(this).text());
		});
		$(this).find('.gasto').text(sum.toFixed(2));
		var sum = 0;
		$('[data-parent="'+$(this).attr('id')+'"]').find('.disponivel').each(function() {
			sum +=+($(this).find('span').text());
		});
		$(this).find('.disponivel').text(sum.toFixed(2));
	});
}

function listaGastos() {
	var strIds = '';
	$('#tbBudgets .filha.selected').each(function() {
		if (strIds.length) {
			strIds += ',';
		}
		strIds += $(this).attr('data-catid');
	});
	var mesAno = $('#mesano').val();
	
	if (request) {
        request.abort();
    }
	
	if (strIds.length) {
		$('#semGastos').hide();
		$('#resultados').show();
		
		// Fire off the request to /form.php
		request = $.ajax({
			url: base_url+"listaGastos",
			type: "post",
			data: {mesano: mesAno, categoriaitem_id: strIds}
		});

		// Callback handler that will be called on success
		request.done(function (response, textStatus, jqXHR){
			gastos = JSON.parse(response);
			var inserehtml = '';
			inserehtml = `
				<table style="width:100%">
					<thead>
						<tr>
							<th style="width:25%">Data</th>
							<th style="width:50%">Sacado</th>
							<th style="width:25%">Valor</th>
						</tr>
					</thead>
					<tbody>`;
			$.each(gastos, function(index, value) {
				inserehtml += `
						<tr>
							<td>`+value.data.substr(0,5)+`</td>
							<td>`+value.sacado_nome+`</td>
							<td>`+value.valor+`</td>
						</tr>`;
			});
			inserehtml += `
					</tbody>
				</table>`;
			
			$('#listaGastos #resultados').html(inserehtml);
		});
	} else {
		$('#semGastos').show();
		$('#resultados').hide();
	}
}
	