<?php

return [
    'not_save'          => 'Não foi possível salvar. Tente novamente.',
    'not_delete'        => 'Não foi possível deletar. Tente novamente',
    'ajax_title'        => 'Tem certeza?',
    'not_found'         => 'Nenhum registro encontrado',

    'categories' => [
        'select_type'       => 'Selecione um tipo de categoria',
        'not_found'         => 'Nenhuma Categoria Encontrada',
        'create'            => 'Categoria adicionada com sucesso.',
        'update'            => 'Categoria atualizada com sucesso',
        'delete'            => 'Categoria deletada com sucesso',
        'ajax_text'         => 'A categoria sera excluída permanentemente',
    ],

    'accounts' => [
        'not_found'         => 'Nenhuma Conta Encontrada',
        'create'            => 'Conta adicionada com sucesso.',
        'update'            => 'Conta atualizada com sucesso',
        'delete'            => 'Conta deletada com sucesso',
        'ajax_text'         => 'A conta será excluída permanentemente',
    ],

    'cards' => [
        'not_found'         => 'Nenhum Cartão de Crédito encontrado',
        'create'            => 'Cartão de Crédito adicionado com sucesso.',
        'update'            => 'Cartão de Crédito atualizado com sucesso',
        'delete'            => 'Cartão de Crédito deletado com sucesso',
        'ajax_text'         => 'O Cartão de Crédito será excluído permanentemente',
    ],

    'invoices' => [
        'not_found'         => 'Nenhuma Fatura encontrada',
        'create'            => 'Fatura adicionado com sucesso.',
        'update'            => 'Fatura atualizado com sucesso',
        'delete'            => 'Fatura deletado com sucesso',
        'ajax_text'         => 'A fatura será excluída permanentemente',
    ],

    'entries' => [
        'not_found'             => 'Nenhum lançamento encontrado',
        'create'                => 'Lançamento adicionado com sucesso.',
        'update'                => 'Lançamento atualizado com sucesso',
        'delete'                => 'Lançamento deletado com sucesso',
        'ajax_text'             => 'O Lançamento será excluído permanentemente',
        'no_open_invoice'       => 'Não existem faturas abertas para esta data',
        'insufficient_limit'    => 'O limite do cartão é insuficiente'
    ],

    'account_scheduling' => [
        'not_found'                 => 'Nenhuma conta encontrada',
        'payable_created'           => 'Contas a pagar adicionado com sucesso.',
        'receivable_created'        => 'Contas a receber adicionado com sucesso.',
        'payable_updated'           => 'Contas a pagar atualizado com sucesso',
        'receivable_updated'        => 'Contas a receber atualizado com sucesso',
        'payable_deleted'           => 'Contas a pagar deletado com sucesso',
        'receivable_deleted'        => 'Contas a receber deletado com sucesso',
        'ajax_text'                 => 'Ao cancelar o pagamento, o lançamento que foi gerado na conta será excluído permanentemente',
        'payable_is_paid'           => 'Conta a pagar já está paga',
        'receivable_is_paid'        => 'Conta a receber já está paga',
        'payable_paid'              => 'Conta paga com sucesso',
        'receivable_paid'           => 'Conta recebida com sucesso',
        'payable_cancel'            => 'Pagamento cancelado com sucesso',
        'receivable_cancel'         => 'Recebimento cancelado com sucesso',
        'not_cancel_payment'        => 'Não foi possível cancelar o pagamento',
        'not_cancel_receivement'    => 'Não foi possível cancelar o recebimento',
    ]
];
