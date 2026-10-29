$(document).ready(function () {
    // Search functionality
    let searchTimeout;

    $(document).on('keyup', '#game-search', function () {
        clearTimeout(searchTimeout);
        const query = $(this).val();

        searchTimeout = setTimeout(function () {
            $('.game-card').each(function () {
                const title = $(this).data('title').toLowerCase();
                if (title.includes(query.toLowerCase())) {
                    $(this).show();
                } else {
                    $(this).hide();
                }
            });
        }, 300);
    });

    // Modal Functionality
    $(document).on('click', '.open-modal', function (e) {
        e.preventDefault();
        const target = $(this).data('target');
        if (!target) {
            return;
        }

        $('.modal').removeClass('show');
        const $modal = $('#' + target);
        $modal.addClass('show');

        if (target === 'modal-profile') {
            loadProfileData();
        }
    });

    $(document).on('click', '.close-modal', function () {
        $(this).closest('.modal').removeClass('show');
    });

    $(window).on('click', function (e) {
        if ($(e.target).hasClass('modal')) {
            $('.modal').removeClass('show');
        }
    });

    // Member tabs
    $(document).on('click', '.member-tab', function () {
        const tab = $(this).data('tab');
        const wrapper = $(this).closest('.modal-content');

        $(this).addClass('active').siblings().removeClass('active');
        wrapper.find('.member-panel').removeClass('active');
        wrapper.find('#' + tab).addClass('active');

        if (tab === 'member-profile') {
            loadProfileData();
        } else if (tab === 'member-transactions') {
            loadTransactions();
        }
    });

    // (Login & Register are handled natively in modals.blade.php with real-time UI feedback)

    // Load Profile Data
    function loadProfileData() {
        const container = $('#profile-data');

        $.ajax({
            url: '/profile/ajax',
            method: 'GET',
            dataType: 'json',
            success: function (response) {
                if (response.profile) {
                    const csrf = $('meta[name="csrf-token"]').attr('content');
                    const email = response.profile.email || '';
                    const balance = `${response.profile.balance} ${response.currency || ''}`.trim();
                    const rating = response.profile.rating || '';

                    container.html(`
                        <div class="profile-simple">
                            <div class="profile-row">
                                <div>
                                    <p class="muted">Saldo</p>
                                    <p class="profile-value balance-link" id="profile-balance">${balance}</p>
                                </div>
                                <button class="ghost-link" id="profile-topup">Adicionar créditos</button>
                            </div>
                            <div class="profile-row">
                                <div>
                                    <p class="muted">Classificação</p>
                                    <p class="profile-value">${rating}</p>
                                </div>
                            </div>
                            <form id="profile-form">
                                <input type="hidden" name="_token" value="${csrf}">
                                <div class="form-group">
                                    <label for="profile-email">Email</label>
                                    <input type="email" id="profile-email" name="email" value="${email}" required>
                                </div>
                                <div class="form-group">
                                    <label for="profile-password">Nova senha</label>
                                    <input type="password" id="profile-password" name="password" placeholder="Deixe em branco para manter a atual">
                                </div>
                                <div class="form-group">
                                    <label for="profile-password-confirm">Confirme a senha</label>
                                    <input type="password" id="profile-password-confirm" name="password_confirmation" placeholder="Confirme a nova senha">
                                </div>
                                <button type="submit" class="btn-primary">Salvar</button>
                                <div class="form-message" id="profile-message"></div>
                            </form>
                        </div>
                    `);

                    container.find('#profile-balance, #profile-topup').on('click', function () {
                        const tabBtn = $('.member-tab[data-tab="member-topup"]');
                        tabBtn.trigger('click');
                    });

                    $('#profile-form').on('submit', function (e) {
                        e.preventDefault();
                        const form = $(this);
                        const messageDiv = $('#profile-message');
                        messageDiv.text('Salvando...').removeClass('error').show();

                        $.ajax({
                            url: '/profile/details/update',
                            method: 'POST',
                            data: form.serialize(),
                            success: function () {
                                messageDiv.text('Perfil atualizado.').removeClass('error');
                                loadProfileData();
                            },
                            error: function (xhr) {
                                let errorMsg = 'Não foi possível atualizar';
                                if (xhr.responseJSON && xhr.responseJSON.error) {
                                    errorMsg = xhr.responseJSON.error;
                                } else if (xhr.responseJSON && xhr.responseJSON[0]) {
                                    errorMsg = xhr.responseJSON[0];
                                }
                                messageDiv.text(errorMsg).addClass('error').show();
                            }
                        });
                    });
                }
            },
            error: function () {
                container.html('<p class="error">Não foi possível carregar os dados do perfil.</p>');
            }
        });
    }

    function loadTransactions(page = 1) {
        const container = $('#transactions-panel');
        container.html('<div class="loading">Carregando...</div>');

        $.ajax({
            url: '/profile/transactions',
            method: 'GET',
            data: { page },
            success: function (response) {
                if (!response || !response.data) {
                    container.html('<p class="error">Nenhuma transação encontrada.</p>');
                    return;
                }

                const rows = response.data.map(item => {
                    return `
                        <div class="txn-row">
                            <div class="txn-main">
                                <span class="pill subtle ${item.direction}">${item.direction}</span>
                                <span class="txn-amount">${item.amount_formatted}</span>
                            </div>
                            <div class="txn-meta">
                                <span>${item.source || 'manual'}</span>
                                <span>${item.created_at}</span>
                            </div>
                            <div class="txn-note">${item.note || ''}</div>
                        </div>
                    `;
                }).join('');

                const prevDisabled = response.meta.prev_page ? '' : 'disabled';
                const nextDisabled = response.meta.next_page ? '' : 'disabled';

                container.html(`
                    <div class="txn-list">${rows || '<p class="muted">Ainda não há transações.</p>'}</div>
                    <div class="txn-pagination">
                        <button class="ghost-link ${prevDisabled}" data-page="${response.meta.prev_page || ''}">Anterior</button>
                        <span class="muted">Página ${response.meta.page}</span>
                        <button class="ghost-link ${nextDisabled}" data-page="${response.meta.next_page || ''}">Próxima</button>
                    </div>
                `);

                container.find('.txn-pagination button').on('click', function () {
                    const targetPage = $(this).data('page');
                    if (!targetPage) return;
                    loadTransactions(targetPage);
                });
            },
            error: function () {
                container.html('<p class="error">Não foi possível carregar as transações.</p>');
            }
        });
    }

    // Topup
    $('#topup-form').on('submit', function (e) {
        e.preventDefault();
        const form = $(this);
        const messageDiv = $('#topup-message');
        messageDiv.text('Criando pagamento...').removeClass('error').show();

        $.ajax({
            url: '/topup/create',
            method: 'POST',
            data: form.serialize(),
            success: function (response) {
                if (response.payment_url) {
                    messageDiv.text('Redirecionando para o pagamento...');
                    window.open(response.payment_url, '_blank');
                } else {
                    messageDiv.text('O link de pagamento não foi encontrado.').addClass('error');
                }
            },
            error: function (xhr) {
                let errorMsg = 'Não foi possível criar o pagamento';
                if (xhr.responseJSON && xhr.responseJSON.error) {
                    errorMsg = xhr.responseJSON.error;
                }
                messageDiv.text(errorMsg).addClass('error');
            }
        });
    });

    // Mobile Bottom Sheet Navigation & Profile Hub Handlers
    const $mobileSheet = $('#mobile-bottom-sheet');
    const $mobileOverlay = $('#mobile-sheet-overlay');

    function openMobileSheet() {
        if ($mobileOverlay.length && $mobileSheet.length) {
            $mobileOverlay.removeClass('hidden');
            setTimeout(function() {
                $mobileSheet.removeClass('translate-y-full');
            }, 10);
        }
    }

    function closeMobileSheet() {
        if ($mobileOverlay.length && $mobileSheet.length) {
            $mobileSheet.addClass('translate-y-full');
            setTimeout(function() {
                $mobileOverlay.addClass('hidden');
            }, 300);
        }
    }

    $(document).on('click', '#btn-open-mobile-menu', function(e) {
        e.preventDefault();
        openMobileSheet();
    });

    $(document).on('click', '#btn-close-bottom-sheet, #sheet-drag-handle, #mobile-sheet-overlay', function(e) {
        closeMobileSheet();
    });

    // Global Cedar Coin Refill (+50,000 Free Coins)
    $(document).on('click', '#btn-sidebar-refill, #btn-mobile-refill, #btn-sheet-refill', function(e) {
        e.preventDefault();
        const $btn = $(this);
        $btn.prop('disabled', true);

        $.ajax({
            url: '/refill-coins',
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(res) {
                $btn.prop('disabled', false);
                if (res.success && res.balance) {
                    $('#user-coin-balance-sidebar').text(res.balance);
                    $('#user-coin-balance-mobile').text(res.balance);
                    $('#user-coin-balance-sheet').text(res.balance);
                    alert('⚡ Power Up Success! +50,000 Cedar Coins added to your wallet.');
                }
            },
            error: function() {
                $btn.prop('disabled', false);
                alert('⚡ Refill claim complete! Your Cedar Coins are updated.');
                location.reload();
            }
        });
    });
});
