<?php

return [
    'not_save'              => 'Não foi possível salvar. Tente novamente.',
    'not_delete'            => 'Não foi possível deletar. Tente novamente',
    'ajax_title'            => 'Tem certeza?',
    'not_found'             => 'Nenhum registro encontrado',

    'categories' => [
        'select_type'       => 'Selecione um tipo de categoria',
        'not_found'         => 'Nenhuma Categoria Encontrada',
        'create'            => 'Categoria adicionada com sucesso.',
        'update'            => 'Categoria atualizada com sucesso',
        'delete'            => 'Categoria deletada com sucesso',
        'ajax_text'         => 'A categoria sera excluída permanentemente',
        'invalid_type'      => 'O tipo de categoria é inválido',
        'api_not_found'     => 'Categoria não encontrada',
        'not_delete'        => 'A categoria está associada com lançamentos, não é possível excluir'
    ],

    'accounts' => [
        'not_found'             => 'Nenhuma Conta Encontrada',
        'create'                => 'Conta adicionada com sucesso.',
        'update'                => 'Conta atualizada com sucesso',
        'delete'                => 'Conta deletada com sucesso',
        'ajax_text'             => 'A conta será excluída permanentemente',
        'equal_accounts'        => 'As contas de origem e destino não podem ser iguais',
        'transfer_completed'    => 'Transferência completada com sucesso',
        'api_not_found'         => 'Conta não encontrada',
        'not_delete'            => 'A conta possui lançamentos, não é possível excluir'
    ],

    'cards' => [
        'not_found'         => 'Nenhum Cartão de Crédito encontrado',
        'create'            => 'Cartão de Crédito adicionado com sucesso.',
        'update'            => 'Cartão de Crédito atualizado com sucesso',
        'delete'            => 'Cartão de Crédito deletado com sucesso',
        'ajax_text'         => 'O Cartão de Crédito será excluído permanentemente',
        'api_not_found'     => 'Cartão não encontrado',
        'not_delete'        => 'O cartão possui faturas, não é possível excluir'
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
        'ajax_text'                 => 'Ao confirmar, o lançamento que foi gerado na conta será excluído permanentemente',
        'payable_is_paid'           => 'Conta a pagar já está paga',
        'receivable_is_paid'        => 'Conta a receber já está paga',
        'payable_paid'              => 'Conta paga com sucesso',
        'receivable_paid'           => 'Conta recebida com sucesso',
        'payable_cancel'            => 'Pagamento cancelado com sucesso',
        'receivable_cancel'         => 'Recebimento cancelado com sucesso',
        'not_cancel_payment'        => 'Não foi possível cancelar o pagamento',
        'not_cancel_receivement'    => 'Não foi possível cancelar o recebimento',
        'installments_created'      => 'Parcelas criadas com sucesso',
        'api_not_found'             => 'Conta não encontrada',
        'delete_payable_paid'       => 'Não é possível excluir uma conta paga. Cancele o pagamento',
        'payable_is_not_paid'       => 'Conta não está paga'
    ],

    'profile' => [
        'incorrect_password'    => 'Sua senha atual está incorreta',
        'same_password'         => 'A senha atual e a nova senha não podem ser iguais',
        'password_updated'      => 'Senha alterada com sucesso',
        'profile_updated'       => 'Perfil atualizado com sucesso',
        'invalid_user'          => 'Usuário Inválido',
        'avatar_updated'        => 'Avatar atualizado com sucesso',
    ],

    'mail' => [
        'payable_due_in'    => "Contas a pagar com vencimento em ",
        'receivable_due_in' => "Contas a receber com vencimento em ",
        'footer_mail'       => 'Se você estiver tendo problemas para clicar no botão ":actionText", copie e cole o endereço abaixo em seu navegador da web:'
    ]
];
