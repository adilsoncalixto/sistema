@extends('home')

@section('files_css_page')
<link href="/assets/plugins/bootstrap-modal/css/bootstrap-modal-bs3patch.css" rel="stylesheet" type="text/css"/>
<link href="/assets/plugins/bootstrap-modal/css/bootstrap-modal.css" rel="stylesheet" type="text/css"/>
<link rel="stylesheet" href="/assets/plugins/select2/select2.css">

<link href="/assets/plugins/jquery-ui/jquery-ui-1.10.1.custom.min.css" rel="stylesheet"/>

@stop

@section('link_01') <a href="/pmo">Pmo</a>  @stop
@section('link_02') <a href="/comercial">Comercial</a> @stop
@section('link_03') Contrato @stop

@section('title_more') Comercial @stop
@section('title_less') Contrato @stop

@section('content_body')

@include('includes.modal.modal_contract')
@include('flash::message')

<div id="mensage_parametro"></div>

<div class="row">
    <div class="col-xs-12">

        <div class="panel panel-default">
            <div class="panel-body">
                {!! Form::open(array('url' => 'comercial/contrato', 'class' => 'form-inline', 'method'=>'get', 'id' => 'form-filtros')) !!}
                    
                    <div class="col-xs-4">
                        {!! Form::select('cliente_busca', $clientes, $cliente_busca, array('id' => 'cliente_busca','class' => 'form-control search-select')) !!}
                    </div>
                    
                    <div class="col-xs-3">
                        {!! Form::select('tipo_busca', $tiposContrato, $tipo_busca, array('id' => 'tipo_busca','class' => 'form-control search-select')) !!}
                    </div>

                    <div class="col-xs-4">
                        {!! Form::select('status_busca', $statusContrato, $status_busca, array('id' => 'status_busca','class' => 'form-control search-select')) !!}
                    </div>

                    <div class="col-xs-1" style="text-align: center;">
                        {!! Form::button('<i class="fa fa-filter"></i> Buscar', array('class' => 'btn btn-sm btn-primary', 'type'=>'submit')) !!}
                    </div>
                {!! Form::close() !!}
            </div>
        </div>

        <div class="panel panel-default">
            <div class="panel-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                        <div align="right">
                            <div class="btn-group btn-group-xs">
                                <a data-toggle="modal" id="novo" role="button" class="btn btn-xs btn-primary">
                                <i class="fa fa-plus"></i> Adicionar novo</a>
                            </div>
                        </div>
                        <tr>
                            <th>Identificação</th>
                            <th>Cliente</th>
                            <th>Tipo de contrato </th>
                            <th>Status</th>
                            <th>Liberar Projeto</th>
                            <th width="130" style="text-align: center;">Ações</th>
                        </tr>
                        </thead>
                        <tbody>
                            @foreach ($contratos as $key => $c)
                            <tr>
                                <td>{!!$c->identification!!}</td>
                                <td>{!!$c->cliente->name!!}</td>
                                <td>{!!$c->tipocontrato->name!!}</td>
                                <td>{!!$c->statuscontrato->name!!}</td>
                                <td>@if($c->project_released == 'S') Sim @else Não @endif</td>
                                <td>
                                    <div class="visible-md visible-lg hidden-sm hidden-xs">
                                        <a href="/comercial/contrato/cr/{!!$c->id!!}" role="button" class="btn btn-xs btn-primary tooltips" data-placement="top" data-original-title="Aditivo"><i class="clip-stack-2"></i></a>
                                        <a onclick="MontarModalEditar({!!$c->id!!})" data-toggle="modal" role="button" class="btn btn-xs btn-teal tooltips" data-placement="top" data-original-title="Editar"><i class="fa fa-edit"></i></a>
                                        <a onclick="MontarModalRemover({!!$c->id!!})" data-toggle="modal" role="button" class="btn btn-xs btn-bricky tooltips" data-placement="top" data-original-title="Remover"><i class="fa fa-times fa fa-white"></i></a>
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

@stop

@section('files_script_page')
<script src="/assets/js/rosie/comercial/comercial_contract.js"></script>
<script src="/assets/plugins/bootstrap-modal/js/bootstrap-modal.js"></script>
<script src="/assets/plugins/bootstrap-modal/js/bootstrap-modalmanager.js"></script>

<script src="/assets/plugins/select2/select2.min.js"></script>
@stop

@section('inline_script_page')
    $('#cliente').select2({ width: '100%'});
    $('#projeto').select2({ width: '100%'});
    $('#tipo_contrato').select2({ width: '100%'});
    $('#status_contrato').select2({ width: '100%'});

    $('#cliente_editar').select2({ width: '100%'});
    $('#projeto_editar').select2({ width: '100%'});
    $('#tipo_contrato_editar').select2({ width: '100%'});
    $('#status_contrato_editar').select2({ width: '100%'});

    $('#cliente_busca').select2({ width: '100%'});
    $('#tipo_busca').select2({ width: '100%'});
    $('#status_busca').select2({ width: '100%'});

@stop