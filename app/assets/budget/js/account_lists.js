var $table = $('#tbTransacoes');
var request;
var filer;
var ofx_resultado;
var curDate = new Date();
var newID='';

var resposta;

Number.prototype.formatMoney = function(c, d, t){
var n = this, 
    c = isNaN(c = Math.abs(c)) ? 2 : c, 
    d = d == undefined ? "." : d, 
    t = t == undefined ? "," : t, 
    s = n < 0 ? "-$" : "$", 
    i = String(parseInt(n = Math.abs(Number(n) || 0).toFixed(c))), 
    j = (j = i.length) > 3 ? j % 3 : 0;
   return s + (j ? i.substr(0, j) + t : "") + i.substr(j).replace(/(\d{3})(?=\d)/g, "$1" + t) + (c ? d + Math.abs(n - i).toFixed(c).slice(2) : "");
 };
 
$(function() {
	$(document).on('click', '#tbTransacoes tr', function(event) {
		if($(event.target).is('#btConciliar')) {
			if ($(event.target).attr('data-conciliado')==1) {
				$(event.target).removeClass('btn-success');
				$(event.target).addClass('btn-secondary');
				$(event.target).attr('data-conciliado',0);
				calculaSaldoGlobal();
			} else {
				$(event.target).addClass('btn-success');
				$(event.target).removeClass('btn-secondary');
				$(event.target).attr('data-conciliado',1);
				calculaSaldoGlobal();
			}
			editaConciliado($(event.target).attr('data-tid'),$(event.target).attr('data-conciliado'));
		} else if ($(this).hasClass('selected') && event.target.type !== 'checkbox') {
			if (!$('#tbTransacoes .editaTransacao').length) {
				if ($(this).attr('data-editavel')==1) {
					adicionaEdicao($(this).attr('id'));
				} else {
					$('#erro').fadeIn(50);
				}
			} else {
				$('#btCancelar').fadeIn(50).fadeOut(20).fadeIn(50);
				$('#btSalvar').fadeIn(50).fadeOut(20).fadeIn(50);
			}
		} else if (event.target.type== 'checkbox') {
			marcaLinha($(event.target).attr('data-indice'),$(event.target).prop('checked'));
		} else if (!$('#tbTransacoes .editaTransacao').length) {
			marcaLinha($(this).attr('data-indice'),true);
		} else {
			$('#btCancelar').fadeIn(50).fadeOut(20).fadeIn(50);
			$('#btSalvar').fadeIn(50).fadeOut(20).fadeIn(50);
		}
	});
	$('#formTransacoes').submit(function(e){
    return false;
	});


	$('#fechaErro').click( function() {
		$('#erro').fadeOut(50);
	});
	
	filer = $('#filer_input').filer({
		showThumbs: false,
		addMore: false,
		allowDuplicates: false,
		limit: 1,
		maxSize: 1,
		extensions: ["ofx"],
		uploadFile: {
			url: '/importOFX/index.php',
			data: null,
            type: 'POST',
            enctype: 'multipart/form-data',
            beforeSend: function(){},
            success: function(data, el){
				ofx_resultado = JSON.parse(data);
				lerPreviaOFX(ofx_resultado,data);
            },
            error: function(el){
                var parent = el.find(".jFiler-jProgressBar").parent();
                el.find(".jFiler-jProgressBar").fadeOut("slow", function(){
                    $("<div class=\"jFiler-item-others text-error\"><i class=\"icon-jfi-minus-circle\"></i> Error</div>").hide().appendTo(parent).fadeIn("slow");    
                });
            },
            statusCode: null,
            onProgress: null,
            onComplete: null
        }
	});
});

function marcaLinha(id,valor) {
	if (id == 'todas') {
		$('#tbTransacoes  tbody :checkbox').prop('checked',valor);
		if (valor) {
			$('#tbTransacoes tr').addClass('selected');
		} else {
			$('#tbTransacoes tr').removeClass('selected');
		}
	} else {
		$('#ck'+id).prop('checked',valor);
		if (valor) {
			$('#r'+id).addClass('selected');
		} else {
			$('#r'+id).removeClass('selected');
		}
	}
	if ($('#tbTransacoes  tbody input:checked').length == $('#tbTransacoes  tbody :checkbox').length) {	
		$('th input:checkbox').prop('checked',true);
	} else {
		$('th input:checkbox').prop('checked',false);
	}
}

function geraListaContas() {
	var lista = '<select name="conta" id="importarNaConta">';
	for (i=0;i<contas.length;i++) {
		lista += '<option value='+contas[i].id+'>'+contas[i].conta_nome+'</option>'
	}
	lista += '</select>';
	return lista;
}

//Esta função lê a prévia do arquivo OFX e formata o modal:
function lerPreviaOFX(arrayOFX,arrayOFX_unparse) {
	$('#OFX_Inicio').fadeOut(20);
	$('#OFX_Resultado').fadeIn(20);
	$('#OFX_Resultado').html('');
	sHTML = `<p><b>Importar de</b>: `+arrayOFX.accountNumber[0]+`<br />
			 <b>Início:</b> `+ arrayOFX.statement.startDate.date +` <b>Término:</b> `+ arrayOFX.statement.endDate.date +`</p>`;
	sHTML += `<br /><strong>Importar em:</strong><br />`;
	sHTML += geraListaContas();
	textoOFX = (JSON.stringify(arrayOFX));
	sHTML += `<textarea rows="4" cols="50" name="OFX" style="display:none">`+arrayOFX_unparse+`</textarea>`
	sHTML += `<input type="text" style="display:none" name="old_url" value="`+window.location.href+`">`;
	sHTML += `<br /><br /><p><strong>Deseja continuar a importação?</strong></p>`;
	$('#OFX_Resultado').append(sHTML);
	if (contaNome!='') {
		$('#importarNaConta').val(contaID);
	}
	$('#btImportarFinal').prop('disabled',false);
}

$(document).on('focus', '.select2', function() {
    $(this).siblings('select').select2('open');
});


$(document).on('click', function(evt) {
	if($(evt.target).is('#btCancelar')) {
        cancelaEdicao();
    } else if ($(evt.target).is('#ckbAll')) {
		$('input:checkbox').prop('checked',$('#ckbAll').prop('checked'));
		if ($('#ckbAll').prop('checked')) {
			$('#tbTransacoes tr').addClass('selected');
		} else {
			$('#tbTransacoes tr').removeClass('selected');
		}
	} else if($(evt.target).is('#btSalvar')) {
		salvaTransacao();
	} else if($(evt.target).is('#btAddSub')) { 
		adicionaSubtransacao();
	} else if($(evt.target).is('#remSubt')) { 
		linha = evt.target.closest('tr');
		linha.remove();
		nrsubTr = +$('#tbTransacoes').find('#countTr').val()-1;
		$('#tbTransacoes').find('#countTr').val(nrsubTr);
		if (!$('#tbTransacoes').find('#sub'+ nrsubTr).length) {
			$('#tbTransacoes').find('#countTr').val(1);
			$('#split').val('false');
		}
	} else if($(evt.target).is('#btAddTransacao')) { 
		adicionaTransacao();
	} else if($(evt.target).is('#btExcluirSel')) { 
		deletarTransacoesSelecionadas();
	} else if($(evt.target).is('#btFile')) { 
		importaArquivo();
	} else if($(evt.target).is('#btImport')) {
		$('#OFX_Inicio').fadeIn(20);
		$('#OFX_Resultado').fadeOut(20);
	} else if($(evt.target).is('#btImportarFinal')) {
		importaArquivoFinal(ofx_resultado);
	} 
});

function importaArquivo() {
	filer.prop('jFiler').reset();	
	$('#filer_input').trigger('click');	
	$('#btImportarFinal').prop('disabled',true);
}

function importaArquivoFinal($arrayOFX) {
	
}

document.onkeydown = function(evt) {
    evt = evt || window.event;
    var isEscape = false;
    if ("key" in evt) {
        isEscape = (evt.key == "Escape" || evt.key == "Esc");
    } else {
        isEscape = (evt.keyCode == 27);
    }
    if (isEscape) {
        cancelaEdicao();
    }
};


function deletarTransacoesSelecionadas() {
		
	$('#tbTransacoes .selected').each(function(index) {
		trid = $(this).attr('data-tid');
		$(this).remove();
		$.post(base_url+"deletaTransacao", { trid: trid });
	});
	calculaSaldoGlobal();
}


function adicionaTransacao() {
	if (contas.length == 0) {
		eModal.alert(lang_error_noaccounts);
	} else if ($('#tbTransacoes .editaTransacao').length){
		$('#btCancelar').fadeIn(10).fadeOut(100).fadeIn(100);
		$('#btSalvar').fadeIn(50).fadeOut(100).fadeIn(100);
	} else {
		rID='New';
		if (contaID==0) {
			contaID='';
			contaNome='';
		}
		proxNr = +$('#tbTransacoes tr:last').attr('data-index')+1;
		
		htmlSum = `<tr id="r`+rID+`" data-indice="`+proxNr+`" data-index="`+proxNr+`" data-tid="" data-editavel="1" style="display:none">
					<td><input id="ck`+proxNr+`" data-indice="`+proxNr+`" type="checkbox"></td>
					<td id="col_conta_nome"></td>
					<td id="col_data"></td>
					<td id="col_sacado_nome"></td>
					<td id="col_categoria"></td>
					<td id="col_memo"></td>
					<td id="col_saida" class="valores"></td>
					<td id="col_entrada" class="valores"></td>
					<td id="col_saldo" class="valores"></td>	
					<td id="col_conciliado">
						<button id="btConciliar" data-conciliado="0" data-tid="rNew" type="button" class="btn btn-secondary btn-circle btn-xs">C</button>
					</td>
				</tr>`;
		htmEditavel = `<tbody id="edita_r`+rID+`">
				<tr class="editaTransacao selected" id="main1" data-parent="`+rID+`">
					<td><input id="tritem_id" name="tritem_id" value="" style="display:none"></td>
					<td><div id="conta_nome" class="input-group-btn"><input type="text" placeholder="`+lang_account_head+`" id="conta" data-formValue="`+contaID+`" value="`+contaNome+`" class="form-control form-inline transacao input-sm typeahead"/></div></td>
					<td><input name="dataTr" type="text" data-provide="datepicker" placeholder="`+lang_account_date+`" id="dataTr" value="`+$.format.date(curDate,'dd/MM/yyyy')+`" class="form-control form-inline transacao input-sm"></td>
					<td><input type="text" placeholder="`+lang_account_payee+`" data-trid="" id="sacado" name="sacado" value="" class="form-control form-inline transacao input-sm"/></td>
					<td id="cat"></td>
					<td><input type="text" placeholder="Memo"  data-trid="" id="memo" name="memo" value="" class="form-control form-inline transacao input-sm"/></td>
					<td><input type="text" name="totalSaida" placeholder="`+lang_account_outflow+`" id="totalSaida" value="" class="form-control form-inline transacao input-sm valor"/></td>
					<td><input type="text" name="totalEntrada" placeholder="`+lang_account_inflow+`" id="totalEntrada" value="" class="form-control form-inline transacao input-sm valor"/></td>
					<td><input type="text" name="split" id="split" value="false" style="display:none"></td>
					<td></td>
				</tr>
				<tr class="editaTransacao selected">
					<td></td><td><input name="contaID" type="text" id="contaID" value="`+contaID+`" style="display:none">
					</td><td><input type="text" name="transacaoID" id="transacaoID" value="" style="display:none"></td>
					<td>
						<button type="button" class="btn btn-info btn-sm" aria-label="Adicionar Subtransação"  id="btAddSub">
						  <span class="glyphicon glyphicon-plus-sign" aria-hidden="true"></span> `+lang_labelSubtransaction+`
						</button>
					</td>							
					<td style="text-align: right">`+lang_remaining_value+`</td><td></td>
					<td></td>
					<td></td>
					<td></td>
					<td></td>
				</tr>
				<tr class="editaTransacao">
					<td></td><td><input name="countTr" id="countTr" value="1"  style="display:none"></td>
					<td></td><td></td><td></td>
					<td></td>
					<td>
						<button type="submit" class="btn btn-success btn-sm" aria-label="Salvar" id="btSalvar">
						  <span class="glyphicon glyphicon-ok-sign" aria-hidden="true"></span> `+lang_btSave+`
						</button>
					</td>
					<td>
						<button type="button" class="btn btn-danger btn-sm" aria-label="Cancelar" id="btCancelar">
						  <span class="glyphicon glyphicon-remove-sign" aria-hidden="true"></span> `+lang_btCancel+`
						</button>
					</td>
					<td></td>
					<td></td>
				</tr>
			</tbody>`;
		$('#tbTransacoes tr:last').after(htmlSum);
		$('#tbEdicao tbody:last').after(htmEditavel);
		
		
		removeSelect2();
		
		$('#cat').html($('.categorias:first').clone());
		$('#cat').find('select').attr('id','categoria');
		$('#cat').find('select').attr('name','categoria');
		$('#cat').attr('id','');
		
		adicionaEdicao('rNew');
		
		$('#tbTransacoes .categorias').select2('val', '0');
		
		
		$('#tbTransacoes').find('#countTr').val(1);
		ligaCompletar();
		if (contaNome=='') {
			$('#tbTransacoes #conta').focus();
		} else {
			$('#tbTransacoes #sacado').focus();
		}
	}
}

function adicionaEdicao(row) {
	var detail = ($('#'+row));
	var id = row;
	var res = $("#edita_"+id);
	detail.after(res.clone().children());	
	ligaCompletar();
	$('#'+row).hide();
	$("input[type=text]").focus(function() {
	   $(this).select();
	});	
}

$table.on('expand-row.bs.table', function(e, index, row, $detail) {
  var id = row._id;
  var res = $("#desc_" + id).html();
  $detail.html(res);
});

$table.on("click-row.bs.table", function(e, row, $tr) {
  if ($tr.next().is('tr.detail-view')) {
    $table.bootstrapTable('collapseRow', $tr.data('index'));
  } else {
    $table.bootstrapTable('expandRow', $tr.data('index'));
  }
});

function adicionaSubtransacao() {
	$('#split').val('true');
	nrsubTr = +$('#tbTransacoes').find('#countTr').val();
	if ($('#tbTransacoes').find('#sub'+ nrsubTr).length) {
		proxNrID = nrsubTr + 1-1;
	} else {
		proxNrID = nrsubTr-1;
		$("#categoria").val('multiplos').trigger('change');
	}
	tritem_ID = '<input id="tritem_id_'+proxNrID+'" name="tritem_id_'+proxNrID+'" val()="" style="display:none">';
	html = `
		<tr class="editaTransacao selected" id="sub`+(proxNrID+1)+`">
			<td><input class="transfpara" type="text" name="transferir_para_id_`+proxNrID+`" id="transferir_para_id_`+proxNrID+`" val()="" style="display:none"></td>
			<td>`+tritem_ID+`</td>
			<td align="right"><a href="#"><span style="font-size: 22px; padding-top:4px;" class="glyphicon glyphicon-remove-circle" aria-hidden="true"  id="remSubt" data-id="`+proxNrID+`"></span></a></td>
			<td><div id="conta_nome" class="input-group-btn"><input type="text" placeholder="`+lang_transferto+`" data-trid="`+proxNrID+`" id="transferir_`+proxNrID+`" name="transferir_`+proxNrID+`>" data-intTr="`+proxNrID+`" value="" class="form-control form-inline transacao input-sm typeahead transferir_para"/></div></td>
			<td id="cat`+proxNrID+`"></td>
			<td><input type="text" placeholder="Memo"  data-trid="`+proxNrID+`" id="memo_`+proxNrID+`" name="memo_`+proxNrID+`" val()="`+$('#memo').val()+`" class="form-control form-inline transacao input-sm" disabled/></td>
			<td><input type="text" placeholder="`+lang_account_outflow+`" data-trid="`+proxNrID+`"  id="saida_`+proxNrID+`" name="saida_`+proxNrID+`" val()="" class="form-control form-inline transacao input-sm valor"/></td>
			<td><input type="text" placeholder="`+lang_account_inflow+`" data-trid="`+proxNrID+`"  id="entrada_`+proxNrID+`" name="entrada_`+proxNrID+`" val()="" class="form-control form-inline transacao input-sm valor"/></td>
			<td></td>
			<td></td>
		</tr>
	`;
	
	if ($('#tbTransacoes').find('#sub'+ nrsubTr).length) {
		$('#tbTransacoes').find('#sub'+ nrsubTr).after(html);
		nrsubTr+=1;
	} else {
		$('#tbTransacoes').find('#main'+ nrsubTr).after(html);
	}
	removeSelect2();
	
	$('#cat'+proxNrID).html($('.categorias:first').clone());
	$('#cat'+proxNrID).find('select').attr('id','categoria_'+proxNrID);
	$('#cat'+proxNrID).find('select').attr('name','categoria_'+proxNrID);
	$('.categorias').select2({
		placeholder: lang_no_category
	});
	$('#categoria_'+proxNrID).select2('val','0');
	$('select').on("select2:select", function (e) { 
		alteraMultiplos(e.params.data.id,e.currentTarget);
	});
	
	$('#tbTransacoes').find('#countTr').val(nrsubTr);
	ligaCompletar();
}

//Esta função verifica se a opção de multiplos foi selecionada, e ativa ou desativa a tela de subtransacoes
function alteraMultiplos(id,categoria) {
	//se a categoria_0 for selecionada com outro valor que multiplos, remove eventuais subitens
	
	if (id!='multiplos' && categoria.id == 'categoria') {
		$('#tbTransacoes').find('[id^=sub]').remove();
		$('#tbTransacoes').find('#countTr').val(1);
		$('#split').val('false');
	} else if ((id=='multiplos') && ($('#tbTransacoes').find('[id^=sub]').length==0)) {
		adicionaSubtransacao();
	}
}

function cancelaEdicao(proxima) {
	
	removeSelect2();
	
	if ($('#tbTransacoes .editaTransacao').length) {
		prowID = $('#tbTransacoes .editaTransacao:first').attr('data-parent');
		$('#tbTransacoes .editaTransacao').remove();
		if (prowID == 'New') {
			$('#r'+prowID).remove();
		} else {
			$('#r'+prowID).show();
		}
		
		
		if (proxima) {
			var proximaLinha = $('#tbTransacoes').find("[data-index='" + (+$('#r'+prowID).attr('data-index')+1) + "']");
			
			if (proximaLinha.attr('data-editavel')==1) {
				adicionaEdicao(proximaLinha.attr('id'));
				$('#categoria').focus();
			}
		}
	}
}

function removeSelect2() {
	$('select').each(function (i, obj) {
		if ($(obj).data('select2'))
		{
			$(obj).select2('destroy');
		}
	});
	if ($('.select2').length) {
		$('.select2').remove();
	}
	if ($('.select2-hidden-accessible')) {
		$('.select2-hidden-accessible').removeClass('select2-hidden-accessible');
	}
}

function editaConciliado(tid,conciliado) {
	// Abort any pending request
    if (request) {
        request.abort();
    }
	
	// Fire off the request to /form.php
	request = $.ajax({
        url: base_url+"editaConciliado",
        type: "post",
        data: {conciliado: conciliado, transacaoID: tid}
    });

    // Callback handler that will be called on success
    request.done(function (response, textStatus, jqXHR){
		
    });

    // Callback handler that will be called on failure
    request.fail(function (jqXHR, textStatus, errorThrown){
        // Log the error to the console
        console.error(
            "The following error occurred: "+
            textStatus, errorThrown, jqXHR.responseText
        );
    });

    // Callback handler that will be called regardless
    // if the request failed or succeeded
    request.always(function () {
    });
	calculaSaldoGlobal();
}

function salvaTransacao() {
	//NAO PERMITE SALVAR SE A TRANSAÇÃO TIVER DESABALANCEADA:
	calculaDiferenca();
	if (+$('#faltandoEntrada').text()!=0 || +$('#faltandoSaida').text()!=0) {
		alert(lang_remaining_error);
		return -1;
	}
	
	// Abort any pending request
    if (request) {
        request.abort();
    }
	// setup some local variables
    var $form = $('#formTransacoes');

    // Let's select and cache all the fields
    var $inputs = $form.find("input, select, button, textarea");
	
    // Serialize the data in the form
    var serializedData = $form.serialize();
	
	
    // Let's disable the inputs for the duration of the Ajax request.
    // Note: we disable elements AFTER the form data has been serialized.
    // Disabled form elements will not be serialized.
    $inputs.prop("disabled", true);
	
	//Atualiza os campos:
	if ($('#rNew').length) {
		var newIndice = $('#rNew').attr('data-index');	
		$('#rNew').attr('id','r'+newIndice);
		$('#tbTransacoes #main1').attr('data-parent',newIndice);
		$('#edita_rNew #main1').attr('data-parent',newIndice);
		$('#edita_rNew').attr('id','edita_r'+newIndice);
	}
	
    var Indice = $('#tbTransacoes #main1').attr('data-parent');
	// Fire off the request to /form.php
	request = $.ajax({
		
        url: base_url+"editaTransacao",
        type: "post",
        data: serializedData
    });

    // Callback handler that will be called on success
    request.done(function (response, textStatus, jqXHR){
		resposta = JSON.parse(response);
		newID = resposta[0];
		$('#r'+Indice).attr('data-tid',newID);
		$('#r'+Indice).find('#btConciliar').attr('data-tid',newID);
		$('#edita_r'+Indice+' #transacaoID').val(newID);
		if (resposta[1] == 1) {
			$('#edita_r'+Indice+' #tritem_id').val(resposta[2][0]);
		} else {
			$('#edita_r'+Indice+' #tritem_id').val(resposta[2][0]);
			for (i=0;i<(+resposta[1]);i++) {
				$('#edita_r'+Indice+' #tritem_id_'+(+i)).val(resposta[2][i]);
			}
		}
    });

    // Callback handler that will be called on failure
    request.fail(function (jqXHR, textStatus, errorThrown){
        // Log the error to the console
        console.error(
            "The following error occurred: "+
            textStatus, errorThrown, jqXHR.responseText
        );
		
    });

    // Callback handler that will be called regardless
    // if the request failed or succeeded
    request.always(function () {
        // Reenable the inputs
        $inputs.prop("disabled", false);
    });
	//rID='New';
	//Deu certo, vamos atualizar a primeira linha e deletar a edição:
	$inputs.prop("disabled", false);
	var linhaEditada = $('#tbTransacoes').find('[id^=main]');
	var linhaEditar = $('#r' + $('#tbTransacoes').find('[id^=main]').attr('data-parent'));
	//linhaEditar.fadeIn(20);		
	linhaEditar.find('#col_conta_nome').html(linhaEditada.find('#conta').val());
	linhaEditar.find('#col_data').html(linhaEditada.find('#dataTr').val());
	linhaEditar.find('#col_sacado_nome').html(linhaEditada.find('#sacado').val());
	var data = linhaEditada.find('#categoria').select2('data');
	linhaEditar.find('#col_categoria').html(data[0].text);
	linhaEditar.find('#col_memo').html(linhaEditada.find('#memo').val());
	total = +linhaEditar.find('#col_entrada').html()-linhaEditar.find('#col_saida').html();
	
	totalNovo = +linhaEditada.find('#totalEntrada').val()-linhaEditada.find('#totalSaida').val();
	
	saldo = +linhaEditar.find('#col_saldo').html()+(totalNovo-total)
	linhaEditar.find('#col_saida').html(linhaEditada.find('#totalSaida').val());
	linhaEditar.find('#col_entrada').html(linhaEditada.find('#totalEntrada').val());
	linhaEditar.find('#col_saldo').html(saldo);
	
	$('#edita_r'+Indice).children().replaceWith();
	$('#edita_r'+Indice).append($('#tbTransacoes .editaTransacao').clone());
	
	cancelaEdicao(false);
	
	calculaSaldoGlobal();
}

// constructs the suggestion engine
var lista_contas = new Bloodhound({
  queryTokenizer: Bloodhound.tokenizers.whitespace,                                                                                                                                                      
  datumTokenizer: Bloodhound.tokenizers.obj.whitespace('conta_nome'),  
  
  local: contas
});

function lista_contasWithDefault(q, sync) {
	  if (q === '') {
    sync(lista_contas.all()); // This is the only change needed to get 'ALL' items as the defaults
  }

  else {
    lista_contas.search(q, sync);
  }
}

function ligaCompletar() {
	$('#conta_nome .typeahead').typeahead({
	  hint: false,
	  highlight: true,
	  minLength: 0
	},
	{
	  name: 'contas',
	  source: lista_contasWithDefault,
	  display: 'conta_nome',
	  templates: {
		header: '<h3 class="nome_contas">'+lang_account_head+'</h3>',
		empty: '<h3 class="nome_contas">'+lang_account_head+'</h3><h5 class="conteudo">'+lang_error_account_notfound+'</h5>',
		suggestion: function(data) {
			return '<p>' + data.conta_nome + '</p>';
		}
	  }
	});
	
	$('.transferir_para').keydown ( function(ev) {
		if ($(this).val().length) {
			$('#tbTransacoes #categoria_'+$('#'+ev.target.id).attr('data-intTr')).select2('val', '0');
			$('#tbTransacoes #categoria_'+$('#'+ev.target.id).attr('data-intTr')).prop('disabled',true);
		} else {
			$('#tbTransacoes #categoria_'+$('#'+ev.target.id).attr('data-intTr')).prop('disabled',false);
		}
	});
	
	$('.transferir_para').focusout ( function(ev) {
		if ($(this).val().length) {
			$('#tbTransacoes #categoria_'+$('#'+ev.target.id).attr('data-intTr')).select2('val', '0');
			$('#tbTransacoes #categoria_'+$('#'+ev.target.id).attr('data-intTr')).prop('disabled',true);
		} else {
			$('#tbTransacoes #categoria_'+$('#'+ev.target.id).attr('data-intTr')).prop('disabled',false);
		}
	});
	
	$('#tbTransacoes').find('[id^=saida_]').focusout( function() {
		var nomeCampo = $(this).attr('id');
		if (!$.isNumeric($(this).val())) {
			$(this).val('0');
		} else if ($(this).val()!=0) {
			$('#entrada_'+nomeCampo.substr(6,nomeCampo.length - 6)).val('0');
		}
		calculaDiferenca();
	});
	
	$('#tbTransacoes').find('[id^=entrada_]').focusout( function() {
		if (!$.isNumeric($(this).val())) {
			$(this).val('0');
		} else if ($(this).val()!=0) {
			$('#saida_'+nomeCampo.substr(8,nomeCampo.length - 8)).val('0');
		}
		calculaDiferenca();
	});
	
	$('#tbTransacoes #totalSaida').focusout( function() {
		if (!$.isNumeric($(this).val())) {
			$(this).val('0');
		} else if ($(this).val()!=0) {
			$('#totalEntrada').val('0');
		}
		calculaDiferenca();
	});
	
	$('#tbTransacoes #totalEntrada').focusout( function() {
		if (!$.isNumeric($(this).val())) {
			$(this).val('0');
		} else if ($(this).val()!=0) {
			$('#totalSaida').val('0');
		}
		calculaDiferenca();
	});
	
	$('#conta').bind('typeahead:autocomplete', function(ev, suggestion) {
		salvaOpcao(suggestion.id, '#contaID');
	});
	$('#conta').bind('typeahead:select', function(ev, suggestion) {
		salvaOpcao(suggestion.id, '#contaID');
	});
	$('#conta').bind('typeahead:cursorchange', function(ev, suggestion) {
		salvaOpcao(suggestion.id, '#contaID');
	});
	
	$('.transferir_para').bind('typeahead:autocomplete', function(ev, suggestion) {
		salvaOpcao(suggestion.id, '#transferir_para_id_'+$('#'+ev.target.id).attr('data-intTr'));
	});
	$('.transferir_para').bind('typeahead:select', function(ev, suggestion) {
		salvaOpcao(suggestion.id, '#transferir_para_id_'+$('#'+ev.target.id).attr('data-intTr'));
	});
	$('.transferir_para').bind('typeahead:cursorchange', function(ev, suggestion) {
		salvaOpcao(suggestion.id, '#transferir_para_id_'+$('#'+ev.target.id).attr('data-intTr'));
	});
	
	$('#tbTransacoes').find('[id^=transferir_]').blur(function() {
		if ($(this).val()=='') {
			salvaOpcao('', '#transferir_para_id_'+$(this).attr('data-intTr'));
		}
	});
	
	$('select').select2({
		placeholder: lang_no_category,
		selectOnClose: true
	});
	$('select').on("select2:select", function (e) { 
		alteraMultiplos(e.params.data.id,e.currentTarget);		
		
	});
	
	$('select').on("select2:close", function (e) { 
		if ($(this).closest('tr').is($('#main1'))) {
			$('#tbTransacoes #memo').focus();
		} else {
			var idLinha = $(this).closest('tr').attr('id');
			var nextCampo =  +idLinha.substr(3,idLinha.length - 3)-1;
			$('#tbTransacoes #saida_'+nextCampo).focus();
		}
	});
	
	
	$('#dataTr').datepicker({
		format: "dd/mm/yyyy",
		todayBtn: "linked",
		autoclose: true,
		toggleActive: true,
		daysOfWeekHighlighted: "0,6",
		language: "pt-BR",
		todayHighlight: true
	}).on('changeDate', function (date, options) {
		//$('#sacado').focus();
		//var parts = date.split('/');
	//please put attention to the month (parts[0]), Javascript counts months from 0:
	// January - 0, February - 1, etc
		//curDate= new Date(parts[2],parts[0]-1,parts[1]); 
		curDate = date.date;
		$(this).select();
	});
	
	jQuery(function($) {
		$('input.valor').autoNumeric('init',{aSep: '', aSign: '', vMin: '-999999999.99'});    
  	});
}

function salvaOpcao (valor, campo) {
	$('#tbTransacoes').find(campo).val(valor);
}

function monthSorter(a, b) {
    if (a.month < b.month) return -1;
    if (a.month > b.month) return 1;
    return 0;
}

function calculaDiferenca() {
	var sumSaida = 0
	$('#tbTransacoes').find('[id^=saida_]').each(function () {
        sumSaida += 1*($(this).val());
    });
	var sumEntrada = 0
	$('#tbTransacoes').find('[id^=entrada_]').each(function () {
        sumEntrada += 1*($(this).val());
    });
	if ($('#tbTransacoes #saida_0').length) {
		$('#tbTransacoes #faltandoEntrada').text(parseFloat(+$('#tbTransacoes #totalEntrada').val()-sumEntrada).toFixed(2));
		$('#tbTransacoes #faltandoSaida').text(parseFloat(+$('#tbTransacoes #totalSaida').val()-sumSaida).toFixed(2));
	} else {
		$('#tbTransacoes #faltandoEntrada').text('0.00');
		$('#tbTransacoes #faltandoSaida').text('0.00');
	}
}

function calculaSaldoGlobal() {
	var sumSaidaConta = {};
	var sumEntradaConta = {};
	var sumSaida = 0;
	var sumSaidaC = 0;
	var sumSaidaNC = 0;
	var sumEntrada = 0;
	var sumEntradaC = 0;
	var sumEntradaNC = 0;
	$('#tbTransacoes #col_saida').each(function () {
        sumSaida += 1*($(this).text());
		if ($(this).parent('tr').find('#btConciliar').attr('data-conciliado')==1) {
			sumSaidaC += 1*($(this).text());
		} else {
			sumSaidaNC += 1*($(this).text());
		}
		if (!$.isNumeric(sumSaidaConta[$(this).parent('tr').find('#col_conta_nome').text()]))
			sumSaidaConta[$(this).parent('tr').find('#col_conta_nome').text()]=0;
		sumSaidaConta[$(this).parent('tr').find('#col_conta_nome').text()]+=1*($(this).text());
    });
	$('#tbTransacoes #col_entrada').each(function () {
        sumEntrada += 1*($(this).text());
		if ($(this).parent('tr').find('#btConciliar').attr('data-conciliado')==1) {
			sumEntradaC += 1*($(this).text());
		} else {
			sumEntradaNC += 1*($(this).text());
		}
		if (!$.isNumeric(sumEntradaConta[$(this).parent('tr').find('#col_conta_nome').text()]))
			sumEntradaConta[$(this).parent('tr').find('#col_conta_nome').text()]=0;
		sumEntradaConta[$(this).parent('tr').find('#col_conta_nome').text()]+=1*($(this).text());
    });
	var saldoTotal = sumEntrada - sumSaida;
	var saldoTotalC = sumEntradaC - sumSaidaC;
	var saldoTotalNC = sumEntradaNC - sumSaidaNC;
	$('#saldoGeral').text(parseFloat(saldoTotal).formatMoney(2));
	$('#saldoConciliado').text(parseFloat(saldoTotalC).formatMoney(2));
	$('#saldoNConciliado').text(parseFloat(saldoTotalNC).formatMoney(2));
	if (contaID==0) {
		$('#somaTotal').text(parseFloat(saldoTotal).formatMoney(2));
		for (var i = 0; i < contas.length; i++) {
			var entrada = parseFloat(0);
			var saida = parseFloat(0);
			if (isNaN(sumEntradaConta[contas[i].conta_nome])) {
				entrada = 0;
			} else {
				entrada = parseFloat(sumEntradaConta[contas[i].conta_nome]);
			}
			if (isNaN(sumSaidaConta[contas[i].conta_nome])) {
				saida = 0;
			} else {
				saida = parseFloat(sumSaidaConta[contas[i].conta_nome]);
			}
			
			$("[id='"+ contas[i].conta_nome+"']").text(parseFloat(entrada - saida).formatMoney(2));
		}
	} else { //A view atual é de uma conta especifica...
		var oldAccountValue = parseFloat($('#menu_saldo_'+contaNome).text().replace('$','').replace(',','')).toFixed(2);
		var deltaTotal = (parseFloat(saldoTotal).toFixed(2)-oldAccountValue);
		var newTotalValue = parseFloat($('#somaTotal').text().replace('$','').replace(',','')).toFixed(2);
		newTotalValue = parseFloat(newTotalValue) + parseFloat(deltaTotal);
		$("[id='"+contaNome+"']").text(parseFloat(saldoTotal).formatMoney(2));
		$('#somaTotal').text(parseFloat(newTotalValue).formatMoney(2));
	}
	if (saldoTotal>=0) {
		$('#saldoGeral').removeClass('SaldoNeg');
		$('#saldoGeral').addClass('SaldoPos');
	} else {
		$('#saldoGeral').addClass('SaldoNeg');
		$('#saldoGeral').removeClass('SaldoPos');
	}
	if (saldoTotalC>=0) {
		$('#saldoConciliado').removeClass('SaldoNeg');
		$('#saldoConciliado').addClass('SaldoPos');
	} else {
		$('#saldoConciliado').addClass('SaldoNeg');
		$('#saldoConciliado').removeClass('SaldoPos');
	}
	if (saldoTotalNC>=0) {
		$('#saldoNConciliado').removeClass('SaldoNeg');
		$('#saldoNConciliado').addClass('SaldoPos');
	} else {
		$('#saldoNConciliado').addClass('SaldoNeg');
		$('#saldoNConciliado').removeClass('SaldoPos');
	}
	$(document).find('[id^=menu_saldo_]').each(function () {
			var nomeConta = $(this).attr('id');
			nomeConta = nomeConta.substr(11,nomeConta.length-11);
			if ($.isNumeric(sumEntradaConta[nomeConta]))
				$(this).text(parseFloat(+sumEntradaConta[nomeConta]-sumSaidaConta[nomeConta]).formatMoney(2));
	});
}