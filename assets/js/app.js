/**
 * Petit Pet — interações leves e progressivas.
 */

(function () {
    'use strict';

    // Confirmação, envio e proteção contra clique duplo em um único fluxo.
    document.addEventListener('submit', function (evento) {
        var formulario = evento.target.closest('form');
        if (!formulario) {
            return;
        }

        if (formulario.hasAttribute('data-confirmar')) {
            var mensagem = formulario.getAttribute('data-confirmar') || 'Tem certeza que deseja continuar?';
            if (!window.confirm(mensagem)) {
                evento.preventDefault();
                return;
            }
        }

        var botao = formulario.querySelector('button[type="submit"]');
        if (botao && !botao.disabled) {
            botao.disabled = true;
            botao.dataset.textoOriginal = botao.textContent;
            botao.textContent = 'Enviando...';
        }
    });

    // Fecha mensagens de feedback manualmente.
    document.querySelectorAll('[data-fechar-mensagem]').forEach(function (botao) {
        botao.addEventListener('click', function () {
            var mensagem = botao.closest('.mensagem');
            if (mensagem) {
                mensagem.remove();
            }
        });
    });

    // Esconde mensagens de sucesso depois de alguns segundos.
    document.querySelectorAll('.mensagem-sucesso').forEach(function (mensagem) {
        window.setTimeout(function () {
            mensagem.style.transition = 'opacity 300ms ease, transform 300ms ease';
            mensagem.style.opacity = '0';
            mensagem.style.transform = 'translateY(-6px)';
            window.setTimeout(function () {
                mensagem.remove();
            }, 320);
        }, 7000);
    });

    // Envia o formulário automaticamente ao trocar um filtro.
    document.querySelectorAll('[data-auto-enviar]').forEach(function (campo) {
        campo.addEventListener('change', function () {
            if (campo.form) {
                campo.form.requestSubmit ? campo.form.requestSubmit() : campo.form.submit();
            }
        });
    });
})();
