<div class="modal fade" id="ModalNovoContrato" tabindex="-1" data-width="750" style="display: none;">
  	<form action="/comercial/contrato/store" id="novo_contrato" method="post" accept-charset="UTF-8" class="smart-wizard form-horizontal" role="form">				
		<input type="hidden" name="_token" value="{!! csrf_token() !!}" />
		<div class="modal-header">
			<button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
				<h4 class="modal-title">Contrato</h4>
		</div>
		<div class="modal-body">
			<div class="panel panel-default">
				<div class="panel-body">
					
					<br/>

					<div class="form-group">
						<div class="col-md-3" style="top:7px;">Cliente</div>
						<div class="col-md-9" id="for_cliente">
							{!! Form::select('cliente', $clientes, 0, array('id' => 'cliente','class' => 'form-control search-select')) !!}
							<div id="for_cliente_message"></div>
						</div>
					</div>

					<div class="form-group">
						<div class="col-md-3" style="top:15px;">Identificação </div>
						<div class="col-md-9" id="for_identificacao">
							<input type="text" id="identificacao" name="identificacao" class="form-control caixa_alta">
							<div id="for_identificacao_message"></div>
						</div>
					</div>

					<div class="form-group">
						<div class="col-md-3" style="top:15px;">Tipo de contrato</div>
						<div class="col-md-9" id="for_tipo_contrato">
							{!! Form::select('tipo_contrato', $tiposContrato, 0, array('id' => 'tipo_contrato','class' => 'form-control search-select')) !!}
							<div id="for_tipo_contrato_message"></div>
						</div>
					</div>
					
					<div class="form-group">
						<div class="col-md-3" style="top:15px;">Status</div>
						<div class="col-md-9" id="for_status_contrato">
							{!! Form::select('status_contrato', $statusContrato, 0, array('id' => 'status_contrato','class' => 'form-control search-select')) !!}
							<div id="for_status_contrato_message"></div>
						</div>
					</div>

					<div class="form-group">
						<div class="col-md-3" style="top:15px;">Vigência</div>
						<div class="col-md-9 calendario" id="for_vigencia">
							<div class="col-md-8 input-group date" style="float:left">
								<input type="text" id="vigencia" name="vigencia" class="form-control data-inicio-modal-usuario" value="">
								<span class="input-group-addon "> <i class="fa fa-calendar"></i> </span>
							</div>
							<div class="col-md-4" style="float:right; top:8px;">
								<input type="checkbox" id="vigencia_check" name="vigencia_check" value="i"> Indeterminado
							</div>
						</div>
						<div class="col-md-3"></div>
						<div class="col-md-9" id="for_vigencia_message"></div>
					</div>

					<div class="form-group">
						<div class="col-md-3" style="top:15px;">Critérios de reajuste</div>
						<div class="col-md-9" id="for_identificacao">
							<input type="text" id="criterios_reajuste" name="criterios_reajuste" class="form-control">
						</div>
					</div>

					<div class="form-group">
						<div class="col-md-3" style="top:15px;">Multas aplicáveis</div>
						<div class="col-md-9" id="for_identificacao">
							<input type="text" id="multas_aplicaveis" name="multas_aplicaveis" class="form-control">
						</div>
					</div>
					
					<div class="form-group">
						<div class="col-md-3" style="top:15px;">Liberar projeto para faturamento</div>
						<div class="col-md-9" id="for_liberar_faturamento">
							<label class="checkbox-inline">
								<input type="checkbox" id="liberar_faturamento" name="liberar_faturamento" value="S">Sim
							</label>
						</div>
					</div>

					<br/>

				</div>
			</div>
		</div>
	<div class="modal-footer">
		<button type="button" data-dismiss="modal" class="btn btn-default">Cancelar	</button>
		<button type="button" id="salvar_dados" class="btn btn-primary">Salvar</button>
	</div>
  </form>	
</div>

<div class="modal fade" id="ModalEditarContrato" tabindex="-1" data-width="750" style="display: none;">
  	<form action="/comercial/contrato/update" id="editar_contrato" method="post" accept-charset="UTF-8" class="smart-wizard form-horizontal" role="form">				
		<input type="hidden" name="_token" value="{!! csrf_token() !!}" />
		<input type="hidden" name="id_contrato_editar" id="id_contrato_editar" value="" />
		<div class="modal-header">
			<button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
				<h4 class="modal-title">Editar Contrato</h4>
		</div>
		<div class="modal-body">
			<div class="panel panel-default">
				<div class="panel-body">
					
					<br/>

					<div class="form-group">
						<div class="col-md-3" style="top:7px;">Cliente</div>
						<div class="col-md-9" id="for_cliente_editar">
							{!! Form::select('cliente_editar', $clientes, 0, array('id' => 'cliente_editar','class' => 'form-control search-select')) !!}
							<div id="for_cliente_message_editar"></div>
						</div>
					</div>

					<div class="form-group">
						<div class="col-md-3" style="top:15px;">Identificação </div>
						<div class="col-md-9" id="for_identificacao_editar">
							<input type="text" id="identificacao_editar" name="identificacao_editar" class="form-control caixa_alta">
							<div id="for_identificacao_message_editar"></div>
						</div>
					</div>

					<div class="form-group">
						<div class="col-md-3" style="top:15px;">Tipo de contrato</div>
						<div class="col-md-9" id="for_tipo_contrato_editar">
							{!! Form::select('tipo_contrato_editar', $tiposContrato, 0, array('id' => 'tipo_contrato_editar','class' => 'form-control search-select')) !!}
							<div id="for_tipo_contrato_message_editar"></div>
						</div>
					</div>
					
					<div class="form-group">
						<div class="col-md-3" style="top:15px;">Status</div>
						<div class="col-md-9" id="for_status_contrato_editar">
							{!! Form::select('status_contrato_editar', $statusContrato, 0, array('id' => 'status_contrato_editar','class' => 'form-control search-select')) !!}
							<div id="for_status_contrato_message_editar"></div>
						</div>
					</div>

					<div class="form-group">
						<div class="col-md-3" style="top:15px;">Vigência</div>
						<div class="col-md-9 calendario" id="for_vigencia_editar">
							<div class="col-md-8 input-group date" style="float:left">
								<input type="text" id="vigencia_editar" name="vigencia_editar" class="form-control data-inicio-modal-usuario" value="">
								<span class="input-group-addon "> <i class="fa fa-calendar"></i> </span>
							</div>
							<div class="col-md-4" style="float:right; top:8px;">
								<input type="checkbox" id="vigencia_check_editar" name="vigencia_check_editar" value="i"> Indeterminado
							</div>
						</div>
						<div class="col-md-3"></div>
						<div class="col-md-9" id="for_vigencia_message_editar"></div>
					</div>

					<div class="form-group">
						<div class="col-md-3" style="top:15px;">Critérios de reajuste</div>
						<div class="col-md-9" id="for_identificacao_editar">
							<input type="text" id="criterios_reajuste_editar" name="criterios_reajuste_editar" class="form-control">
						</div>
					</div>

					<div class="form-group">
						<div class="col-md-3" style="top:15px;">Multas aplicáveis</div>
						<div class="col-md-9" id="for_identificacao_editar">
							<input type="text" id="multas_aplicaveis_editar" name="multas_aplicaveis_editar" class="form-control">
						</div>
					</div>
					
					<div class="form-group">
						<div class="col-md-3" style="top:15px;">Liberar projeto para faturamento</div>
						<div class="col-md-9" id="for_liberar_faturamento_editar">
							<label class="checkbox-inline">
								<input type="checkbox" id="liberar_faturamento_editar" name="liberar_faturamento_editar" value="S">Sim
							</label>
						</div>
					</div>

					<br/>

				</div>
			</div>
		</div>
	<div class="modal-footer">
		<button type="button" data-dismiss="modal" class="btn btn-default">Cancelar	</button>
		<button type="button" id="salvar_dados_editar" class="btn btn-primary">Atualizar</button>
	</div>
  </form>	
</div>

<div class="modal fade" id="ModalRemoverContrato" tabindex="-1" data-width="750" style="display: none;">
  	<form action="/comercial/contrato/destroy" id="remover_contrato" method="post" accept-charset="UTF-8" class="smart-wizard form-horizontal" role="form">				
		<input type="hidden" name="_token" value="{!! csrf_token() !!}" />
		<input type="hidden" name="id_contrato_remover" id="id_contrato_remover" value="" />
		<div class="modal-header">
			<button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
				<h4 class="modal-title">Remover Contrato</h4>
		</div>
		<div class="modal-body">
			<div class="panel panel-default">
				<div class="panel-body">
					
					<br/>

					<div class="form-group">
						<div class="col-md-3" style="top:7px;">Cliente</div>
						<div class="col-md-9" id="for_cliente_remover">
							{!! Form::select(null, $clientes, 0, array('id' => 'cliente_remover','class' => 'form-control search-select', 'disabled' => 'disabled')) !!}
							<div id="for_cliente_message_remover"></div>
						</div>
					</div>

					<div class="form-group">
						<div class="col-md-3" style="top:15px;">Identificação </div>
						<div class="col-md-9" id="for_identificacao_remover">
							<input type="text" id="identificacao_remover" class="form-control" disabled="disabled">
							<div id="for_identificacao_message_remover"></div>
						</div>
					</div>

					<div class="form-group">
						<div class="col-md-3" style="top:15px;">Tipo de contrato</div>
						<div class="col-md-9" id="for_tipo_contrato_remover">
							{!! Form::select(null, $tiposContrato, 0, array('id' => 'tipo_contrato_remover','class' => 'form-control search-select', 'disabled' => 'disabled')) !!}
							<div id="for_tipo_contrato_message_remover"></div>
						</div>
					</div>
					
					<div class="form-group">
						<div class="col-md-3" style="top:15px;">Status</div>
						<div class="col-md-9" id="for_status_contrato_remover">
							{!! Form::select(null, $statusContrato, 0, array('id' => 'status_contrato_remover','class' => 'form-control search-select', 'disabled' => 'disabled')) !!}
							<div id="for_status_contrato_message_remover"></div>
						</div>
					</div>

					<div class="form-group">
						<div class="col-md-3" style="top:15px;">Vigência</div>
						<div class="col-md-9 calendario" id="for_vigencia_remover">
							<div class="col-md-8 input-group date" style="float:left">
								<input type="text" id="vigencia_remover" class="form-control" value="" disabled="disabled">
								<span class="input-group-addon "> <i class="fa fa-calendar"></i> </span>
							</div>
							<div class="col-md-4" style="float:right; top:8px;">
								<input type="checkbox" id="vigencia_check_remover" disabled="disabled" value="i"> Indeterminado
							</div>
						</div>
						<div class="col-md-3"></div>
						<div class="col-md-9" id="for_vigencia_message_remover"></div>
					</div>

					<div class="form-group">
						<div class="col-md-3" style="top:15px;">Critérios de reajuste</div>
						<div class="col-md-9" id="for_identificacao_remover">
							<input type="text" id="criterios_reajuste_remover" class="form-control" disabled="disabled">
						</div>
					</div>

					<div class="form-group">
						<div class="col-md-3" style="top:15px;">Multas aplicáveis</div>
						<div class="col-md-9" id="for_identificacao_remover">
							<input type="text" id="multas_aplicaveis_remover" class="form-control" disabled="disabled">
						</div>
					</div>
					
					<div class="form-group">
						<div class="col-md-3" style="top:15px;">Liberar projeto para faturamento</div>
						<div class="col-md-9" id="for_liberar_faturamento_remover">
							<label class="checkbox-inline">
								<input type="checkbox" id="liberar_faturamento_remover" disabled="disabled" value="S">Sim
							</label>
						</div>
					</div>

					<br/>

				</div>
			</div>
		</div>
	<div class="modal-footer">
		<button type="button" data-dismiss="modal" class="btn btn-default">Cancelar	</button>
		<button type="button" id="remover_dados" class="btn btn-primary">Remover</button>
	</div>
  </form>	
</div>