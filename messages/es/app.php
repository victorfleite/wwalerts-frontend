<?php
return [ 
		
		// Page: emergencia
		'emergencia.index.title' => 'Avisos de Eventos Meteorológicos Severos',
		'emergencia.index.label.id'=>'ID',
		'emergencia.index.label.risco' => 'Risco',
		'emergencia.index.label.codar' => 'Cobrade',
		'emergencia.index.label.novo' => 'Novo',
		'emergencia.index.label.entrar' => 'Entrar',
		'emergencia.index.label.data' => 'Data',
		'emergencia.index.label.duracao' => 'Duração Estimada',
		'emergencia.index.label.instituicao' => 'Instituição',
		'emergencia.index.label.protocolo.status' => 'Status',
		'emergencia.index.label.autor' => 'Responsável',
		'emergencia.index.label.analise' => 'Data da análise',
		'emergencia.index.label.previsao' => ', previsão para',
		'emergencia.index.label.salvar' => 'Salvar',
		
		'emergencia.index.label.novo.aviso' => 'Criar Aviso',
		'emergencia.index.label.pesquisa_avancada' => 'Pesquisa Avançada',
		'emergencia.index.label.encerrado' => 'Encerrado',
		'emergencia.index.encerrado.sim' => 'Sim',
		'emergencia.index.encerrado.nao' => 'Não',
		
		'emergencia.create.label.botao.salvar_aviso'=>'Salvar Aviso',
		'emergencia.create.label.botao.atualizar_aviso'=>'Atualizar Aviso',
		
		'emergencia.show.titulo' => 'Aviso de Evento Meteorológico Severo',
		'emergencia.show.avisos' => 'Avisos',
		'emergencia.show.botao.estender.aviso' => 'Estender Aviso',
		'emergencia.show.botao.finalizar.aviso' => 'Finalizar Aviso',
		'emergencia.show.label.codar' => 'Cobrade',
		'emergencia.show.label.risco' => 'Risco',
		'emergencia.show.label.data' => 'Data',
		'emergencia.show.label.duracao' => 'Duração Estimada',
		'emergencia.show.label.instituicao' => 'Instituição',
		'emergencia.show.label.protocolo.status' => 'Cor',
		'emergencia.show.label.autor' => 'Responsável',
		'emergencia.show.acoes.label.tipoacao' => 'Tipo Ação',
		'emergencia.show.acoes.label.descricao' => 'Descrição',
		
		'emergencia.cancel.titulo' => 'Cancelamento de Aviso de Evento Meteorológico Severo',
		'emergencia.cancel.label.motivo' => 'Motivo do Cancelamento',
		'emergencia.cancel.label.botao.cancelar' => 'Cancelar Aviso',
		
		
		/* model emergencia */
		'emergencia.id' => 'ID',
		'emergencia.version' => 'Versão',
		'emergencia.codar_id' => 'Cobrade',
		'emergencia.data_inicial' => 'Data Inicial',
		'emergencia.data_model' => 'Data do Modelo',
		'emergencia.duracao_estimada' => 'Duração Estimada',
		'emergencia.location' => 'Polígono',
		'emergencia.owner_id' => 'Responsável',
		'emergencia.risco_id' => 'Risco',
		'emergencia.encerrado' => 'Encerrado',
		'emergencia.descricao' => 'Descrição',
		'emergencia.message.error.validacao_datas' => 'Data Inicial não pode ser maior que a Duração estimada',	
		
		
		/* emergencia_log i18n */
		'emergencia_log.i18n.emergencia_criada' => 'Aviso de Eventos Meteorológicos Severos criado',
		'emergencia_log.i18n.emergencia_atualizada' => 'Aviso de Eventos Meteorológicos Severos atualizado',
		'emergencia_log.i18n.emergencia_cancelada' => 'Aviso de Evento Meteorológico Severo cancelado',
				
		'emergencia_log.descricao.emergencia.criada' => 'Aviso de Eventos Meteorológicos Severos criado',
		'emergencia_log.descricao.emergencia.atualizada'=>'Aviso de Eventos Meteorológicos Severos atualizado',
		'emergencia_log.descricao.emergencia.cancelada' => 'Aviso de Eventos Meteorológicos Severos cancelado',

		
		'mapa.emergencia.capMsgTypeAlert'=>'Alerta',
		'mapa.emergencia.capMsgTypeCancel'=>'Cancelado',
		'mapa.emergencia.capMsgTypeUpdate'=>'Atualizado',
		'mapa.emergencia.capSeverityModerate'=>'Perigo Pontencial',
		'mapa.emergencia.capSeveritySevere'=>'Perigo',
		'mapa.emergencia.capSeverityExtreme'=>'Grande Perigo',
		
		/* model emergencia_log */
		'emergencia_log.id' => 'ID',
		'emergencia_log.data' => 'Data',
		'emergencia_log.descricao' => 'Evento',
		'emergencia_log.responsavel_id' => 'Responsável',
		
		/* model usuario */
		'usuario.id' => 'ID',
		'usuario.email' => 'Email',
		'usuario.fone_cel' => 'Telefone Celular',
		'usuario.fone_com' => 'Telefone Comercial',
		'usuario.fone_res' => 'Telefone Residencial',
		'usuario.instituicao_id' => 'Instituição',
		'usuario.nome' => 'Nome',
		'usuario.senha' => 'Senha',
		'usuario.ativo' => 'Ativo',
		
		'mapa.emergencia.risco_0' => 'Nada previsto',
		'mapa.emergencia.risco_1' => 'Perigo potencial',
		'mapa.emergencia.risco_2' => 'Perigo',
		'mapa.emergencia.risco_3' => 'Grande perigo',
		
		'mapa.emergencia.codar_0' => 'Ventos Costeiros',
		'mapa.emergencia.codar_1' => 'Ressaca',
		'mapa.emergencia.codar_2' => 'Onda de Frio',
		'mapa.emergencia.codar_3' => 'Tornados',
		'mapa.emergencia.codar_4' => 'Tempestade de Raios',
		'mapa.emergencia.codar_5' => 'Granizo',
		'mapa.emergencia.codar_6' => 'Chuvas Intensas',
		'mapa.emergencia.codar_7' => 'Vendaval',
		'mapa.emergencia.codar_8' => 'Onda de Calor',
		'mapa.emergencia.codar_9' => 'Friagem',
		'mapa.emergencia.codar_10' => 'Geada',
		'mapa.emergencia.codar_11' => 'Estiagem',
		'mapa.emergencia.codar_12' => 'Seca',
		'mapa.emergencia.codar_13' => 'Incêndios',
		'mapa.emergencia.codar_14' => 'Baixa Umidade',
		'mapa.emergencia.codar_15' => 'Declínio de Temperatura',
		'mapa.emergencia.codar_16' => 'Neve',
		'mapa.emergencia.codar_17' => 'Acumulado de Chuva',				
	
		/* model acao */
		'acao.titulo' => 'Ação',
		'acao.descricao' => 'Descrição',
		'acao.data' => 'Data',
		'acao.responsavel' => 'Responsavel',
		'acao.tipoacao' => 'Tipo da Ação',
		'acao.classe' => 'Classe',
		'acao.acoes' => 'Ações' 
];