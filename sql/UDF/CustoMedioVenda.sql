DROP FUNCTION IF EXISTS CustoMedioVenda//
 
CREATE FUNCTION CustoMedioVenda(ordemID INT, dataOrdem DATE, profileID INT) RETURNS DECIMAL(10,2)
BEGIN
	DECLARE custoVendaAcumulado DECIMAL(10,2) DEFAULT 0;
	DECLARE custoVendaAtual DECIMAL(10,2) DEFAULT 0;
	DECLARE posAcumulada INT DEFAULT 0;
	DECLARE posAtual INT DEFAULT 0;
	DECLARE vOp VARCHAR(1);
	DECLARE maxRecords INT DEFAULT 0;
	DECLARE ativo VARCHAR(10);
	DECLARE tipoOp VARCHAR(2);
	DECLARE corID INT DEFAULT 0;
	DECLARE custoMedio DECIMAL(10,2) DEFAULT 0;
	DECLARE iLoop INT DEFAULT 0;
    
	SELECT ativo_nome, tipo_operacao, corretora_id INTO ativo, tipoOp, corID FROM appinv_vw_acoes_ordens WHERE ordem_id = ordemID;
	SELECT count(*) INTO maxRecords FROM appinv_vw_acoes_ordens WHERE profile_id = profileID AND ativo_nome = ativo AND tipo_operacao = tipoOp AND corretora_id = corID AND nota_data <= dataOrdem;
	
	CMloop: WHILE iLoop < maxRecords DO
		SELECT operacao INTO vOp FROM appinv_vw_acoes_ordens WHERE profile_id = profileID AND ativo_nome = ativo AND tipo_operacao = tipoOp AND corretora_id = corID ORDER BY nota_data, ordem_id LIMIT iLoop,1;
		IF vOp = 'v' THEN
			SELECT ativo_quantidade, (ativo_trans_valor-taxa_cblc-taxa_bovespa-taxa_corretagem) INTO posAtual,custoVendaAtual FROM appinv_vw_acoes_ordens WHERE profile_id = profileID AND ativo_nome = ativo AND tipo_operacao = tipoOp AND corretora_id = corID ORDER BY nota_data, ordem_id LIMIT iLoop,1;
			SET custoVendaAcumulado = custoVendaAcumulado + custoVendaAtual;
			SET posAcumulada = posAcumulada - posAtual;
		ELSE
			IF iLoop+1 >= maxRecords THEN
				LEAVE CMloop;
			ELSE
				SELECT ativo_quantidade INTO posAtual FROM appinv_vw_acoes_ordens WHERE profile_id = profileID AND ativo_nome = ativo AND tipo_operacao = tipoOp AND corretora_id = corID ORDER BY nota_data, ordem_id LIMIT iLoop,1;
				SET custoVendaAcumulado = custoVendaAcumulado + (custoVendaAcumulado/posAcumulada)*posAtual;
				SET posAcumulada = posAcumulada + posAtual;
			END IF;
		END IF;
		SET iLoop = iLoop+1;	
	END WHILE;
	IF posAcumulada < 0 THEN
		SET custoMedio = custoVendaAcumulado/posAcumulada;
	ELSE
		SET custoMedio = 0;
	END IF;
	RETURN ABS(custoMedio);
END